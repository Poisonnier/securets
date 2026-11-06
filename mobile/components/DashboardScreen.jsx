import React, { useEffect, useMemo, useRef, useState } from 'react';
import {
  View, Text, Image, StyleSheet, TouchableOpacity,
  ScrollView, StatusBar, Modal, Pressable,
  TouchableWithoutFeedback, ActivityIndicator, Alert,
} from 'react-native';
import { Feather } from '@expo/vector-icons';

import IntegrationScreen  from './IntegrationScreen';
import NotificationScreen from './NotificationScreen';
import SettingsScreen     from './SettingsScreen';
import LoginModal         from './LoginModal';
import { integrationApi } from '../src/api/integrations';
import { useTheme } from '../src/theme/ThemeContext';

const LOGO_ASSET = require('./new-securets-logo-dark.png');

// ─── Logo Header ────────────────────────────────────────────────────────────
function LogoHeader({ styles, onOpenNotifications, onOpenSettings }) {
  return (
    <View style={styles.logoRow}>
      <Image
        source={LOGO_ASSET}
        style={styles.headerLogo}
        resizeMode="contain"
      />
      <View style={styles.headerActions}>
        {onOpenNotifications && (
          <TouchableOpacity
            style={styles.headerIconBtn}
            onPress={onOpenNotifications}
            activeOpacity={0.75}
          >
            <Feather name="bell" size={18} color="#94A3B8" strokeWidth={1.75} />
          </TouchableOpacity>
        )}
        {onOpenSettings && (
          <TouchableOpacity
            style={styles.headerIconBtn}
            onPress={onOpenSettings}
            activeOpacity={0.75}
          >
            <Feather name="settings" size={18} color="#94A3B8" strokeWidth={1.75} />
          </TouchableOpacity>
        )}
      </View>
    </View>
  );
}

// ─── Dashboard Home (guest-friendly) ────────────────────────────────────────
function approvalMeta(event) {
  const meta = [];

  if (event?.device) {
    meta.push(event.device);
  }

  if (event?.ip_address) {
    meta.push(`IP ${event.ip_address}`);
  }

  return meta.join(' · ') || 'A protected app is waiting for your response.';
}

