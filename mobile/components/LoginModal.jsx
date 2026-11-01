import React, { useState } from 'react';
import {
  View, Text, StyleSheet, TouchableOpacity,
  TextInput, ActivityIndicator, Alert, Platform, Modal,
  KeyboardAvoidingView, ScrollView,
} from 'react-native';
import Svg, { Path, Circle } from 'react-native-svg';
import API, { API_BASE_URL, AUTH_SESSION_LABEL, setStoredToken } from '../src/api/client';

const C = {
  dark: '#0F172A', mid: '#1E293B', blue: '#3D5AF1',
  border: 'rgba(255,255,255,0.1)', text: '#F8FAFC', muted: '#94A3B8',
};

/**
 * LoginModal – floating bottom-sheet that can be shown over any screen.
 * Props:
 *   visible        – boolean
 *   onClose        – () => void
 *   onLoginSuccess – (user) => void
 */
export default function LoginModal({ visible, onClose, onLoginSuccess }) {
  const [email,    setEmail]    = useState('');
  const [password, setPassword] = useState('');
  const [loading,  setLoading]  = useState(false);
  const [error,    setError]    = useState('');
  const [showPw,   setShowPw]   = useState(false);

  const handleLogin = async () => {
    if (!email.trim() || !password) {
      setError('Please enter your email and password.');
      return;
    }
    setLoading(true);
    setError('');
    try {
      const res = await API.post('/mobile/login', {
        email: email.trim(),
        password,
        device_name: `${Platform.OS}_mobile_app`,
      });
      if (res.data?.token) {
        await setStoredToken(res.data.token, { expiresAt: res.data.expires_at });

        // Clear fields BEFORE success callback
        setEmail('');
        setPassword('');
        setError('');

        // Propagate user data up — push token registration is handled
        // centrally in App.jsx handleLoginSuccess via notifications.js
        onLoginSuccess?.(res.data.user);
      }
    } catch (e) {
      console.error('LoginModal Error:', e.response?.data || e.message);

      const status = e?.response?.status;
      let msg;

      if (status === 422 || status === 401) {
        // The request reached the API and it rejected the credentials.
        msg = e.response?.data?.message || e.response?.data?.errors?.email?.[0] || 'Incorrect email or password.';
      } else if (status >= 500) {
        msg = 'SecureTS is temporarily unavailable (server error). Please try again in a moment.';
      } else if (status) {
        msg = `Sign-in failed (HTTP ${status}). Please try again.`;
      } else {
        // No response at all: the request never reached the API.
        msg = `Cannot reach SecureTS at ${API_BASE_URL}. Check your connection and API URL.`;
      }

      setError(msg);
    } finally {
      setLoading(false);
    }
  };


  return (
    <Modal
      visible={visible}
      transparent
      animationType="slide"
      onRequestClose={onClose}
    >
      <KeyboardAvoidingView
        style={styles.backdrop}
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      >
        {/* Tap backdrop to dismiss */}
        <TouchableOpacity style={StyleSheet.absoluteFill} activeOpacity={1} onPress={onClose} />

        <View style={styles.sheet}>
          {/* Handle bar */}
          <View style={styles.handle} />

          {/* Shield icon */}
          <View style={styles.logoWrap}>
            <Svg width="48" height="48" viewBox="0 0 64 64" fill="none">
              <Path
                d="M32 6L10 16V30C10 44.4 19.4 57.6 32 62C44.6 57.6 54 44.4 54 30V16L32 6Z"
                fill="#3D5AF1"
              />
              <Circle cx="32" cy="30" r="10" fill="#fff" opacity="0.9" />
            </Svg>
            <Text style={styles.title}>Sign In to SecureTS</Text>
            <Text style={styles.subtitle}>Required to access your integrations</Text>
          </View>

          {/* Error */}
          {!!error && <Text style={styles.errorText}>{error}</Text>}

          {/* Email */}
          <Text style={styles.label}>EMAIL</Text>
          <TextInput
            style={styles.input}
            placeholder="you@example.com"
            placeholderTextColor={C.muted}
            value={email}
            onChangeText={setEmail}
            autoCapitalize="none"
            autoCorrect={false}
            keyboardType="email-address"
            textContentType="emailAddress"
            importantForAutofill="yes"
            editable={!loading}
          />

          {/* Password */}
          <Text style={styles.label}>PASSWORD</Text>
          <View style={styles.pwRow}>
            <TextInput
              style={[styles.input, { flex: 1, marginBottom: 0 }]}
              placeholder="••••••••"
              placeholderTextColor={C.muted}
              value={password}
              onChangeText={setPassword}
              secureTextEntry={!showPw}
              autoCorrect={false}
              textContentType="password"
              importantForAutofill="yes"
              editable={!loading}
            />
            <TouchableOpacity style={styles.eyeBtn} onPress={() => setShowPw(v => !v)}>
              <Text style={styles.eyeText}>{showPw ? '🙈' : '👁️'}</Text>
            </TouchableOpacity>
          </View>

          {/* Submit */}
          <TouchableOpacity
            style={[styles.btn, loading && styles.btnDisabled]}
            onPress={handleLogin}
            disabled={loading}
            activeOpacity={0.85}
          >
            {loading
              ? <ActivityIndicator color="#fff" size="small" />
              : <Text style={styles.btnText}>Sign In</Text>
            }
          </TouchableOpacity>

          <Text style={styles.sessionNote}>
            Your SecureTS session stays signed in for up to {AUTH_SESSION_LABEL} after the app closes.
          </Text>

          {/* Cancel */}
          <TouchableOpacity style={styles.cancelBtn} onPress={onClose}>
            <Text style={styles.cancelText}>Cancel</Text>
          </TouchableOpacity>
        </View>
      </KeyboardAvoidingView>
    </Modal>
  );
}

