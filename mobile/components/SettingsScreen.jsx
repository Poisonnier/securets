import React, { useMemo, useState } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity,
  ScrollView, Switch, Alert,
} from 'react-native';
import { Feather } from '@expo/vector-icons';
import { useTheme } from '../src/theme/ThemeContext';

function Row({ icon, label, value, danger, onPress, rightElement, styles }) {
  return (
    <TouchableOpacity
      style={styles.row}
      onPress={onPress}
      disabled={!onPress}
      activeOpacity={onPress ? 0.7 : 1}
    >
      <View style={styles.rowIconWrap}>
        <Feather name={icon} size={18} color={danger ? '#F87171' : '#4063D7'} strokeWidth={1.75} />
      </View>
      <View style={{ flex: 1 }}>
        <Text style={[styles.rowLabel, danger && { color: '#F87171' }]}>{label}</Text>
        {!!value && <Text style={styles.rowValue}>{value}</Text>}
      </View>
      {rightElement ?? (onPress && <Feather name="chevron-right" size={18} color="#64748B" />)}
    </TouchableOpacity>
  );
}

export default function SettingsScreen({ user, onLoginPress, onLogout }) {
  const C = useTheme();
  const styles = useMemo(() => createStyles(C), [C]);
  const isLoggedIn = !!user;
  const [biometricLock, setBiometricLock] = useState(true);
  const [autoLockTimer, setAutoLockTimer] = useState('5 minutes');

  const getInitials = (name) => {
    if (!name) return 'U';
    return name
      .trim()
      .split(/\s+/)
      .slice(0, 2)
      .map((part) => part[0]?.toUpperCase() ?? '')
      .join('');
  };

  const handleCycleAutoLock = () => {
    const options = ['Immediately', '1 minute', '5 minutes', '15 minutes'];
    const nextIndex = (options.indexOf(autoLockTimer) + 1) % options.length;
    setAutoLockTimer(options[nextIndex]);
  };

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
          {/* Profile Section */}
          <Text style={styles.sectionLabel}>Profile</Text>
          <View style={styles.card}>
            <View style={styles.profileRow}>
              <View style={styles.avatar}>
                <Text style={styles.avatarText}>{getInitials(user.name)}</Text>
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.profileName}>{user.name || 'User'}</Text>
                <Text style={styles.profileEmail}>{user.email || 'user@securets.io'}</Text>
              </View>
            </View>
            <View style={styles.divider} />
            <Row
              icon="key"
              label="Change Password"
              value="Update your account password"
              onPress={() => Alert.alert('Change Password', 'Password management is handled via your web security portal.')}
              styles={styles}
            />
          </View>

          {/* Preferences Section */}
          <Text style={styles.sectionLabel}>Preferences</Text>
          <View style={styles.card}>
            <Row
              icon="shield"
              label="Biometric App Lock"
              value="Require Face ID / fingerprint on launch"
              rightElement={
                <Switch
                  value={biometricLock}
                  onValueChange={setBiometricLock}
                  trackColor={{ false: 'rgba(255,255,255,0.12)', true: '#4063D7' }}
                  thumbColor="#FFFFFF"
                />
              }
              styles={styles}
            />
            <View style={styles.divider} />
            <Row
              icon="clock"
              label="Auto-Lock Timer"
              value={autoLockTimer}
              onPress={handleCycleAutoLock}
              styles={styles}
            />
          </View>

          {/* Log Out */}
          <TouchableOpacity style={styles.logoutBtn} onPress={onLogout} activeOpacity={0.8}>
            <Feather name="log-out" size={16} color="#F87171" style={{ marginRight: 8 }} />
            <Text style={styles.logoutText}>Log Out</Text>
          </TouchableOpacity>
        </>
      ) : (
        <>
          {/* Guest card */}
          <View style={styles.guestCard}>
            <View style={styles.avatar}>
              <Feather name="user" size={24} color="#FFFFFF" />
            </View>
            <Text style={styles.guestTitle}>You're not signed in</Text>
            <Text style={styles.guestSub}>
              Sign in to manage your profile, connected apps, and security preferences.
            </Text>
            <TouchableOpacity style={styles.signInBtn} onPress={onLoginPress} activeOpacity={0.85}>
              <Text style={styles.signInText}>Sign In to SecureTS</Text>
            </TouchableOpacity>
          </View>

          {/* Preferences Section */}
          <Text style={styles.sectionLabel}>Preferences</Text>
          <View style={styles.card}>
            <Row
              icon="shield"
              label="Biometric App Lock"
              value="Require Face ID / fingerprint on launch"
              rightElement={
                <Switch
                  value={biometricLock}
                  onValueChange={setBiometricLock}
                  trackColor={{ false: 'rgba(255,255,255,0.12)', true: '#4063D7' }}
                  thumbColor="#FFFFFF"
                />
              }
              styles={styles}
            />
            <View style={styles.divider} />
            <Row
              icon="clock"
              label="Auto-Lock Timer"
              value={autoLockTimer}
              onPress={handleCycleAutoLock}
              styles={styles}
            />
          </View>
        </>
      )}
    </ScrollView>
  );
}