function DashboardHome({
  user,
  hiddenApproval,
  onRestoreApproval,
  onRespondToApproval,
  approvalDecision,
  onOpenNotifications,
  onOpenIntegrations,
  styles,
}) {
  const [recentNotifications, setRecentNotifications] = useState([]);
  const [alertsCount, setAlertsCount] = useState(0);
  const [threatsBlockedCount, setThreatsBlockedCount] = useState(0);
  const [protectionStatus, setProtectionStatus] = useState('Active');

  useEffect(() => {
    if (!user) return;
    const fetchRecent = async () => {
      try {
        const [historyRes, appsRes] = await Promise.all([
          integrationApi.history(),
          integrationApi.list(),
        ]);
        const events = historyRes.data ?? [];
        const apps   = appsRes.data ?? [];

        setRecentNotifications(events.slice(0, 3));
        setAlertsCount(events.length);
        setThreatsBlockedCount(events.filter((e) => e.status === 'declined').length);
        setProtectionStatus(apps.some((a) => a.is_active) ? 'Active' : 'Standby');
      } catch (_) {}
    };
    fetchRecent();
  }, [user]);

  return (
    <>
      <View style={styles.welcomeBanner}>
        <Text style={styles.welcomeTitle}>
          {user ? `Welcome, ${user.name || 'User'}` : 'Welcome to SecureTS'}
        </Text>
        <Text style={styles.welcomeSub}>
          {user ? 'All linked accounts are protected.' : 'Sign in to activate your ThreatShield.'}
        </Text>
      </View>
      <ScrollView
        style={styles.feed}
        contentContainerStyle={styles.feedContent}
        showsVerticalScrollIndicator={false}
      >
        {hiddenApproval && (
          <View style={styles.homeApprovalCard}>
            <View style={styles.homeApprovalHeader}>
              <Text style={styles.homeApprovalEyebrow}>Pending login request</Text>
              <TouchableOpacity
                style={styles.homeApprovalReview}
                onPress={onRestoreApproval}
                disabled={!!approvalDecision}
                activeOpacity={0.85}
              >
                <Text style={styles.homeApprovalReviewText}>Review</Text>
              </TouchableOpacity>
            </View>
            <Text style={styles.homeApprovalApp}>
              {hiddenApproval.integration?.name ?? 'Linked app'}
            </Text>
            <Text style={styles.homeApprovalMeta}>{approvalMeta(hiddenApproval)}</Text>
            <Text style={styles.homeApprovalText}>
              This request was closed from the popup. You can still approve or decline it here.
            </Text>
            <View style={styles.homeApprovalActions}>
              <TouchableOpacity
                style={[styles.homeApprovalBtn, styles.homeApprovalApprove]}
                onPress={() => onRespondToApproval('approved')}
                disabled={!!approvalDecision}
                activeOpacity={0.85}
              >
                {approvalDecision === 'approved'
                  ? <ActivityIndicator color="#FFFFFF" size="small" />
                  : <Text style={styles.homeApprovalApproveText}>Approve</Text>}
              </TouchableOpacity>
              <TouchableOpacity
                style={[styles.homeApprovalBtn, styles.homeApprovalDecline]}
                onPress={() => onRespondToApproval('declined')}
                disabled={!!approvalDecision}
                activeOpacity={0.85}
              >
                {approvalDecision === 'declined'
                  ? <ActivityIndicator color="#F87171" size="small" />
                  : <Text style={styles.homeApprovalDeclineText}>Decline</Text>}
              </TouchableOpacity>
            </View>
          </View>
        )}

        {user && (
          <>
            {/* ── Stat cards (minimalist grid) ── */}
            <View style={styles.statsGrid}>
              <View style={styles.statCard}>
                <Text style={styles.statLabel}>Protection Status</Text>
                <Text style={[styles.statValue, { color: '#00BBFF' }]}>{protectionStatus}</Text>
              </View>
              <View style={styles.statCard}>
                <Text style={styles.statLabel}>Threats Blocked</Text>
                <Text style={[styles.statValue, { color: '#F8FAFC' }]}>{threatsBlockedCount}</Text>
              </View>
              <View style={styles.statCard}>
                <Text style={styles.statLabel}>Auth Latency</Text>
                <Text style={[styles.statValue, { color: '#F8FAFC' }]}>{'<500ms'}</Text>
              </View>
              <View style={styles.statCard}>
                <Text style={styles.statLabel}>Alerts</Text>
                <Text style={[styles.statValue, { color: '#F8FAFC' }]}>{alertsCount}</Text>
              </View>
            </View>

            {/* ── All Systems Operational panel ── */}
            <View style={styles.statusPanel}>
              <View style={styles.statusIconWrap}>
                <Feather name="shield" size={18} color="#00BBFF" strokeWidth={1.75} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.statusTitle}>All Systems Operational</Text>
                <Text style={styles.statusSub}>
                  ThreatShield is actively monitoring your account. No threats detected.
                </Text>
              </View>
            </View>

            {/* ── Integration Hub card ── */}
            <TouchableOpacity
              style={styles.integrationCard}
              onPress={onOpenIntegrations}
              activeOpacity={0.85}
            >
              <View style={styles.statusIconWrap}>
                <Feather name="link" size={18} color="#00BBFF" strokeWidth={1.75} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.integrationCardTitle}>Integration Hub</Text>
                <Text style={styles.integrationCardSub}>
                  Connect external apps, manage credentials, approve or decline logins.
                </Text>
              </View>
              <Feather name="chevron-right" size={18} color="#94A3B8" strokeWidth={1.75} />
            </TouchableOpacity>

            {/* ── Recent Alerts ── */}
            <View style={styles.summarySection}>
              <View style={styles.summaryHeader}>
                <Text style={styles.summaryTitle}>Recent Alerts</Text>
                <TouchableOpacity onPress={onOpenNotifications}>
                  <Text style={styles.summaryLink}>View All</Text>
                </TouchableOpacity>
              </View>
              {recentNotifications.length > 0 ? (
                recentNotifications.map((notif) => (
                  <TouchableOpacity
                    key={notif.id}
                    style={styles.summaryItem}
                    onPress={onOpenNotifications}
                  >
                    <View style={styles.summaryIconWrap}>
                      <Feather
                        name={
                          notif.status === 'approved'
                            ? 'check-circle'
                            : notif.status === 'declined'
                            ? 'x-circle'
                            : 'bell'
                        }
                        size={16}
                        color={
                          notif.status === 'approved'
                            ? '#00BBFF'
                            : notif.status === 'declined'
                            ? '#F87171'
                            : '#94A3B8'
                        }
                        strokeWidth={1.75}
                      />
                    </View>
                    <View style={{ flex: 1 }}>
                      <Text style={styles.summaryItemTitle}>{notif.integration?.name ?? 'SecureTS Alert'}</Text>
                      <Text style={styles.summaryItemMeta}>{new Date(notif.created_at).toLocaleTimeString()}</Text>
                    </View>
                    <Feather name="chevron-right" size={16} color="#64748B" strokeWidth={1.75} />
                  </TouchableOpacity>
                ))
              ) : (
                <View style={styles.summaryEmpty}>
                  <Text style={styles.summaryEmptyText}>No recent activity found.</Text>
                </View>
              )}
            </View>
          </>
        )}

        {!user && (
          <View style={styles.emptyState}>
            <View style={styles.emptyIconBox}>
              <Feather name="shield" size={24} color="#00BBFF" strokeWidth={1.75} />
            </View>
            <Text style={styles.emptyTitle}>Secure Gateway</Text>
            <Text style={styles.emptyText}>
              All systems operational. Login events will appear here once you're connected.
            </Text>
          </View>
        )}
      </ScrollView>
    </>
  );
}