const styles = StyleSheet.create({
  backdrop: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.65)',
    justifyContent: 'flex-end',
  },
  sheet: {
    backgroundColor: C.mid,
    borderTopLeftRadius: 28,
    borderTopRightRadius: 28,
    padding: 28,
    paddingBottom: 44,
    borderTopWidth: 1,
    borderColor: C.border,
  },
  handle: {
    width: 40, height: 4, borderRadius: 2,
    backgroundColor: 'rgba(255,255,255,0.2)',
    alignSelf: 'center', marginBottom: 24,
  },
  logoWrap: { alignItems: 'center', marginBottom: 24 },
  title:    { color: C.text, fontSize: 20, fontWeight: '800', marginTop: 12 },
  subtitle: { color: C.muted, fontSize: 13, marginTop: 4 },

  errorText: {
    color: '#F87171', fontSize: 13, textAlign: 'center',
    marginBottom: 12, backgroundColor: 'rgba(239,68,68,0.1)',
    borderRadius: 8, padding: 10,
  },

  label: {
    color: C.muted, fontSize: 11, fontWeight: '700',
    letterSpacing: 1, marginBottom: 6, marginTop: 14,
  },
  input: {
    backgroundColor: '#0F172A',
    borderWidth: 1.5, borderColor: C.border,
    borderRadius: 10, paddingHorizontal: 14, paddingVertical: 12,
    color: C.text, fontSize: 14, marginBottom: 0,
  },
  pwRow:   { flexDirection: 'row', alignItems: 'center', gap: 8 },
  eyeBtn:  { padding: 10 },
  eyeText: { fontSize: 18 },

  btn: {
    backgroundColor: C.blue, borderRadius: 12,
    paddingVertical: 14, alignItems: 'center',
    marginTop: 24,
  },
  btnDisabled: { opacity: 0.6 },
  btnText: { color: '#fff', fontSize: 15, fontWeight: '700' },
  sessionNote: {
    color: C.muted,
    fontSize: 12,
    textAlign: 'center',
    lineHeight: 18,
    marginTop: 12,
  },

  cancelBtn: { alignItems: 'center', marginTop: 14, padding: 8 },
  cancelText: { color: C.muted, fontSize: 14 },
});
