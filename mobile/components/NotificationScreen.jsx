import React, { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import {
    ActivityIndicator, Alert, FlatList, Image, RefreshControl,
    StyleSheet, Text, TouchableOpacity, View,
} from 'react-native';
import { integrationApi } from '../src/api/integrations';
import { createReverbClient, disconnectReverb } from '../src/lib/reverb';
import { getStoredToken } from '../src/api/client';
import { useTheme } from '../src/theme/ThemeContext';

const STATUS_COLORS = {
    pending:  { bg: 'rgba(234,179,8,0.12)',  text: '#FACC15' },
    approved: { bg: 'rgba(34,197,94,0.12)',  text: '#4ADE80' },
    declined: { bg: 'rgba(239,68,68,0.12)',  text: '#F87171' },
    auto:     { bg: 'rgba(148,163,184,0.12)',text: '#94A3B8' },
};

/**
 * NotificationScreen
 * ------------------
 * Listens for real-time login approval requests via:
 *   1. Laravel Reverb WebSocket (primary – instant delivery)
 *   2. 15-second HTTP polling fallback (handles reconnect gaps)
 *
 * Requires the `user` prop from DashboardScreen to set up the private channel.
 */
export default function NotificationScreen({ user }) {
    const COLORS = useTheme();
    const styles = useMemo(() => createStyles(COLORS), [COLORS]);
    const [pending,   setPending]   = useState([]);
    const [history,   setHistory]   = useState([]);
    const [loading,   setLoading]   = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [tab, setTab] = useState('pending'); // 'pending' | 'history'
    const [wsStatus, setWsStatus]   = useState('connecting'); // 'connecting' | 'connected' | 'disconnected'

    const channelRef = useRef(null);

    const load = useCallback(async () => {
        try {
            const [p, h] = await Promise.all([
                integrationApi.pending(),
                integrationApi.history(),
            ]);
            setPending(p.data);
            setHistory(h.data);
        } catch {
            Alert.alert('Error', 'Could not load notifications.');
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    }, []);

    // ── WebSocket subscription (primary real-time path) ──────────────────────
    useEffect(() => {
        if (!user?.id) return;

        let cancelled = false;

        const connectWebSocket = async () => {
            try {
                const token = await getStoredToken();
                if (!token || cancelled) return;

                const pusher = createReverbClient(token);

                pusher.connection.bind('connected', () => {
                    if (!cancelled) setWsStatus('connected');
                });
                pusher.connection.bind('disconnected', () => {
                    if (!cancelled) setWsStatus('disconnected');
                });
                pusher.connection.bind('error', () => {
                    if (!cancelled) setWsStatus('disconnected');
                });

                // Subscribe to the private channel for this user
                const channel = pusher.subscribe(`private-user.${user.id}`);
                channelRef.current = channel;

                // Listen for the LoginAttemptBroadcast event
                channel.bind('LoginAttemptBroadcast', (data) => {
                    if (cancelled) return;
                    if (data.user_id && Number(data.user_id) !== Number(user.id)) return;

                    const incomingId = data.id ?? data.event_id;
                    // Prepend the new pending event to the list instantly
                    setPending((prev) => {
                        const alreadyExists = prev.some((e) => (e.id ?? e.event_id) === incomingId);
                        if (alreadyExists) return prev;
                        return [{ ...data, id: incomingId }, ...prev];
                    });
                });
            } catch (err) {
                console.warn('[Reverb] WebSocket connection failed:', err?.message ?? err);
                if (!cancelled) setWsStatus('disconnected');
            }
        };

        connectWebSocket();

        return () => {
            cancelled = true;
            disconnectReverb();
            channelRef.current = null;
            setWsStatus('connecting');
        };
    }, [user?.id]);

    // ── Fallback polling (15 s) — catches events when WS is down ─────────────
    useEffect(() => {
        load();
        const interval = setInterval(() => {
            integrationApi.pending().then(r => setPending(r.data)).catch(() => {});
        }, 15000);
        return () => clearInterval(interval);
    }, [load]);

    const [respondingId, setRespondingId] = useState(null);

    const respond = async (event, decision) => {
        const eventId = event?.id ?? event?.event_id;
        if (!eventId || respondingId) return;

        setRespondingId(eventId);
        // Optimistically remove from pending and prepend to history
        setPending(prev => prev.filter(e => (e.id ?? e.event_id) !== eventId));
        setHistory(prev => [{ ...event, id: eventId, status: decision }, ...prev]);

        try {
            await integrationApi.respond(eventId, decision);
            const h = await integrationApi.history();
            setHistory(h.data);
        } catch {
            load();
            Alert.alert('Error', 'Could not submit response.');
        } finally {
            setRespondingId(null);
        }
    };

    const handleArchive = async (eventId) => {
        try {
            await integrationApi.archive(eventId);
            setHistory(prev => prev.filter(e => e.id !== eventId));
        } catch {
            Alert.alert('Error', 'Could not archive notification.');
        }
    };

    const handleDelete = async (eventId) => {
        Alert.alert(
            'Delete Notification',
            'Are you sure you want to delete this notification permanently?',
            [
                { text: 'Cancel', style: 'cancel' },
                {
                    text: 'Delete',
                    style: 'destructive',
                    onPress: async () => {
                        try {
                            await integrationApi.delete(eventId);
                            setHistory(prev => prev.filter(e => e.id !== eventId));
                        } catch {
                            Alert.alert('Error', 'Could not delete notification.');
                        }
                    }
                }
            ]
        );
    };

    const renderPending = ({ item: e }) => (
        <View style={styles.approvalCard}>
            <View style={styles.approvalTop}>
                <View style={styles.approvalFavicon}>
                    <Image
                        source={{ uri: e.integration?.favicon_url || 'https://www.google.com/s2/favicons?domain=example.com&sz=64' }}
                        style={{ width: 28, height: 28 }}
                    />
                </View>
                <View style={{ flex: 1 }}>
                    <Text style={styles.approvalApp}>{e.integration?.name ?? 'Unknown App'}</Text>
                    <Text style={styles.approvalMeta}>
                        {e.device ? e.device + ' · ' : ''}{new Date(e.created_at).toLocaleString()}
                    </Text>
                    {e.ip_address && <Text style={styles.approvalMeta}>IP: {e.ip_address}</Text>}
                </View>
            </View>
            <View style={styles.approvalActions}>
                <TouchableOpacity
                    style={styles.btnApprove}
                    disabled={respondingId === (e.id ?? e.event_id)}
                    onPress={() => respond(e, 'approved')}
                >
                    <Text style={styles.btnApproveText}>✓ Approve</Text>
                </TouchableOpacity>
                <TouchableOpacity
                    style={styles.btnDecline}
                    disabled={respondingId === (e.id ?? e.event_id)}
                    onPress={() => respond(e, 'declined')}
                >
                    <Text style={styles.btnDeclineText}>✕ Decline</Text>
                </TouchableOpacity>
            </View>
        </View>
    );

    const renderHistory = ({ item: e }) => {
        const color = STATUS_COLORS[e.status] ?? STATUS_COLORS.auto;
        return (
            <View style={styles.historyCard}>
                <View style={styles.historyRow}>
                    <View style={styles.historyFavicon}>
                        <Image
                            source={{ uri: e.integration?.favicon_url || 'https://www.google.com/s2/favicons?domain=example.com&sz=64' }}
                            style={{ width: 24, height: 24 }}
                        />
                    </View>
                    <View style={{ flex: 1 }}>
                        <Text style={styles.historyApp}>{e.integration?.name ?? '–'}</Text>
                        <Text style={styles.historyMeta}>{e.device ?? ''} {e.ip_address ? '· ' + e.ip_address : ''}</Text>
                        <Text style={styles.historyTime}>{new Date(e.created_at).toLocaleString()}</Text>
                    </View>
                    <View style={[styles.badge, { backgroundColor: color.bg }]}>
                        <Text style={[styles.badgeText, { color: color.text }]}>{e.status}</Text>
                    </View>
                </View>
                <View style={styles.historyActions}>
                    <TouchableOpacity style={styles.historyActionBtn} onPress={() => handleArchive(e.id)}>
                        <Text style={styles.historyActionText}>📦 Archive</Text>
                    </TouchableOpacity>
                    <TouchableOpacity style={[styles.historyActionBtn, { borderLeftWidth: 1, borderLeftColor: COLORS.border }]} onPress={() => handleDelete(e.id)}>
                        <Text style={[styles.historyActionText, { color: COLORS.danger }]}>🗑️ Delete</Text>
                    </TouchableOpacity>
                </View>
            </View>
        );
    };

    return (
        <View style={styles.container}>
            <View style={styles.header}>
                <Text style={styles.headerTitle}>Notifications</Text>
                {pending.length > 0 && (
                    <View style={styles.badgeDot}>
                        <Text style={styles.badgeDotText}>{pending.length}</Text>
                    </View>
                )}
                <View style={{ flex: 1 }} />
                <TouchableOpacity
                    style={styles.refreshBtn}
                    onPress={() => { setRefreshing(true); load(); }}
                >
                    <Text style={styles.refreshBtnText}>Refresh</Text>
                </TouchableOpacity>
            </View>

            {/* Tabs */}
            <View style={styles.tabs}>
                <TouchableOpacity style={[styles.tabBtn, tab === 'pending' && styles.tabBtnActive]} onPress={() => setTab('pending')}>
                    <Text style={[styles.tabBtnText, tab === 'pending' && styles.tabBtnTextActive]}>
                        Approvals {pending.length > 0 ? `(${pending.length})` : ''}
                    </Text>
                </TouchableOpacity>
                <TouchableOpacity style={[styles.tabBtn, tab === 'history' && styles.tabBtnActive]} onPress={() => setTab('history')}>
                    <Text style={[styles.tabBtnText, tab === 'history' && styles.tabBtnTextActive]}>Login History</Text>
                </TouchableOpacity>
            </View>

            {loading ? (
                <View style={styles.centered}>
                    <ActivityIndicator color={COLORS.blue} size="large" />
                </View>
            ) : tab === 'pending' ? (
                pending.length === 0 ? (
                    <View style={styles.empty}>
                        <Text style={{ fontSize: 40, marginBottom: 12 }}>✅</Text>
                        <Text style={styles.emptyTitle}>No pending approvals</Text>
                        <Text style={styles.emptyText}>You'll be notified when a login attempt needs your approval.</Text>
                    </View>
                ) : (
                    <FlatList
                        data={pending}
                        keyExtractor={e => String(e.id)}
                        renderItem={renderPending}
                        contentContainerStyle={{ padding: 16, paddingBottom: 40 }}
                        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} tintColor={COLORS.blue} />}
                    />
                )
            ) : (
                history.length === 0 ? (
                    <View style={styles.empty}>
                        <Text style={styles.emptyTitle}>No login history</Text>
                        <Text style={styles.emptyText}>Login events from your integrated apps will appear here.</Text>
                    </View>
                ) : (
                    <FlatList
                        data={history}
                        keyExtractor={e => String(e.id)}
                        renderItem={renderHistory}
                        contentContainerStyle={{ padding: 16, paddingBottom: 40 }}
                        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(); }} tintColor={COLORS.blue} />}
                    />
                )
            )}
        </View>
    );
}