const createStyles = () => StyleSheet.create({
  container: { flex: 1, backgroundColor: '#0E1326' },
  content: { paddingBottom: 48 },

  header: {
    paddingHorizontal: 20, paddingTop: 56, paddingBottom: 16,
    backgroundColor: '#0E1326',
    borderBottomWidth: 1, borderBottomColor: 'rgba(255, 255, 255, 0.08)',
  },
  headerTitle: { fontSize: 22, fontWeight: '800', color: '#F8FAFC' },

  // ── Profile row ──
  profileRow: {
    flexDirection: 'row', alignItems: 'center', gap: 16,
    padding: 18,
  },
  avatar: {
    width: 52, height: 52, borderRadius: 26,
    backgroundColor: '#4063D7',
    alignItems: 'center', justifyContent: 'center',
  },
  avatarText:   { color: '#FFFFFF', fontSize: 18, fontWeight: '800', letterSpacing: 0.5 },
  profileName:  { color: '#F8FAFC', fontSize: 16, fontWeight: '700' },
  profileEmail: { color: '#94A3B8', fontSize: 13, marginTop: 2 },

  // ── Section ──
  sectionLabel: {
    color: '#94A3B8', fontSize: 11, fontWeight: '700',
    textTransform: 'uppercase',
    letterSpacing: 1, marginHorizontal: 20, marginBottom: 8, marginTop: 24,
  },
  card: {
    marginHorizontal: 20, backgroundColor: '#1C2236',
    borderRadius: 14, borderWidth: 1, borderColor: 'rgba(255, 255, 255, 0.08)', overflow: 'hidden',
  },
  row: {
    flexDirection: 'row', alignItems: 'center',
    padding: 16, gap: 12,
  },
  rowIconWrap: {
    width: 36, height: 36, borderRadius: 8,
    backgroundColor: '#0E1326',
    borderWidth: 1, borderColor: 'rgba(64, 99, 215, 0.25)',
    alignItems: 'center', justifyContent: 'center',
  },
  rowLabel:   { color: '#F8FAFC', fontSize: 14, fontWeight: '600' },
  rowValue:   { color: '#94A3B8', fontSize: 12, marginTop: 2 },
  divider:    { height: 1, backgroundColor: 'rgba(255, 255, 255, 0.08)', marginLeft: 64 },

  // ── Log Out ──
  logoutBtn: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginHorizontal: 20, marginTop: 28,
    backgroundColor: 'transparent',
    borderWidth: 1, borderColor: 'rgba(248, 113, 113, 0.35)',
    borderRadius: 12, paddingVertical: 14, paddingHorizontal: 16,
  },
  logoutText: { color: '#F87171', fontSize: 15, fontWeight: '600' },

  // ── Guest ──
  guestCard: {
    margin: 20, padding: 32,
    backgroundColor: '#1C2236', borderRadius: 16,
    borderWidth: 1, borderColor: 'rgba(255, 255, 255, 0.08)',
    alignItems: 'center',
  },
  guestTitle: { color: '#F8FAFC', fontSize: 18, fontWeight: '700', marginTop: 14, marginBottom: 8 },
  guestSub: {
    color: '#94A3B8', fontSize: 13, textAlign: 'center', lineHeight: 20, marginBottom: 22,
  },
  signInBtn: {
    backgroundColor: '#4063D7', borderRadius: 10,
    paddingVertical: 13, paddingHorizontal: 32, alignItems: 'center',
  },
  signInText: { color: '#FFFFFF', fontSize: 14, fontWeight: '700' },
});
