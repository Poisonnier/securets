import React, { useEffect, useRef, useState } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity,
  ScrollView, StatusBar, Modal, Pressable,
  TouchableWithoutFeedback, ActivityIndicator, Alert,
} from 'react-native';

import IntegrationScreen  from './IntegrationScreen';
import NotificationScreen from './NotificationScreen';
import SettingsScreen     from './SettingsScreen';
import LoginModal         from './LoginModal';
import { integrationApi } from '../src/api/integrations';

// ─── Logo Header ────────────────────────────────────────────────────────────
function LogoHeader() {
  return (
    <View style={styles.logoRow}>
      <View style={styles.hexOuter}><Text style={styles.hexLock}>🔒</Text></View>
      <View style={styles.shield}>
        <View style={styles.stl} /><View style={styles.str} />
        <View style={styles.sbl} /><View style={styles.sbr} />
        <View style={styles.shieldOverlay}><Text style={styles.shieldCheck}>✓</Text></View>
      </View>
      <View>
        <Text style={styles.brandName}>Secure<Text style={styles.brandTs}>TS</Text></Text>
        <Text style={styles.brandSub}>ThreatShield</Text>
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
}) {
  const [recentNotifications, setRecentNotifications] = useState([]);
  const [alertsCount, setAlertsCount] = useState(0);

  useEffect(() => {
    if (!user) return;
    const fetchRecent = async () => {
      try {
        const res = await integrationApi.history();
        setRecentNotifications(res.data.slice(0, 3));
        setAlertsCount(res.data.length);
      } catch (_) {}
    };
    fetchRecent();
  }, [user]);

  return (
    <>
      <View style={styles.welcomeBanner}>
        <Text style={styles.welcomeTitle}>
          {user ? `Welcome, ${user.name || 'User'}! 👋` : 'Welcome to SecureTS 👋'}
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
                  ? <ActivityIndicator color="#4ADE80" size="small" />
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
            {/* ── Stat cards (matches web dashboard) ── */}
            <View style={styles.statsGrid}>
              <View style={[styles.statCard, styles.statBlue]}>
                <Text style={styles.statLabel}>Protection Status</Text>
                <Text style={[styles.statValue, { color: '#818CF8' }]}>Active</Text>
              </View>
              <View style={[styles.statCard, styles.statGreen]}>
                <Text style={styles.statLabel}>Threats Blocked</Text>
                <Text style={[styles.statValue, { color: '#4ADE80' }]}>0</Text>
              </View>
              <View style={[styles.statCard, styles.statYellow]}>
                <Text style={styles.statLabel}>Auth Latency</Text>
                <Text style={[styles.statValue, { color: '#FACC15' }]}>{'<500ms'}</Text>
              </View>
              <View style={[styles.statCard, styles.statRed]}>
                <Text style={styles.statLabel}>Alerts</Text>
                <Text style={[styles.statValue, { color: '#F87171' }]}>{alertsCount}</Text>
              </View>
            </View>

            {/* ── All Systems Operational panel ── */}
            <View style={styles.statusPanel}>
              <View style={styles.statusIconWrap}>
                <Text style={{ fontSize: 22 }}>🛡️</Text>
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
              <View style={[styles.statusIconWrap, { backgroundColor: 'rgba(61,90,241,0.12)' }]}>
                <Text style={{ fontSize: 22 }}>🔗</Text>
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.integrationCardTitle}>Integration Hub</Text>
                <Text style={styles.integrationCardSub}>
                  Connect external apps, manage credentials, approve or decline logins.
                </Text>
              </View>
              <Text style={{ color: '#94A3B8', fontSize: 22 }}>›</Text>
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
                      <Text style={{ fontSize: 16 }}>{notif.status === 'approved' ? '✅' : notif.status === 'declined' ? '❌' : '🔔'}</Text>
                    </View>
                    <View style={{ flex: 1 }}>
                      <Text style={styles.summaryItemTitle}>{notif.integration?.name ?? 'SecureTS Alert'}</Text>
                      <Text style={styles.summaryItemMeta}>{new Date(notif.created_at).toLocaleTimeString()}</Text>
                    </View>
                    <Text style={styles.summaryChevron}>›</Text>
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
            <Text style={{ fontSize: 52, marginBottom: 14 }}>🛡️</Text>
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
function GuestGate({ tabName, onLoginPress }) {
  return (
    <View style={styles.gateWrap}>
      <Text style={{ fontSize: 56, marginBottom: 18 }}>🔒</Text>
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
          />
        );

      case 'integrations':
        return isLoggedIn
          ? <IntegrationScreen />
          : <GuestGate tabName="Integration Hub" onLoginPress={openLogin} />;

      case 'notifications':
        return isLoggedIn
          ? <NotificationScreen user={user} />
          : <GuestGate tabName="Login Approvals" onLoginPress={openLogin} />;

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
        return <DashboardHome user={user} />;
    }
  };

  return (
    <View style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#0F172A" />

      {/* Header — only on dashboard tab */}
      {activeTab === 'dashboard' && (
        <View style={styles.header}>
          <LogoHeader />
        </View>
      )}

      {/* Tab content */}
      <View style={{ flex: 1 }}>
        {renderContent()}
      </View>

      {/* ── Bottom Navigation ── */}
      <View style={styles.bottomNav}>
        {[
          { key: 'dashboard',     icon: '⊞',  label: 'Home' },
          { key: 'integrations',  icon: '🔗',  label: 'Apps' },
          { key: 'notifications', icon: '🔔',  label: 'Alerts' },
          { key: 'settings',      icon: '⚙️',  label: 'Settings' },
        ].map(tab => (
          <TouchableOpacity
            key={tab.key}
            id={`nav${tab.key.charAt(0).toUpperCase() + tab.key.slice(1)}`}
            style={styles.navItem}
            onPress={() => onTabChange(tab.key)}
            activeOpacity={0.7}
          >
            <View style={[
              styles.navSquare,
              activeTab === tab.key && styles.navSquareActive,
            ]}>
              <Text style={styles.navIcon}>{tab.icon}</Text>
            </View>
            <Text style={[
              styles.navLabel,
              activeTab === tab.key && styles.navLabelActive,
            ]}>
              {tab.label}
            </Text>
          </TouchableOpacity>
        ))}
      </View>

      {isLoggedIn && (
        <ApprovalPopup
          visible={approvalPopupVisible}
          event={pendingApproval}
          onDismiss={dismissApprovalPopup}
          onRespond={respondToApproval}
          approvalDecision={approvalDecision}
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

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#0A0F1E' },

  // Header
  header: {
    backgroundColor: '#0F172A',
    paddingTop: 52, paddingBottom: 14, paddingHorizontal: 20,
    borderBottomWidth: 1, borderBottomColor: 'rgba(56,102,230,0.3)',
  },
  logoRow: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  hexOuter: {
    width: 30, height: 30, backgroundColor: '#1E293B',
    borderRadius: 6, alignItems: 'center', justifyContent: 'center',
  },
  hexLock: { fontSize: 14 },
  shield: {
    width: 26, height: 30, borderRadius: 3,
    overflow: 'hidden', flexDirection: 'row', flexWrap: 'wrap', position: 'relative',
  },
  stl: { width: 13, height: 15, backgroundColor: '#C0392B' },
  str: { width: 13, height: 15, backgroundColor: '#ECF0F1' },
  sbl: { width: 13, height: 15, backgroundColor: '#27AE60' },
  sbr: { width: 13, height: 15, backgroundColor: '#2980B9' },
  shieldOverlay: { ...StyleSheet.absoluteFillObject, alignItems: 'center', justifyContent: 'center' },
  shieldCheck: { color: 'white', fontSize: 12, fontWeight: '700' },
  brandName: { fontSize: 15, fontWeight: '800', color: 'white' },
  brandTs:   { color: '#3866E6' },
  brandSub:  { fontSize: 8, color: '#64748B', fontWeight: '500' },

  // Welcome
  welcomeBanner: {
    backgroundColor: '#0F172A',
    paddingHorizontal: 20, paddingVertical: 16,
    borderBottomWidth: 1, borderBottomColor: 'rgba(255,255,255,0.06)',
  },
  welcomeTitle: { color: 'white', fontSize: 20, fontWeight: '700', marginBottom: 4 },
  welcomeSub:   { color: '#3866E6', fontSize: 13, fontWeight: '500' },

  // Summary Section
  summarySection: {
    backgroundColor: '#1E293B',
    borderRadius: 18,
    padding: 16,
    marginBottom: 18,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.06)',
  },
  summaryHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  summaryTitle: {
    color: 'white',
    fontSize: 16,
    fontWeight: '700',
  },
  summaryLink: {
    color: '#3866E6',
    fontSize: 13,
    fontWeight: '600',
  },
  summaryItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255,255,255,0.06)',
  },
  summaryIconWrap: {
    width: 36,
    height: 36,
    backgroundColor: '#0F172A',
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 12,
  },
  summaryItemTitle: {
    color: '#F8FAFC',
    fontSize: 14,
    fontWeight: '600',
  },
  summaryItemMeta: {
    color: '#64748B',
    fontSize: 12,
    marginTop: 2,
  },
  summaryChevron: {
    color: '#64748B',
    fontSize: 20,
    fontWeight: '300',
    marginLeft: 8,
  },
  summaryEmpty: {
    paddingVertical: 20,
    alignItems: 'center',
  },
  summaryEmptyText: {
    color: '#64748B',
    fontSize: 13,
  },

  // Feed
  feed: { flex: 1 },
  feedContent: { flexGrow: 1, padding: 20 },
  emptyState: {
    flex: 1, alignItems: 'center', justifyContent: 'center', marginTop: '25%',
  },
  homeApprovalCard: {
    backgroundColor: '#1E293B',
    borderWidth: 1,
    borderColor: 'rgba(234,179,8,0.3)',
    borderRadius: 18,
    padding: 18,
    marginBottom: 18,
  },
  homeApprovalHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    gap: 12,
    marginBottom: 10,
  },
  homeApprovalEyebrow: {
    color: '#FACC15',
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 1,
    textTransform: 'uppercase',
  },
  homeApprovalReview: {
    paddingHorizontal: 12,
    paddingVertical: 7,
    borderRadius: 999,
    backgroundColor: 'rgba(129,140,248,0.14)',
  },
  homeApprovalReviewText: {
    color: '#A5B4FC',
    fontSize: 12,
    fontWeight: '700',
  },
  homeApprovalApp: {
    color: '#F8FAFC',
    fontSize: 18,
    fontWeight: '700',
    marginBottom: 4,
  },
  homeApprovalMeta: {
    color: '#94A3B8',
    fontSize: 13,
    marginBottom: 8,
  },
  homeApprovalText: {
    color: '#CBD5E1',
    fontSize: 13,
    lineHeight: 20,
    marginBottom: 14,
  },
  homeApprovalActions: { flexDirection: 'row', gap: 10 },
  homeApprovalBtn: {
    flex: 1,
    minHeight: 48,
    borderRadius: 12,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 12,
  },
  homeApprovalApprove: {
    backgroundColor: 'rgba(34,197,94,0.15)',
    borderWidth: 1,
    borderColor: 'rgba(34,197,94,0.3)',
  },
  homeApprovalApproveText: { color: '#4ADE80', fontWeight: '700' },
  homeApprovalDecline: {
    backgroundColor: 'rgba(239,68,68,0.15)',
    borderWidth: 1,
    borderColor: 'rgba(239,68,68,0.3)',
  },
  homeApprovalDeclineText: { color: '#F87171', fontWeight: '700' },
  emptyTitle: { color: '#F8FAFC', fontSize: 18, fontWeight: '700', marginBottom: 8 },
  emptyText:  { color: '#64748B', fontSize: 13, textAlign: 'center', lineHeight: 20 },

  // Guest gate
  gateWrap: {
    flex: 1, alignItems: 'center', justifyContent: 'center', padding: 40,
  },
  gateTitle: { color: '#F8FAFC', fontSize: 20, fontWeight: '800', marginBottom: 10 },
  gateSub:   { color: '#94A3B8', fontSize: 14, textAlign: 'center', lineHeight: 22, marginBottom: 28 },
  gateBtn: {
    backgroundColor: '#3D5AF1', borderRadius: 12,
    paddingVertical: 14, paddingHorizontal: 40, alignItems: 'center',
  },
  gateBtnText: { color: '#fff', fontSize: 15, fontWeight: '700' },

  // Bottom nav
  bottomNav: {
    flexDirection: 'row',
    backgroundColor: '#0F172A',
    borderTopWidth: 1,
    borderTopColor: 'rgba(56,102,230,0.3)',
    paddingBottom: 28, paddingTop: 10,
    justifyContent: 'space-evenly', alignItems: 'center',
  },
  navItem:  { alignItems: 'center', flex: 1, gap: 4 },
  navSquare: {
    width: 44, height: 38,
    backgroundColor: 'rgba(255,255,255,0.08)',
    borderRadius: 8, alignItems: 'center', justifyContent: 'center',
  },
  navSquareActive: { backgroundColor: '#3866E6' },
  navIcon:  { fontSize: 17 },
  navLabel: { fontSize: 10, color: '#64748B', fontWeight: '500' },
  navLabelActive: { color: '#818CF8' },

  popupBackdrop: {
    flex: 1, backgroundColor: 'rgba(0,0,0,0.72)',
    alignItems: 'center', justifyContent: 'center', padding: 24,
  },
  popupCard: {
    width: '100%', maxWidth: 360, backgroundColor: '#1E293B',
    borderRadius: 16, padding: 22, borderWidth: 1, borderColor: 'rgba(234,179,8,0.35)',
  },
  popupTitle: { color: '#FACC15', fontSize: 12, fontWeight: '700', letterSpacing: 1, marginBottom: 8 },
  popupApp: { color: '#F8FAFC', fontSize: 18, fontWeight: '700', marginBottom: 6 },
  popupMeta: { color: '#94A3B8', fontSize: 13, marginBottom: 18 },
  popupActions: { flexDirection: 'row', gap: 8 },
  popupActionDisabled: { opacity: 0.7 },
  popupApprove: {
    flex: 1, backgroundColor: 'rgba(34,197,94,0.15)', borderWidth: 1,
    borderColor: 'rgba(34,197,94,0.3)', borderRadius: 10, padding: 12, alignItems: 'center',
  },
  popupApproveText: { color: '#4ADE80', fontWeight: '700' },
  popupDecline: {
    flex: 1, backgroundColor: 'rgba(239,68,68,0.15)', borderWidth: 1,
    borderColor: 'rgba(239,68,68,0.3)', borderRadius: 10, padding: 12, alignItems: 'center',
  },
  popupDeclineText: { color: '#F87171', fontWeight: '700' },

  // ── Stat cards (web-matching) ──────────────────────────────────────────
  statsGrid: {
    flexDirection: 'row', flexWrap: 'wrap', gap: 12, marginBottom: 14,
  },
  statCard: {
    flex: 1, minWidth: '45%',
    backgroundColor: '#1E293B',
    borderRadius: 14, padding: 18,
    borderWidth: 1, borderColor: 'rgba(255,255,255,0.08)',
  },
  statBlue:   { borderTopWidth: 3, borderTopColor: '#3D5AF1' },
  statGreen:  { borderTopWidth: 3, borderTopColor: '#22C55E' },
  statYellow: { borderTopWidth: 3, borderTopColor: '#EAB308' },
  statRed:    { borderTopWidth: 3, borderTopColor: '#EF4444' },
  statLabel: {
    fontSize: 10, fontWeight: '700', color: '#94A3B8',
    textTransform: 'uppercase', letterSpacing: 0.5, marginBottom: 8,
  },
  statValue: { fontSize: 22, fontWeight: '800' },

  // ── Status panel ───────────────────────────────────────────────────────
  statusPanel: {
    flexDirection: 'row', alignItems: 'center', gap: 14,
    backgroundColor: '#1E293B',
    borderRadius: 14, padding: 18,
    borderWidth: 1, borderColor: 'rgba(255,255,255,0.08)',
    marginBottom: 12,
  },
  statusIconWrap: {
    width: 48, height: 48,
    backgroundColor: 'rgba(34,197,94,0.12)',
    borderRadius: 24,
    alignItems: 'center', justifyContent: 'center',
    flexShrink: 0,
  },
  statusTitle: { color: '#4ADE80', fontSize: 14, fontWeight: '700', marginBottom: 3 },
  statusSub:   { color: '#94A3B8', fontSize: 12, lineHeight: 17 },

  // ── Integration Hub card ───────────────────────────────────────────────
  integrationCard: {
    flexDirection: 'row', alignItems: 'center', gap: 14,
    backgroundColor: '#1E293B',
    borderRadius: 14, padding: 18,
    borderWidth: 1, borderColor: 'rgba(255,255,255,0.08)',
    marginBottom: 16,
  },
  integrationCardTitle: { color: '#F8FAFC', fontSize: 14, fontWeight: '700', marginBottom: 3 },
  integrationCardSub:   { color: '#94A3B8', fontSize: 12, lineHeight: 17 },
});