const createStyles = (COLORS) => StyleSheet.create({
    container: { flex: 1, backgroundColor: COLORS.bg },
    header: {
        flexDirection: 'row', alignItems: 'center', gap: 10,
        paddingHorizontal: 20, paddingTop: 56, paddingBottom: 16,
        backgroundColor: COLORS.surface,
        borderBottomWidth: 1, borderBottomColor: COLORS.border,
    },
    headerTitle: { fontSize: 22, fontWeight: '800', color: COLORS.text },
    badgeDot: {
        backgroundColor: '#EF4444', borderRadius: 10,
        paddingHorizontal: 7, paddingVertical: 2,
    },
    badgeDotText: { color: '#fff', fontSize: 12, fontWeight: '700' },
    wsIndicator: { fontSize: 11, fontWeight: '600', marginRight: 6 },
    refreshBtn: {
        backgroundColor: COLORS.subtle,
        paddingHorizontal: 12,
        paddingVertical: 6,
        borderRadius: 8,
    },
    refreshBtnText: {
        color: COLORS.text,
        fontSize: 13,
        fontWeight: '600',
    },
    tabs: { flexDirection: 'row', padding: 12, gap: 8 },
    tabBtn: { flex: 1, paddingVertical: 9, borderRadius: 8, alignItems: 'center', backgroundColor: COLORS.subtle },
    tabBtnActive: { backgroundColor: 'rgba(61,90,241,0.15)' },
    tabBtnText: { color: COLORS.muted, fontSize: 13, fontWeight: '600' },
    tabBtnTextActive: { color: COLORS.blueLight },
    centered: { flex: 1, alignItems: 'center', justifyContent: 'center' },
    empty: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: 40 },
    emptyTitle: { color: COLORS.text, fontSize: 18, fontWeight: '700', marginBottom: 8 },
    emptyText:  { color: COLORS.muted, fontSize: 14, textAlign: 'center' },

    approvalCard: {
        backgroundColor: COLORS.card,
        borderWidth: 1, borderColor: 'rgba(234,179,8,0.25)',
        borderRadius: 14, padding: 16, marginBottom: 12,
    },
    approvalTop:    { flexDirection: 'row', alignItems: 'flex-start', gap: 12, marginBottom: 14 },
    approvalFavicon:{ width: 40, height: 40, borderRadius: 8, backgroundColor: COLORS.subtle, alignItems: 'center', justifyContent: 'center' },
    approvalApp:    { color: COLORS.text, fontSize: 15, fontWeight: '600' },
    approvalMeta:  { color: COLORS.muted, fontSize: 12, marginTop: 2 },
    approvalActions:{ flexDirection: 'row', gap: 8 },
    btnApprove: { flex: 1, backgroundColor: 'rgba(34,197,94,0.12)', borderWidth: 1, borderColor: 'rgba(34,197,94,0.25)', borderRadius: 8, padding: 11, alignItems: 'center' },
    btnApproveText: { color: '#4ADE80', fontWeight: '700', fontSize: 14 },
    btnDecline: { flex: 1, backgroundColor: 'rgba(239,68,68,0.12)', borderWidth: 1, borderColor: 'rgba(239,68,68,0.25)', borderRadius: 8, padding: 11, alignItems: 'center' },
    btnDeclineText: { color: '#F87171', fontWeight: '700', fontSize: 14 },
    historyCard: {
        backgroundColor: COLORS.card,
        borderRadius: 12,
        borderWidth: 1,
        borderColor: COLORS.border,
        marginBottom: 10,
        overflow: 'hidden',
    },
    historyRow: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: 12,
        padding: 14,
    },
    historyFavicon: { width: 36, height: 36, borderRadius: 8, backgroundColor: COLORS.subtle, alignItems: 'center', justifyContent: 'center' },
    historyApp:  { color: COLORS.text, fontSize: 14, fontWeight: '600' },
    historyMeta: { color: COLORS.muted, fontSize: 12, marginTop: 2 },
    historyTime: { color: COLORS.muted, fontSize: 11, marginTop: 2 },
    historyActions: {
        flexDirection: 'row',
        borderTopWidth: 1,
        borderTopColor: COLORS.border,
        backgroundColor: 'rgba(255,255,255,0.02)',
    },
    historyActionBtn: {
        flex: 1,
        paddingVertical: 10,
        alignItems: 'center',
        justifyContent: 'center',
    },
    historyActionText: {
        fontSize: 12,
        fontWeight: '600',
        color: COLORS.muted,
    },
    badge: { borderRadius: 10, paddingHorizontal: 10, paddingVertical: 4 },
    badgeText: { fontSize: 11, fontWeight: '700' },
});
