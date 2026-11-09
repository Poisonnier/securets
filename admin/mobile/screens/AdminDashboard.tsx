import React, { useMemo, useRef, useState } from 'react';
import {
  Animated,
  Easing,
  FlatList,
  LayoutChangeEvent,
  PanResponder,
  Pressable,
  SafeAreaView,
  StatusBar,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { ThemeProvider, ThemeTokens, useTheme } from '../theme/ThemeContext';

type AlertActionType = 'primary' | 'neutral';

interface AlertAction {
  id: string;
  label: string;
  variant: AlertActionType;
}

interface TriageAlert {
  id: string;
  category: string;
  title: string;
  subject: string;
  meta: string;
  timestamp: string;
  actions: AlertAction[];
  resolvedAction?: string;
}

interface SystemVital {
  id: string;
  label: string;
  value: string;
  isLiveAccent?: boolean;
}

const INITIAL_VITALS: SystemVital[] = [
  { id: 'api-health', label: 'API Health', value: '99.98%', isLiveAccent: true },
  { id: 'active-users', label: 'Active Users', value: '1,428' },
  { id: 'pending-reqs', label: 'Pending Reqs', value: '14' },
];

const INITIAL_ALERTS: TriageAlert[] = [
  {
    id: 'alert-recovery-01',
    category: 'DEVICE RECOVERY',
    title: 'Lost Device Recovery Request',
    subject: 'm.chen@acme-corp.io • iPhone 16 Pro',
    meta: 'IP 198.51.100.42 • Verified via Secondary Backup Code',
    timestamp: '2m ago',
    actions: [
      { id: 'approve', label: 'Approve', variant: 'primary' },
      { id: 'deny', label: 'Deny', variant: 'neutral' },
    ],
  },
  {
    id: 'alert-totp-02',
    category: 'ANOMALY DETECTION',
    title: 'Spike in Failed TOTP Attempts',
    subject: 'Target App: VaultCore Production',
    meta: '48 failed verifications in 60s • ASN 14061 (Frankfurt)',
    timestamp: 'Just now',
    actions: [
      { id: 'force-logout', label: 'Force Logout', variant: 'neutral' },
      { id: 'deny', label: 'Deny', variant: 'neutral' },
    ],
  },
];

const THUMB_SIZE = 44;
const TRACK_PADDING = 4;
const SWIPE_THRESHOLD_RATIO = 0.82;

function SwipeToLockdown({
  locked,
  onTriggerLockdown,
   onResetLockdown,
}: {
  locked: boolean;
  onTriggerLockdown: () => void;
  onResetLockdown: () => void;
}) {
  const theme = useTheme();
  const styles = useMemo(() => createStyles(theme), [theme]);
  const [trackWidth, setTrackWidth] = useState<number>(0);
  const translateX = useRef(new Animated.Value(0)).current;

  const maxSlide = Math.max(0, trackWidth - THUMB_SIZE - TRACK_PADDING * 2);

  const animateThumbTo = (toValue: number, callback?: () => void) => {
    Animated.timing(translateX, {
      toValue,
      duration: 180,
      easing: Easing.out(Easing.quad),
      useNativeDriver: true,
    }).start(callback);
  };

  const panResponder = useMemo(
    () =>
      PanResponder.create({
        onStartShouldSetPanResponder: () => !locked && maxSlide > 0,
        onMoveShouldSetPanResponder: (_, gestureState) =>
          !locked && maxSlide > 0 && Math.abs(gestureState.dx) > 4,
        onPanResponderMove: (_, gestureState) => {
          if (locked || maxSlide <= 0) {
            return;
          }
          const clamped = Math.min(Math.max(0, gestureState.dx), maxSlide);
          translateX.setValue(clamped);
        },
        onPanResponderRelease: (_, gestureState) => {
          if (locked || maxSlide <= 0) {
            return;
          }
          if (gestureState.dx >= maxSlide * SWIPE_THRESHOLD_RATIO) {
            animateThumbTo(maxSlide, onTriggerLockdown);
          } else {
            animateThumbTo(0);
          }
        },
        onPanResponderTerminate: () => {
          if (!locked) {
            animateThumbTo(0);
          }
        },
      }),
    [locked, maxSlide, onTriggerLockdown, translateX]
  );

  const handleTrackLayout = (event: LayoutChangeEvent) => {
    const width = event.nativeEvent.layout.width;
    setTrackWidth(width);
    if (locked) {
      const nextMax = Math.max(0, width - THUMB_SIZE - TRACK_PADDING * 2);
      translateX.setValue(nextMax);
    }
  };

  const handleUnlock = () => {
    animateThumbTo(0, onResetLockdown);
  };

  return (
    <View style={styles.lockdownContainer}>
      <View style={styles.lockdownHeaderRow}>
        <Text style={styles.sectionLabel}>EMERGENCY KILL SWITCH</Text>
        {locked ? (
          <Pressable
            onPress={handleUnlock}
            style={styles.resetLockdownButton}
            accessibilityRole="button"
            accessibilityLabel="Restore normal authentication"
          >
            <Text style={styles.resetLockdownText}>Restore Access</Text>
          </Pressable>
        ) : (
          <Text style={styles.lockdownMeta}>Global Auth Freeze</Text>
        )}
      </View>

      <View
        onLayout={handleTrackLayout}
        style={[
          styles.sliderTrack,
          locked && styles.sliderTrackLocked,
        ]}
        accessibilityRole="adjustable"
        accessibilityLabel="Swipe to trigger global authentication lockdown"
      >
        <Text style={styles.sliderLabel} numberOfLines={1}>
          {locked ? 'LOCKDOWN ACTIVE — ALL TOKENS HALTED' : 'SWIPE TO LOCKDOWN'}
        </Text>

        <Animated.View
          {...panResponder.panHandlers}
          style={[
            styles.sliderThumb,
            { transform: [{ translateX }] },
          ]}
        >
          <Text style={styles.sliderThumbGlyph}>{locked ? '■' : '→'}</Text>
        </Animated.View>
      </View>
    </View>
  );
}

function AdminDashboardContent() {
  const theme = useTheme();
  const styles = useMemo(() => createStyles(theme), [theme]);

  const [alerts, setAlerts] = useState<TriageAlert[]>(INITIAL_ALERTS);
  const [isLockedDown, setIsLockedDown] = useState<boolean>(false);

  const pendingCount = useMemo(
    () => alerts.filter((item) => !item.resolvedAction).length,
    [alerts]
  );

  const vitals = useMemo(
    () =>
      INITIAL_VITALS.map((vital) => {
        if (vital.id === 'pending-reqs') {
          return { ...vital, value: String(12 + pendingCount) };
        }
        if (vital.id === 'api-health' && isLockedDown) {
          return { ...vital, value: 'LOCKED' };
        }
        return vital;
      }),
    [pendingCount, isLockedDown]
  );

  const handleAlertAction = (alertId: string, actionLabel: string) => {
    setAlerts((current) =>
      current.map((item) =>
        item.id === alertId ? { ...item, resolvedAction: actionLabel } : item
      )
    );
  };

  const renderAlertCard = ({ item }: { item: TriageAlert }) => {
    const isFirstUnresolved =
      !item.resolvedAction &&
      alerts.find((a) => !a.resolvedAction)?.id === item.id;

    return (
      <View style={styles.alertCard}>
        <View style={styles.alertCardTopRow}>
          <View style={styles.categoryWrap}>
            {isFirstUnresolved && <View style={styles.accentDot} />}
            <Text style={styles.alertCategory}>{item.category}</Text>
          </View>
          <Text style={styles.alertTimestamp}>{item.timestamp}</Text>
        </View>

        <Text style={styles.alertTitle}>{item.title}</Text>
        <Text style={styles.alertSubject}>{item.subject}</Text>
        <Text style={styles.alertMeta}>{item.meta}</Text>

        {item.resolvedAction ? (
          <View style={styles.resolvedBanner}>
            <Text style={styles.resolvedText}>
              Action recorded: {item.resolvedAction}
            </Text>
          </View>
        ) : (
          <View style={styles.actionRow}>
            {item.actions.map((action) => {
              const usePrimaryFill =
                action.variant === 'primary' && isFirstUnresolved && !isLockedDown;

              return (
                <Pressable
                  key={action.id}
                  onPress={() => handleAlertAction(item.id, action.label)}
                  disabled={isLockedDown}
                  style={[
                    styles.actionButton,
                    usePrimaryFill
                      ? styles.actionButtonPrimary
                      : styles.actionButtonNeutral,
                    isLockedDown && styles.actionButtonDisabled,
                  ]}
                  accessibilityRole="button"
                  accessibilityLabel={`${action.label} for ${item.title}`}
                >
                  <Text
                    style={
                      usePrimaryFill
                        ? styles.actionButtonTextPrimary
                        : styles.actionButtonTextNeutral
                    }
                  >
                    {action.label}
                  </Text>
                </Pressable>
              );
            })}
          </View>
        )}
      </View>
    );
  };

  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar barStyle="light-content" backgroundColor={theme.colors.background} />
      <View style={styles.container}>
        {/* Top Header */}
        <View style={styles.header}>
          <View>
            <Text style={styles.brandSubtitle}>SECURETHREATSHIELD</Text>
            <Text style={styles.headerTitle}>Command Center</Text>
          </View>
          <View style={styles.nodeBadge}>
            <Text style={styles.nodeBadgeText}>
              {isLockedDown ? 'HALTED' : 'LIVE NODE'}
            </Text>
          </View>
        </View>

        {/* 1. Glanceable System Vitals Stack */}
        <View style={styles.vitalsSection}>
          <Text style={styles.sectionLabel}>SYSTEM VITALS</Text>
          <View style={styles.vitalsRow}>
            {vitals.map((vital) => (
              <View key={vital.id} style={styles.vitalCard}>
                <Text style={styles.vitalLabel} numberOfLines={1}>
                  {vital.label}
                </Text>
                <Text
                  style={[
                    styles.vitalValue,
                    vital.isLiveAccent && styles.vitalValueAccent,
                  ]}
                  numberOfLines={1}
                >
                  {vital.value}
                </Text>
              </View>
            ))}
          </View>
        </View>

        {/* 2. Push-Based Triage & Actionable Alerts */}
        <View style={styles.triageSection}>
          <View style={styles.triageHeaderRow}>
            <Text style={styles.sectionLabel}>ACTIVE TRIAGE QUEUE</Text>
            <Text style={styles.queueCountText}>{pendingCount} Pending</Text>
          </View>

          <FlatList
            data={alerts}
            keyExtractor={(item) => item.id}
            renderItem={renderAlertCard}
            contentContainerStyle={styles.alertsListContent}
            showsVerticalScrollIndicator={false}
          />
        </View>

        {/* 3. Emergency Switch */}
        <SwipeToLockdown
          locked={isLockedDown}
          onTriggerLockdown={() => setIsLockedDown(true)}
          onResetLockdown={() => setIsLockedDown(false)}
        />
      </View>
    </SafeAreaView>
  );
}

