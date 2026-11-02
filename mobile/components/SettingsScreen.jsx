import React, { useMemo } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity,
  ScrollView, Image,
} from 'react-native';
import { useTheme } from '../src/theme/ThemeContext';

function Row({ icon, label, value, danger, onPress, styles }) {
  return (
    <TouchableOpacity
      style={styles.row}
      onPress={onPress}
      activeOpacity={onPress ? 0.7 : 1}
    >
      <Text style={styles.rowIcon}>{icon}</Text>
      <View style={{ flex: 1 }}>
        <Text style={[styles.rowLabel, danger && { color: '#F87171' }]}>{label}</Text>
        {!!value && <Text style={styles.rowValue}>{value}</Text>}
      </View>
      {onPress && <Text style={styles.rowChevron}>›</Text>}
    </TouchableOpacity>
  );
}

export default function SettingsScreen({ user, onLoginPress, onLogout, onOpenIntegrations, onOpenApprovals }) {
  const C = useTheme();
  const styles = useMemo(() => createStyles(C), [C]);
  const isLoggedIn = !!user;

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={styles.content}
      showsVerticalScrollIndicator={false}
    >
      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Settings</Text>
      </View>

      {isLoggedIn ? (
        <>
          {/* Profile card */}
          <View style={styles.profileCard}>
            <View style={styles.avatar}>
              <Text style={styles.avatarText}>
                {user.name ? user.name[0].toUpperCase() : '?'}
              </Text>
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.profileName}>{user.name}</Text>
              <Text style={styles.profileEmail}>{user.email}</Text>
              <View style={styles.badge}>
                <Text style={styles.badgeText}>🔐 Authenticated</Text>
              </View>
            </View>
          </View>

          {/* Account section */}
          <Text style={styles.sectionLabel}>ACCOUNT</Text>
          <View style={styles.card}>
            <Row icon="🔗" label="Integration Hub" value="Manage connected apps" onPress={onOpenIntegrations} styles={styles} />
            <View style={styles.divider} />
            <Row icon="🔔" label="Login Approvals" value="Approve / decline logins on this device" onPress={onOpenApprovals} styles={styles} />
          </View>

          {/* App section */}
          <Text style={styles.sectionLabel}>APP</Text>
          <View style={styles.card}>
            <Row icon="🛡️" label="SecureTS ThreatShield" value="Version 1.0.0" styles={styles} />
            <View style={styles.divider} />
            <Row icon="🌐" label="Backend" value="Laravel + Supabase" styles={styles} />
          </View>

          {/* Logout */}
          <TouchableOpacity style={styles.logoutBtn} onPress={onLogout} activeOpacity={0.8}>
            <Text style={styles.logoutText}>⏻  Sign Out</Text>
          </TouchableOpacity>
        </>
      ) : (
        <>
          {/* Guest card */}
          <View style={styles.guestCard}>
            <Text style={{ fontSize: 52, marginBottom: 16 }}>👤</Text>
            <Text style={styles.guestTitle}>You're not signed in</Text>
            <Text style={styles.guestSub}>
              Sign in to access your Integration Hub, TOTP codes,
              and login approval notifications.
            </Text>
            <TouchableOpacity style={styles.signInBtn} onPress={onLoginPress} activeOpacity={0.85}>
              <Text style={styles.signInText}>Sign In to SecureTS</Text>
            </TouchableOpacity>
          </View>

          {/* App info even for guests */}
          <Text style={styles.sectionLabel}>APP</Text>
          <View style={styles.card}>
            <Row icon="🛡️" label="SecureTS ThreatShield" value="Version 1.0.0" styles={styles} />
            <View style={styles.divider} />
            <Row icon="🌐" label="Backend" value="Laravel + Supabase" styles={styles} />
          </View>
        </>
      )}
    </ScrollView>
  );
}

const createStyles = (C) => StyleSheet.create({
  container: { flex: 1, backgroundColor: C.bg },
  content: { paddingBottom: 48 },

  header: {
    paddingHorizontal: 20, paddingTop: 56, paddingBottom: 16,
    backgroundColor: C.surface,
    borderBottomWidth: 1, borderBottomColor: C.border,
  },
  headerTitle: { fontSize: 22, fontWeight: '800', color: C.text },

  // ── Profile card ──
  profileCard: {
    flexDirection: 'row', alignItems: 'center', gap: 16,
    margin: 20, padding: 20,
    backgroundColor: C.card, borderRadius: 16, borderWidth: 1, borderColor: C.border,
  },
  avatar: {
    width: 56, height: 56, borderRadius: 28,
    backgroundColor: C.blue,
    alignItems: 'center', justifyContent: 'center',
  },
  avatarText:   { color: '#fff', fontSize: 22, fontWeight: '800' },
  profileName:  { color: C.text, fontSize: 16, fontWeight: '700' },
  profileEmail: { color: C.muted, fontSize: 13, marginTop: 2 },
  badge: {
    marginTop: 6, alignSelf: 'flex-start',
    backgroundColor: 'rgba(61,90,241,0.15)',
    borderRadius: 20, paddingHorizontal: 10, paddingVertical: 3,
  },
  badgeText: { color: '#818CF8', fontSize: 11, fontWeight: '600' },

  // ── Section ──
  sectionLabel: {
    color: C.muted, fontSize: 11, fontWeight: '700',
    letterSpacing: 1, marginHorizontal: 20, marginBottom: 8, marginTop: 20,
  },
  card: {
    marginHorizontal: 20, backgroundColor: C.card,
    borderRadius: 14, borderWidth: 1, borderColor: C.border, overflow: 'hidden',
  },
  row: {
    flexDirection: 'row', alignItems: 'center',
    padding: 16, gap: 12,
  },
  rowIcon:    { fontSize: 20 },
  rowLabel:   { color: C.text, fontSize: 14, fontWeight: '600' },
  rowValue:   { color: C.muted, fontSize: 12, marginTop: 2 },
  rowChevron: { color: C.muted, fontSize: 20 },
  divider:    { height: 1, backgroundColor: C.border, marginLeft: 52 },

  // ── Logout ──
  logoutBtn: {
    margin: 20, marginTop: 28,
    backgroundColor: 'rgba(239,68,68,0.1)',
    borderWidth: 1, borderColor: 'rgba(239,68,68,0.25)',
    borderRadius: 12, padding: 16, alignItems: 'center',
  },
  logoutText: { color: '#F87171', fontSize: 15, fontWeight: '700' },

  // ── Guest ──
  guestCard: {
    margin: 20, padding: 32,
    backgroundColor: C.card, borderRadius: 20,
    borderWidth: 1, borderColor: C.border,
    alignItems: 'center',
  },
  guestTitle: { color: C.text, fontSize: 20, fontWeight: '800', marginBottom: 10 },
  guestSub: {
    color: C.muted, fontSize: 14, textAlign: 'center', lineHeight: 22, marginBottom: 24,
  },
  signInBtn: {
    backgroundColor: C.blue, borderRadius: 12,
    paddingVertical: 14, paddingHorizontal: 32, alignItems: 'center',
  },
  signInText: { color: '#fff', fontSize: 15, fontWeight: '700' },
});
