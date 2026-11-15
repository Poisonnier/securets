import React, { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { Alert, Pressable, RefreshControl, SafeAreaView, ScrollView, StyleSheet, Text, View } from 'react-native';
import { AdminOverview, getAdminApiBaseUrl, getOverview, restoreAccess, triggerLockdown } from '../api/client';
import { ThemeProvider, ThemeTokens, useTheme } from '../theme/ThemeContext';

function Key({ label, onPress, tone = 'neutral' }: { label: string; onPress: () => void; tone?: 'danger' | 'primary' | 'neutral' }) {
  const theme = useTheme();
  const styles = useMemo(() => createStyles(theme), [theme]);
  return <Pressable accessibilityRole="button" accessibilityLabel={label} onPress={onPress} style={({ pressed }) => [styles.key, tone === 'danger' && styles.keyDanger, tone === 'primary' && styles.keyPrimary, pressed && styles.keyPressed]}><Text style={styles.keyText}>{label}</Text></Pressable>;
}

function DashboardContent() {
  const theme = useTheme();
  const styles = useMemo(() => createStyles(theme), [theme]);
  const [overview, setOverview] = useState<AdminOverview | null>(null);
  const [refreshing, setRefreshing] = useState(false);
  const [busy, setBusy] = useState(false);
  const [message, setMessage] = useState('');
  const loadingRef = useRef<Promise<void> | null>(null);
  const load = useCallback(async () => {
    if (loadingRef.current) return loadingRef.current;

    const request = (async () => {
      try {
        const response = await getOverview();
        setOverview(response.data);
        setMessage('');
      } catch (_) {
        // Keep the last successful payload visible during a transient timeout.
        setMessage(`Admin server unavailable: ${getAdminApiBaseUrl()}`);
      } finally {
        loadingRef.current = null;
      }
    })();

    loadingRef.current = request;
    return request;
  }, []);
  useEffect(() => { load(); }, [load]);
  const run = async (operation: () => Promise<unknown>, success: string) => { setBusy(true); try { await operation(); setMessage(success); await load(); } catch (error: any) { setMessage(error?.response?.data?.message ?? 'Action could not be completed.'); } finally { setBusy(false); } };
  const confirm = (title: string, body: string, operation: () => Promise<unknown>, success: string) => Alert.alert(title, body, [{ text: 'Cancel', style: 'cancel' }, { text: 'Confirm', style: 'destructive', onPress: () => run(operation, success) }]);
  const onRefresh = async () => { setRefreshing(true); await load(); setRefreshing(false); };
  const locked = overview?.lockdown.active ?? false;
  const devices = overview?.devices ?? [];
  const compromisedDevices = devices.filter((device) => !['active', 'recovered'].includes(device.state));
  return <SafeAreaView style={styles.safeArea}><ScrollView contentContainerStyle={styles.content} refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={theme.colors.accent} />}>
    <View style={styles.header}><View><Text style={styles.eyebrow}>SECURETHREATSHIELD / ADMIN</Text><Text style={styles.title}>Emergency Keypad</Text></View><View style={[styles.livePill, locked && styles.lockedPill]}><View style={styles.liveDot} /><Text style={styles.liveText}>{locked ? 'HALTED' : (overview?.metrics.health ?? 'CONNECTING').toUpperCase()}</Text></View></View>
    <View style={styles.metricsRow}><View style={styles.metric}><Text style={styles.metricLabel}>USERS</Text><Text style={styles.metricValue}>{overview?.metrics.users ?? '—'}</Text></View><View style={styles.metric}><Text style={styles.metricLabel}>SESSIONS</Text><Text style={styles.metricValue}>{overview?.metrics.sessions ?? '—'}</Text></View><View style={styles.metric}><Text style={styles.metricLabel}>DEVICES</Text><Text style={styles.metricValue}>{overview?.metrics.devices ?? '—'}</Text></View></View>
    <View style={[styles.lockdownCard, locked && styles.lockdownCardActive]}><View style={styles.sectionHeading}><View><Text style={styles.sectionTitle}>EMERGENCY LOCKDOWN</Text><Text style={styles.sectionHint}>{locked ? 'System traffic and authentication are halted' : 'Freeze the system during a suspected breach'}</Text></View><Text style={styles.warningGlyph}>{locked ? '■' : '!'}</Text></View>{locked ? <Key label="Restore normal access" tone="primary" onPress={() => confirm('Deactivate emergency lockdown?', 'Re-enable normal authentication and application traffic across the system.', restoreAccess, 'Emergency lockdown deactivated.')} /> : <Key label="ACTIVATE EMERGENCY LOCKDOWN" tone="danger" onPress={() => confirm('Activate emergency lockdown?', 'This halts authentication and application traffic, and revokes active sessions and tokens across the system.', triggerLockdown, 'Emergency lockdown activated.')} />}</View>
    <View style={styles.alertSection}><View style={styles.sectionHeading}><View><Text style={styles.sectionTitle}>CURRENT COMPROMISE ALERTS</Text><Text style={styles.sectionHint}>Live device records requiring admin attention</Text></View><Text style={styles.count}>{compromisedDevices.length}</Text></View>{compromisedDevices.length ? compromisedDevices.map((device) => <View key={device.id} style={styles.alertCard}><View style={styles.alertHeader}><Text style={styles.alertDevice}>{device.name}</Text><Text style={styles.alertStatus}>{device.status}</Text></View><Text style={styles.alertOwner}>{device.owner}</Text><Text style={styles.alertMeta}>{device.location}</Text></View>) : <View style={styles.alertEmpty}><Text style={styles.emptyTitle}>No compromised devices reported</Text><Text style={styles.emptyHint}>This status is based on the latest system database response.</Text></View>}</View>{!!message && devices.length > 0 && <Text style={styles.message}>{message}</Text>}{busy && <Text style={styles.message}>Sending command…</Text>}
  </ScrollView></SafeAreaView>;
}

