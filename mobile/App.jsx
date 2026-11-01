import React, { useEffect, useRef, useState } from 'react';
import { View, StyleSheet, StatusBar } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import {
  registerForPushNotificationsAsync,
  addLoginApprovalNotificationListener,
  addForegroundNotificationListener,
} from './src/lib/notifications';

import SplashScreen     from './components/SplashScreen';
import OnboardingScreen from './components/OnboardingScreen';
import DashboardScreen  from './components/DashboardScreen';
import API, { clearStoredSession, getStoredToken } from './src/api/client';

/**
 * App Navigation Flow:
 *   1. SplashScreen     – always shown for 5 s on launch
 *   2. OnboardingScreen – shown ONCE (first launch only)
 *   3. DashboardScreen  – main tabs, works in guest mode
 *                         Login is accessed from:
 *                           • Settings tab  (Sign In button when guest)
 *                           • Floating LoginModal on Integration / Alerts tabs
 */
export default function App() {
  // 'splash' | 'onboarding' | 'main'
  const [phase, setPhase] = useState('splash');
  const [user,  setUser]  = useState(null);
  const [activeTab, setActiveTab] = useState('dashboard');

  // Stores the event_id from a push notification tap so DashboardScreen
  // can surface the correct approval popup immediately.
  const [pushEventId, setPushEventId] = useState(null);

  // Refs to hold listener cleanup functions so we can remove them on logout.
  const tapListenerCleanupRef        = useRef(null);
  const foregroundListenerCleanupRef = useRef(null);

  // ── Attach / detach push notification listeners ───────────────────────────
  const attachNotificationListeners = () => {
    // Clean up any existing listeners first (idempotent)
    tapListenerCleanupRef.current?.();
    foregroundListenerCleanupRef.current?.();

    // 1. User TAPS a push notification (background / killed state)
    //    → navigate to Notifications tab and surface the approval popup
    tapListenerCleanupRef.current = addLoginApprovalNotificationListener((data) => {
      const eventId = data.event_id ?? data.id ?? null;
      if (eventId) {
        setPushEventId(eventId);
      }
      setActiveTab('notifications');
    });

    // 2. Push notification ARRIVES while app is foregrounded
    //    → surface the approval popup on the current tab without changing tabs
    foregroundListenerCleanupRef.current = addForegroundNotificationListener((data) => {
      const eventId = data.event_id ?? data.id ?? null;
      if (eventId) {
        setPushEventId(eventId);
      }
    });
  };

  const detachNotificationListeners = () => {
    tapListenerCleanupRef.current?.();
    foregroundListenerCleanupRef.current?.();
    tapListenerCleanupRef.current = null;
    foregroundListenerCleanupRef.current = null;
  };

  // ── After splash ─────────────────────────────────────────────────────────────
  const handleSplashFinish = async () => {
    try {
      // Restore persisted session (if any)
      const token = await getStoredToken();
      if (token) {
        try {
          const { data } = await API.get('/mobile/me');
          setUser(data);
          await AsyncStorage.setItem('user_data', JSON.stringify(data));
          // Re-attach listeners for restored sessions
          attachNotificationListeners();
        } catch (error) {
          // Only a 401 means the token itself was rejected. A network blip or a
          // 5xx (for example the API's database being briefly unreachable) must
          // not sign the user out, otherwise a short outage wipes a valid session.
          if (error?.response?.status === 401) {
            await clearStoredSession();
            await AsyncStorage.removeItem('user_data');
          } else {
            // Keep the session alive with the last known profile.
            const cached = await AsyncStorage.getItem('user_data');
            if (cached) {
              try {
                setUser(JSON.parse(cached));
                attachNotificationListeners();
              } catch (_) {}
            }
          }
        }
      } else {
        await AsyncStorage.removeItem('user_data');
      }

      // Show onboarding on first ever launch
      const onboardingDone = await AsyncStorage.getItem('onboarding_complete');
      setPhase(onboardingDone === 'true' ? 'main' : 'onboarding');
    } catch (_) {
      setPhase('main');
    }
  };

  // ── After onboarding ─────────────────────────────────────────────────────────
  const handleOnboardingFinish = () => setPhase('main');

  // ── Called by LoginModal / SettingsScreen after successful Sanctum login ──────
  const handleLoginSuccess = async (userData) => {
    setUser(userData);
    try { await AsyncStorage.setItem('user_data', JSON.stringify(userData)); } catch (_) {}

    // Register / refresh the Expo push token so the backend can reach this device
    try { await registerForPushNotificationsAsync(); } catch (_) {}

    // Wire up notification listeners now that the user is authenticated
    attachNotificationListeners();
  };

  // ── Logout ───────────────────────────────────────────────────────────────────
  const handleLogout = async () => {
    // Detach listeners before clearing state
    detachNotificationListeners();
    setPushEventId(null);

    try {
      await API.post('/mobile/logout');
    } catch (_) {}

    try {
      await clearStoredSession();
      await AsyncStorage.removeItem('user_data');
    } catch (_) {}
    setUser(null);
    setActiveTab('dashboard');
  };

  // Clean up listeners when App unmounts
  useEffect(() => {
    return () => detachNotificationListeners();
  }, []);

  return (
    <View style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#0F172A" />

      {phase === 'splash' && (
        <SplashScreen onFinish={handleSplashFinish} />
      )}

      {phase === 'onboarding' && (
        <OnboardingScreen onFinish={handleOnboardingFinish} />
      )}

      {phase === 'main' && (
        <DashboardScreen
          user={user}
          activeTab={activeTab}
          onTabChange={setActiveTab}
          onLoginSuccess={handleLoginSuccess}
          onLogout={handleLogout}
          pushEventId={pushEventId}
          onPushEventConsumed={() => setPushEventId(null)}
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#0A0F1E' },
});