function ApprovalPopup({
  visible,
  event,
  onDismiss,
  onRespond,
  approvalDecision,
  styles,
}) {
  if (!visible || !event) return null;

  return (
    <Modal visible transparent animationType="fade" onRequestClose={onDismiss}>
      <Pressable style={styles.popupBackdrop} onPress={onDismiss}>
        <TouchableWithoutFeedback>
          <View style={styles.popupCard}>
            <Text style={styles.popupTitle}>Login request</Text>
            <Text style={styles.popupApp}>{event.integration?.name ?? 'Linked app'}</Text>
            <Text style={styles.popupMeta}>{approvalMeta(event)}</Text>
            <View style={styles.popupActions}>
              <TouchableOpacity
                style={[styles.popupApprove, approvalDecision && styles.popupActionDisabled]}
                onPress={() => onRespond('approved')}
                disabled={!!approvalDecision}
                activeOpacity={0.85}
              >
                {approvalDecision === 'approved'
                  ? <ActivityIndicator color="#4ADE80" size="small" />
                  : <Text style={styles.popupApproveText}>Approve</Text>}
              </TouchableOpacity>
              <TouchableOpacity
                style={[styles.popupDecline, approvalDecision && styles.popupActionDisabled]}
                onPress={() => onRespond('declined')}
                disabled={!!approvalDecision}
                activeOpacity={0.85}
              >
                {approvalDecision === 'declined'
                  ? <ActivityIndicator color="#F87171" size="small" />
                  : <Text style={styles.popupDeclineText}>Decline</Text>}
              </TouchableOpacity>
            </View>
          </View>
        </TouchableWithoutFeedback>
      </Pressable>
    </Modal>
  );
}

// ─── Guest Gate (shown over Integration/Alerts when not logged in) ───────────
function GuestGate({ tabName, onLoginPress, styles }) {
  return (
    <View style={styles.gateWrap}>
      <View style={styles.emptyIconBox}>
        <Feather name="lock" size={22} color="#00BBFF" strokeWidth={1.75} />
      </View>
      <Text style={styles.gateTitle}>Sign in required</Text>
      <Text style={styles.gateSub}>
        {tabName} is only available to signed-in users.
      </Text>
      <TouchableOpacity style={styles.gateBtn} onPress={onLoginPress} activeOpacity={0.85}>
        <Text style={styles.gateBtnText}>Sign In</Text>
      </TouchableOpacity>
    </View>
  );
}