export default function AdminDashboard() { return <ThemeProvider><DashboardContent /></ThemeProvider>; }

const createStyles = (theme: ThemeTokens) => StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: theme.colors.background }, content: { padding: theme.spacing.lg, paddingBottom: 32 }, header: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingBottom: theme.spacing.lg, borderBottomWidth: 1, borderBottomColor: theme.colors.border }, eyebrow: { color: theme.colors.textMuted, fontSize: 11, fontWeight: '600', letterSpacing: 1 }, title: { color: theme.colors.text, fontSize: 25, fontWeight: '700', marginTop: 5 }, livePill: { flexDirection: 'row', alignItems: 'center', gap: 6, borderColor: theme.colors.border, borderWidth: 1, borderRadius: 20, paddingHorizontal: 10, paddingVertical: 8 }, lockedPill: { borderColor: '#ef4444' }, liveDot: { width: 7, height: 7, borderRadius: 4, backgroundColor: theme.colors.accent }, liveText: { color: theme.colors.text, fontSize: 10, fontWeight: '700', letterSpacing: .7 }, metricsRow: { flexDirection: 'row', gap: 8, marginVertical: 18 }, metric: { flex: 1, backgroundColor: theme.colors.surface, borderColor: theme.colors.border, borderWidth: 1, borderRadius: theme.radius.card, padding: 13 }, metricLabel: { color: theme.colors.textMuted, fontSize: 10, fontWeight: '600', letterSpacing: .8 }, metricValue: { color: theme.colors.text, fontSize: 22, fontWeight: '700', marginTop: 7 }, lockdownCard: { backgroundColor: '#211d2d', borderColor: '#8b4b61', borderWidth: 1, borderRadius: 14, padding: 16, marginBottom: 24 }, lockdownCardActive: { backgroundColor: '#24202a', borderColor: '#ef4444' }, sectionHeading: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 10 }, sectionTitle: { color: theme.colors.text, fontSize: 12, fontWeight: '700', letterSpacing: .9 }, sectionHint: { color: theme.colors.textMuted, fontSize: 12, marginTop: 4 }, warningGlyph: { color: '#ff8c8c', fontSize: 20, fontWeight: '800' }, count: { color: theme.colors.accent, fontSize: 13, fontWeight: '700' }, key: { minHeight: 48, flex: 1, borderColor: theme.colors.border, borderWidth: 1, borderRadius: 9, alignItems: 'center', justifyContent: 'center', paddingHorizontal: 10, backgroundColor: 'rgba(255,255,255,.03)' }, keyPrimary: { backgroundColor: theme.colors.primary, borderColor: theme.colors.primary }, keyDanger: { backgroundColor: '#7f2634', borderColor: '#c64b5b' }, keyPressed: { opacity: .65, transform: [{ scale: .98 }] }, keyText: { color: theme.colors.text, fontSize: 13, fontWeight: '700', textAlign: 'center' }, deviceCard: { backgroundColor: theme.colors.surface, borderColor: theme.colors.border, borderWidth: 1, borderRadius: 13, padding: 14 }, deviceHeading: { flexDirection: 'row', alignItems: 'center' }, deviceIcon: { width: 38, height: 38, borderRadius: 10, backgroundColor: '#273454', alignItems: 'center', justifyContent: 'center' }, deviceIconText: { color: theme.colors.accent, fontSize: 19 }, deviceCopy: { flex: 1, marginLeft: 10 }, deviceName: { color: theme.colors.text, fontSize: 14, fontWeight: '700' }, deviceOwner: { color: theme.colors.textMuted, fontSize: 12, marginTop: 3 }, status: { fontSize: 10, fontWeight: '700', textTransform: 'uppercase' }, statusGood: { color: '#54d58b' }, statusWarn: { color: '#ffb454' }, deviceMeta: { color: theme.colors.textMuted, fontSize: 11, marginTop: 12, marginBottom: 12 }, deviceActions: { flexDirection: 'row', gap: 7 }, empty: { borderColor: theme.colors.border, borderWidth: 1, borderRadius: 12, padding: 22, alignItems: 'center' }, emptyTitle: { color: theme.colors.text, fontSize: 14, fontWeight: '600' }, emptyHint: { color: theme.colors.textMuted, fontSize: 12, marginTop: 6, textAlign: 'center' }, alertSection: { marginTop: 22 }, alertCard: { backgroundColor: '#2b1d27', borderColor: '#8b4b61', borderWidth: 1, borderRadius: 12, padding: 14, marginBottom: 8 }, alertHeader: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }, alertDevice: { color: theme.colors.text, fontSize: 14, fontWeight: '700', flex: 1 }, alertStatus: { color: '#ffb454', fontSize: 10, fontWeight: '800', textTransform: 'uppercase' }, alertOwner: { color: theme.colors.textMuted, fontSize: 12, marginTop: 5 }, alertMeta: { color: theme.colors.textMuted, fontSize: 11, marginTop: 4 }, alertEmpty: { borderColor: theme.colors.border, borderWidth: 1, borderRadius: 12, padding: 18, alignItems: 'center' }, quickRow: { flexDirection: 'row', marginTop: 16 }, message: { color: theme.colors.textMuted, fontSize: 12, textAlign: 'center', marginTop: 12 },
});