export default function AdminDashboard() {
  return (
    <ThemeProvider>
      <AdminDashboardContent />
    </ThemeProvider>
  );
}

const createStyles = (theme: ThemeTokens) =>
  StyleSheet.create({
    safeArea: {
      flex: 1,
      backgroundColor: theme.colors.background,
    },
    container: {
      flex: 1,
      backgroundColor: theme.colors.background,
      paddingHorizontal: theme.spacing.lg,
      paddingTop: theme.spacing.md,
      paddingBottom: theme.spacing.lg,
      justifyContent: 'space-between',
    },
    header: {
      flexDirection: 'row',
      alignItems: 'center',
      justifyContent: 'space-between',
      paddingBottom: theme.spacing.md,
      borderBottomWidth: 1,
      borderBottomColor: theme.colors.border,
    },
    brandSubtitle: {
      color: theme.colors.textMuted,
      fontSize: 12,
      fontWeight: '500',
      letterSpacing: 0.8,
      marginBottom: theme.spacing.xs,
    },
    headerTitle: {
      color: theme.colors.text,
      fontSize: 20,
      fontWeight: '600',
    },
    nodeBadge: {
      backgroundColor: theme.colors.surface,
      borderWidth: 1,
      borderColor: theme.colors.border,
      borderRadius: theme.radius.control,
      paddingHorizontal: theme.spacing.md,
      paddingVertical: theme.spacing.sm,
    },
    nodeBadgeText: {
      color: theme.colors.text,
      fontSize: 12,
      fontWeight: '600',
      letterSpacing: 0.5,
      fontVariant: ['tabular-nums'],
    },
    sectionLabel: {
      color: theme.colors.textMuted,
      fontSize: 12,
      fontWeight: '600',
      letterSpacing: 0.8,
    },
    vitalsSection: {
      marginTop: theme.spacing.lg,
    },
    vitalsRow: {
      flexDirection: 'row',
      gap: theme.spacing.sm,
      marginTop: theme.spacing.sm,
    },
    vitalCard: {
      flex: 1,
      backgroundColor: theme.colors.surface,
      borderWidth: 1,
      borderColor: theme.colors.border,
      borderRadius: theme.radius.card,
      padding: theme.spacing.lg,
    },
    vitalLabel: {
      color: theme.colors.textMuted,
      fontSize: 12,
      fontWeight: '400',
      marginBottom: theme.spacing.xs,
    },
    vitalValue: {
      color: theme.colors.text,
      fontSize: 16,
      fontWeight: '600',
      fontVariant: ['tabular-nums'],
    },
    vitalValueAccent: {
      color: theme.colors.accent,
    },
    triageSection: {
      flex: 1,
      marginTop: theme.spacing.xl,
      marginBottom: theme.spacing.lg,
    },
    triageHeaderRow: {
      flexDirection: 'row',
      alignItems: 'center',
      justifyContent: 'space-between',
      marginBottom: theme.spacing.sm,
    },
    queueCountText: {
      color: theme.colors.textMuted,
      fontSize: 12,
      fontWeight: '500',
      fontVariant: ['tabular-nums'],
    },
    alertsListContent: {
      gap: theme.spacing.md,
      paddingBottom: theme.spacing.sm,
    },
    alertCard: {
      backgroundColor: theme.colors.surface,
      borderWidth: 1,
      borderColor: theme.colors.border,
      borderRadius: theme.radius.card,
      padding: theme.spacing.lg,
    },
    alertCardTopRow: {
      flexDirection: 'row',
      alignItems: 'center',
      justifyContent: 'space-between',
      marginBottom: theme.spacing.sm,
    },
    categoryWrap: {
      flexDirection: 'row',
      alignItems: 'center',
      gap: theme.spacing.sm,
    },
    accentDot: {
      width: 8,
      height: 8,
      borderRadius: 4,
      backgroundColor: theme.colors.accent,
    },
    alertCategory: {
      color: theme.colors.textMuted,
      fontSize: 12,
      fontWeight: '600',
      letterSpacing: 0.6,
    },
    alertTimestamp: {
      color: theme.colors.textMuted,
      fontSize: 12,
      fontWeight: '400',
      fontVariant: ['tabular-nums'],
    },
    alertTitle: {
      color: theme.colors.text,
      fontSize: 16,
      fontWeight: '600',
      marginBottom: theme.spacing.xs,
    },
    alertSubject: {
      color: theme.colors.text,
      fontSize: 14,
      fontWeight: '500',
      marginBottom: theme.spacing.xs,
    },
    alertMeta: {
      color: theme.colors.textMuted,
      fontSize: 12,
      fontWeight: '400',
      lineHeight: 16,
      marginBottom: theme.spacing.lg,
    },
    actionRow: {
      flexDirection: 'row',
      gap: theme.spacing.sm,
    },
    actionButton: {
      flex: 1,
      minHeight: 44,
      borderRadius: theme.radius.control,
      alignItems: 'center',
      justifyContent: 'center',
      paddingHorizontal: theme.spacing.md,
    },
    actionButtonPrimary: {
      backgroundColor: theme.colors.primary,
    },
    actionButtonNeutral: {
      backgroundColor: 'transparent',
      borderWidth: 1,
      borderColor: theme.colors.border,
    },
    actionButtonDisabled: {
      opacity: 0.5,
    },
    actionButtonTextPrimary: {
      color: theme.colors.text,
      fontSize: 14,
      fontWeight: '600',
    },
    actionButtonTextNeutral: {
      color: theme.colors.text,
      fontSize: 14,
      fontWeight: '500',
    },
    resolvedBanner: {
      minHeight: 44,
      borderRadius: theme.radius.control,
      borderWidth: 1,
      borderColor: theme.colors.border,
      alignItems: 'center',
      justifyContent: 'center',
      paddingHorizontal: theme.spacing.md,
    },
    resolvedText: {
      color: theme.colors.textMuted,
      fontSize: 14,
      fontWeight: '500',
    },
    lockdownContainer: {
      paddingTop: theme.spacing.md,
      borderTopWidth: 1,
      borderTopColor: theme.colors.border,
    },
    lockdownHeaderRow: {
      flexDirection: 'row',
      alignItems: 'center',
      justifyContent: 'space-between',
      marginBottom: theme.spacing.sm,
    },
    lockdownMeta: {
      color: theme.colors.textMuted,
      fontSize: 12,
      fontWeight: '400',
    },
    resetLockdownButton: {
      minHeight: 44,
      paddingHorizontal: theme.spacing.md,
      borderRadius: theme.radius.control,
      borderWidth: 1,
      borderColor: theme.colors.border,
      alignItems: 'center',
      justifyContent: 'center',
    },
    resetLockdownText: {
      color: theme.colors.text,
      fontSize: 12,
      fontWeight: '600',
    },
    sliderTrack: {
      height: 52,
      backgroundColor: theme.colors.surface,
      borderRadius: theme.radius.card,
      borderWidth: 1,
      borderColor: theme.colors.accent,
      padding: TRACK_PADDING,
      justifyContent: 'center',
      position: 'relative',
    },
    sliderTrackLocked: {
      borderColor: theme.colors.border,
    },
    sliderLabel: {
      color: theme.colors.text,
      fontSize: 12,
      fontWeight: '600',
      letterSpacing: 0.8,
      textAlign: 'center',
      paddingHorizontal: THUMB_SIZE + theme.spacing.sm,
    },
    sliderThumb: {
      width: THUMB_SIZE,
      height: THUMB_SIZE,
      borderRadius: theme.radius.control,
      backgroundColor: theme.colors.surface,
      borderWidth: 1,
      borderColor: theme.colors.accent,
      alignItems: 'center',
      justifyContent: 'center',
      position: 'absolute',
      left: TRACK_PADDING,
      top: TRACK_PADDING,
    },
    sliderThumbGlyph: {
      color: theme.colors.accent,
      fontSize: 16,
      fontWeight: '600',
    },
  });