// ─── Main Screen ─────────────────────────────────────────────────────────────
export default function DashboardScreen({
  user, activeTab, onTabChange, onLoginSuccess, onLogout,
  pushEventId, onPushEventConsumed,
}) {
  const theme = useTheme();
  const styles = useMemo(() => createStyles(theme), [theme]);
  const [loginModalVisible, setLoginModalVisible] = useState(false);
  const [pendingApproval, setPendingApproval] = useState(null);
  const [approvalPopupVisible, setApprovalPopupVisible] = useState(false);
  const [approvalDecision, setApprovalDecision] = useState(null);
  const pendingApprovalIdRef = useRef(null);
  const dismissedApprovalIdRef = useRef(null);

  const isLoggedIn = !!user;

  const openLogin = () => setLoginModalVisible(true);
  const closeLogin = () => setLoginModalVisible(false);

  const handleLoginSuccess = (userData) => {
    closeLogin();
    onLoginSuccess?.(userData);
  };

  useEffect(() => {
    if (!user) {
      pendingApprovalIdRef.current = null;
      dismissedApprovalIdRef.current = null;
      setPendingApproval(null);
      setApprovalPopupVisible(false);
      setApprovalDecision(null);
      return;
    }

    let cancelled = false;

    const tick = async () => {
      try {
        const response = await integrationApi.pending();
        if (cancelled) {
          return;
        }

        const nextEvent = response.data?.[0] ?? null;

        if (!nextEvent) {
          pendingApprovalIdRef.current = null;
          dismissedApprovalIdRef.current = null;
          setPendingApproval(null);
          setApprovalPopupVisible(false);
          return;
        }

        const isNewEvent = pendingApprovalIdRef.current !== nextEvent.id;

        pendingApprovalIdRef.current = nextEvent.id;
        setPendingApproval(nextEvent);

        if (isNewEvent) {
          dismissedApprovalIdRef.current = null;
          setApprovalPopupVisible(true);
          return;
        }

        if (dismissedApprovalIdRef.current !== nextEvent.id) {
          setApprovalPopupVisible(true);
        }
      } catch (_) {
        // Leave the last known state in place if polling fails.
      }
    };

    tick();
    const id = setInterval(tick, 15000);

    return () => {
      cancelled = true;
      clearInterval(id);
    };
  }, [user]);

  // ── React to a push-notification tap (background / foreground delivery) ────
  //    pushEventId is set by App.jsx's notification listeners.
  //    We look for that event in the already-polled pendingApproval first;
  //    if it's a different id we do a one-shot fetch so the popup shows the
  //    right data immediately without waiting for the next poll cycle.
  useEffect(() => {
    if (!pushEventId || !user) return;

    const surfaceEvent = async () => {
      try {
        // If the polled event already matches, just open the popup.
        if (pendingApproval && String(pendingApproval.id) === String(pushEventId)) {
          dismissedApprovalIdRef.current = null;
          setApprovalPopupVisible(true);
        } else {
          // Fetch the full pending list and find the matching event.
          const response = await integrationApi.pending();
          const matched = (response.data ?? []).find(
            (ev) => String(ev.id) === String(pushEventId),
          ) ?? response.data?.[0] ?? null;

          if (matched) {
            pendingApprovalIdRef.current  = matched.id;
            dismissedApprovalIdRef.current = null;
            setPendingApproval(matched);
            setApprovalPopupVisible(true);
          }
        }
      } catch (_) {
        // Silently ignore – the regular poll will catch it next cycle.
      } finally {
        // Always clear the consumed id so the effect doesn't re-fire.
        onPushEventConsumed?.();
      }
    };

    surfaceEvent();
  // pendingApproval intentionally excluded – we only want this to fire
  // when pushEventId changes, not on every poll cycle.
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [pushEventId, user]);

  const dismissApprovalPopup = () => {
    if (!pendingApproval) return;

    dismissedApprovalIdRef.current = pendingApproval.id;
    setApprovalPopupVisible(false);
  };

  const restoreApprovalPopup = () => {
    if (!pendingApproval) return;

    dismissedApprovalIdRef.current = null;
    setApprovalPopupVisible(true);
  };

  const respondToApproval = async (decision) => {
    if (!pendingApproval || approvalDecision) return;

    setApprovalDecision(decision);

    try {
      await integrationApi.respond(pendingApproval.id, decision);
      pendingApprovalIdRef.current = null;
      dismissedApprovalIdRef.current = null;
      setPendingApproval(null);
      setApprovalPopupVisible(false);
    } catch (_) {
      Alert.alert('Error', 'Could not submit your response.');
    } finally {
      setApprovalDecision(null);
    }
  };

  const renderContent = () => {
    switch (activeTab) {
      case 'dashboard':
        return (
          <DashboardHome
            user={user}
            hiddenApproval={pendingApproval && !approvalPopupVisible ? pendingApproval : null}
            onRestoreApproval={restoreApprovalPopup}
            onRespondToApproval={respondToApproval}
            approvalDecision={approvalDecision}
            onOpenNotifications={() => onTabChange('notifications')}
            onOpenIntegrations={() => onTabChange('integrations')}
            styles={styles}
          />
        );

      case 'integrations':
        return isLoggedIn
          ? <IntegrationScreen />
          : <GuestGate tabName="Integration Hub" onLoginPress={openLogin} styles={styles} />;

      case 'notifications':
        return isLoggedIn
          ? <NotificationScreen user={user} />
          : <GuestGate tabName="Login Approvals" onLoginPress={openLogin} styles={styles} />;

      case 'settings':
        return (
          <SettingsScreen
            user={user}
            onLoginPress={openLogin}
            onLogout={onLogout}
            onOpenIntegrations={() => onTabChange('integrations')}
            onOpenApprovals={() => onTabChange('notifications')}
          />
        );

      default:
        return <DashboardHome user={user} styles={styles} />;
    }
  };

  return (
    <View style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#0E1326" />

      {/* Header — only on dashboard tab */}
      {activeTab === 'dashboard' && (
        <View style={styles.header}>
          <LogoHeader
            styles={styles}
            onOpenNotifications={() => onTabChange('notifications')}
            onOpenSettings={() => onTabChange('settings')}
          />
        </View>
      )}

      {/* Tab content */}
      <View style={{ flex: 1 }}>
        {renderContent()}
      </View>

      {/* ── Bottom Navigation ── */}
      <View style={styles.bottomNav}>
        {[
          { key: 'dashboard',     icon: 'grid',     label: 'Home' },
          { key: 'integrations',  icon: 'link',     label: 'Apps' },
          { key: 'notifications', icon: 'bell',     label: 'Alerts' },
          { key: 'settings',      icon: 'settings', label: 'Settings' },
        ].map(tab => {
          const isActive = activeTab === tab.key;
          return (
            <TouchableOpacity
              key={tab.key}
              id={`nav${tab.key.charAt(0).toUpperCase() + tab.key.slice(1)}`}
              style={styles.navItem}
              onPress={() => onTabChange(tab.key)}
              activeOpacity={0.7}
            >
              <View style={[
                styles.navSquare,
                isActive && styles.navSquareActive,
              ]}>
                <Feather
                  name={tab.icon}
                  size={18}
                  color={isActive ? '#00BBFF' : '#94A3B8'}
                  strokeWidth={1.75}
                />
              </View>
              <Text style={[
                styles.navLabel,
                isActive && styles.navLabelActive,
              ]}>
                {tab.label}
              </Text>
            </TouchableOpacity>
          );
        })}
      </View>

      {isLoggedIn && (
        <ApprovalPopup
          visible={approvalPopupVisible}
          event={pendingApproval}
          onDismiss={dismissApprovalPopup}
          onRespond={respondToApproval}
          approvalDecision={approvalDecision}
          styles={styles}
        />
      )}

      {/* ── Floating Login Modal ── */}
      <LoginModal
        visible={loginModalVisible}
        onClose={closeLogin}
        onLoginSuccess={handleLoginSuccess}
      />
    </View>
  );
}

const createStyles = () => StyleSheet.create({
  container: { flex: 1, backgroundColor: '#0E1326' },

  // Header
  header: {
    backgroundColor: '#0E1326',
    paddingTop: 52,
    paddingBottom: 14,
    paddingHorizontal: 20,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255, 255, 255, 0.08)',
  },
  logoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  headerLogo: {
    width: 132,
    height: 32,
  },
  headerActions: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  headerIconBtn: {
    width: 36,
    height: 36,
    borderRadius: 8,
    backgroundColor: '#1C2236',
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.08)',
    alignItems: 'center',
    justifyContent: 'center',
  },

  // Welcome
  welcomeBanner: {
    backgroundColor: '#0E1326',
    paddingHorizontal: 20,
    paddingVertical: 16,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255, 255, 255, 0.08)',
  },
  welcomeTitle: {
    color: '#F8FAFC',
    fontSize: 18,
    fontWeight: '700',
    marginBottom: 4,
    letterSpacing: -0.3,
  },
  welcomeSub: {
    color: '#94A3B8',
    fontSize: 12,
    fontWeight: '500',
  },

  // Summary Section
  summarySection: {
    backgroundColor: '#1C2236',
    borderRadius: 12,
    padding: 16,
    marginBottom: 18,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.08)',
  },
  summaryHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  summaryTitle: {
    color: '#F8FAFC',
    fontSize: 14,
    fontWeight: '700',
  },
  summaryLink: {
    color: '#00BBFF',
    fontSize: 12,
    fontWeight: '600',
  },
  summaryItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    borderTopWidth: 1,
    borderTopColor: 'rgba(255, 255, 255, 0.06)',
  },
  summaryIconWrap: {
    width: 34,
    height: 34,
    backgroundColor: '#0E1326',
    borderRadius: 8,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.08)',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 12,
  },
  summaryItemTitle: {
    color: '#F8FAFC',
    fontSize: 13,
    fontWeight: '600',
  },
  summaryItemMeta: {
    color: '#94A3B8',
    fontSize: 11,
    marginTop: 2,
  },
  summaryEmpty: {
    paddingVertical: 18,
    alignItems: 'center',
  },
  summaryEmptyText: {
    color: '#64748B',
    fontSize: 12,
  },

  // Feed
  feed: { flex: 1 },
  feedContent: { flexGrow: 1, padding: 20 },
  emptyState: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: '20%',
    backgroundColor: '#1C2236',
    borderRadius: 12,
    padding: 28,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.08)',
  },
  emptyIconBox: {
    width: 48,
    height: 48,
    borderRadius: 10,
    backgroundColor: '#0E1326',
    borderWidth: 1,
    borderColor: 'rgba(0, 187, 255, 0.25)',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 14,
  },
  homeApprovalCard: {
    backgroundColor: '#1C2236',
    borderWidth: 1,
    borderColor: 'rgba(0, 187, 255, 0.35)',
    borderRadius: 12,
    padding: 18,
    marginBottom: 16,
  },
  homeApprovalHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    gap: 12,
    marginBottom: 10,
  },
  homeApprovalEyebrow: {
    color: '#00BBFF',
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 0.8,
    textTransform: 'uppercase',
  },
  homeApprovalReview: {
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: 6,
    backgroundColor: '#0E1326',
    borderWidth: 1,
    borderColor: 'rgba(0, 187, 255, 0.3)',
  },
  homeApprovalReviewText: {
    color: '#00BBFF',
    fontSize: 11,
    fontWeight: '600',
  },
  homeApprovalApp: {
    color: '#F8FAFC',
    fontSize: 16,
    fontWeight: '700',
    marginBottom: 4,
  },
  homeApprovalMeta: {
    color: '#94A3B8',
    fontSize: 12,
    marginBottom: 8,
  },
  homeApprovalText: {
    color: '#94A3B8',
    fontSize: 12,
    lineHeight: 18,
    marginBottom: 14,
  },
  homeApprovalActions: { flexDirection: 'row', gap: 10 },
  homeApprovalBtn: {
    flex: 1,
    minHeight: 42,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 12,
  },
  homeApprovalApprove: {
    backgroundColor: '#4063D7',
  },
  homeApprovalApproveText: { color: '#FFFFFF', fontSize: 13, fontWeight: '600' },
  homeApprovalDecline: {
    backgroundColor: '#0E1326',
    borderWidth: 1,
    borderColor: 'rgba(239, 68, 68, 0.35)',
  },
  homeApprovalDeclineText: { color: '#F87171', fontSize: 13, fontWeight: '600' },
  emptyTitle: { color: '#F8FAFC', fontSize: 16, fontWeight: '700', marginBottom: 6 },
  emptyText:  { color: '#94A3B8', fontSize: 12, textAlign: 'center', lineHeight: 18 },

  // Guest gate
  gateWrap: {
    flex: 1, alignItems: 'center', justifyContent: 'center', padding: 32,
  },
  gateTitle: { color: '#F8FAFC', fontSize: 18, fontWeight: '700', marginBottom: 8 },
  gateSub:   { color: '#94A3B8', fontSize: 13, textAlign: 'center', lineHeight: 20, marginBottom: 24 },
  gateBtn: {
    backgroundColor: '#4063D7',
    borderRadius: 8,
    paddingVertical: 13,
    paddingHorizontal: 36,
    alignItems: 'center',
  },
  gateBtnText: { color: '#FFFFFF', fontSize: 14, fontWeight: '600' },

  // Bottom nav
  bottomNav: {
    flexDirection: 'row',
    backgroundColor: '#0E1326',
    borderTopWidth: 1,
    borderTopColor: 'rgba(255, 255, 255, 0.08)',
    paddingBottom: 24,
    paddingTop: 10,
    justifyContent: 'space-evenly',
    alignItems: 'center',
  },
  navItem:  { alignItems: 'center', flex: 1, gap: 4 },
  navSquare: {
    width: 40,
    height: 34,
    backgroundColor: 'transparent',
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  navSquareActive: {
    backgroundColor: '#1C2236',
    borderWidth: 1,
    borderColor: 'rgba(0, 187, 255, 0.3)',
  },
  navLabel: { fontSize: 10, color: '#94A3B8', fontWeight: '500' },
  navLabelActive: { color: '#00BBFF', fontWeight: '600' },

  popupBackdrop: {
    flex: 1,
    backgroundColor: 'rgba(14, 19, 38, 0.8)',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 24,
  },
  popupCard: {
    width: '100%',
    maxWidth: 360,
    backgroundColor: '#1C2236',
    borderRadius: 12,
    padding: 22,
    borderWidth: 1,
    borderColor: 'rgba(0, 187, 255, 0.3)',
  },
  popupTitle: {
    color: '#00BBFF',
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 0.8,
    textTransform: 'uppercase',
    marginBottom: 8,
  },
  popupApp: { color: '#F8FAFC', fontSize: 17, fontWeight: '700', marginBottom: 4 },
  popupMeta: { color: '#94A3B8', fontSize: 12, marginBottom: 18 },
  popupActions: { flexDirection: 'row', gap: 10 },
  popupActionDisabled: { opacity: 0.6 },
  popupApprove: {
    flex: 1,
    backgroundColor: '#4063D7',
    borderRadius: 8,
    paddingVertical: 12,
    alignItems: 'center',
  },
  popupApproveText: { color: '#FFFFFF', fontSize: 13, fontWeight: '600' },
  popupDecline: {
    flex: 1,
    backgroundColor: '#0E1326',
    borderWidth: 1,
    borderColor: 'rgba(239, 68, 68, 0.35)',
    borderRadius: 8,
    paddingVertical: 12,
    alignItems: 'center',
  },
  popupDeclineText: { color: '#F87171', fontSize: 13, fontWeight: '600' },

  // ── Stat cards (minimalist grid) ───────────────────────────────────────
  statsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 10,
    marginBottom: 12,
  },
  statCard: {
    flex: 1,
    minWidth: '45%',
    backgroundColor: '#1C2236',
    borderRadius: 10,
    padding: 16,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.08)',
  },
  statLabel: {
    fontSize: 10,
    fontWeight: '600',
    color: '#94A3B8',
    textTransform: 'uppercase',
    letterSpacing: 0.6,
    marginBottom: 6,
  },
  statValue: { fontSize: 20, fontWeight: '700' },

  // ── Status panel ───────────────────────────────────────────────────────
  statusPanel: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 14,
    backgroundColor: '#1C2236',
    borderRadius: 10,
    padding: 16,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.08)',
    marginBottom: 10,
  },
  statusIconWrap: {
    width: 38,
    height: 38,
    backgroundColor: '#0E1326',
    borderRadius: 8,
    borderWidth: 1,
    borderColor: 'rgba(0, 187, 255, 0.25)',
    alignItems: 'center',
    justifyContent: 'center',
    flexShrink: 0,
  },
  statusTitle: { color: '#F8FAFC', fontSize: 13, fontWeight: '700', marginBottom: 2 },
  statusSub:   { color: '#94A3B8', fontSize: 12, lineHeight: 17 },

  // ── Integration Hub card ───────────────────────────────────────────────
  integrationCard: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 14,
    backgroundColor: '#1C2236',
    borderRadius: 10,
    padding: 16,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.08)',
    marginBottom: 16,
  },
  integrationCardTitle: { color: '#F8FAFC', fontSize: 13, fontWeight: '700', marginBottom: 2 },
  integrationCardSub:   { color: '#94A3B8', fontSize: 12, lineHeight: 17 },
});
