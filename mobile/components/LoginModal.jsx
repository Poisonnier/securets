import React, { useMemo, useState } from 'react';
import {
  View, Text, Image, StyleSheet, TouchableOpacity,
  TextInput, ActivityIndicator, Platform, Modal,
  KeyboardAvoidingView, ScrollView,
} from 'react-native';
import { Feather } from '@expo/vector-icons';
import * as WebBrowser from 'expo-web-browser';
import API, {
  getConfiguredEndpoints,
  AUTH_SESSION_LABEL,
  setStoredToken,
  getGoogleAuthRedirectUrl,
  exchangeGoogleAuthCode,
} from '../src/api/client';
import { useTheme } from '../src/theme/ThemeContext';

const LOGO_ASSET = require('./new-securets-logo-dark.png');

/**
 * LoginModal – floating bottom-sheet that can be shown over any screen.
 * Props:
 *   visible        – boolean
 *   onClose        – () => void
 *   onLoginSuccess – (user) => void
 */
export default function LoginModal({ visible, onClose, onLoginSuccess }) {
  const C = useTheme();
  const styles = useMemo(() => createStyles(C), [C]);
  const [email,    setEmail]    = useState('');
  const [password, setPassword] = useState('');
  const [loading,  setLoading]  = useState(false);
  const [error,    setError]    = useState('');
  const [showPw,   setShowPw]   = useState(false);
  const [googleLoading, setGoogleLoading] = useState(false);
  const [focusedField, setFocusedField] = useState(null);
  const isBusy = loading || googleLoading;

  const handleGoogleLogin = async () => {
    setGoogleLoading(true);
    setError('');
    try {
      const result = await WebBrowser.openAuthSessionAsync(
        getGoogleAuthRedirectUrl(),
        'securets://auth/google',
      );

      if (result.type !== 'success' || !result.url) {
        return;
      }

      const queryString = result.url.includes('?') ? result.url.split('?')[1].split('#')[0] : '';
      const params = new URLSearchParams(queryString);
      const oauthError = params.get('error');
      const code = params.get('code');

      if (oauthError) {
        setError('Google authentication failed or was cancelled. Please try again.');
        return;
      }

      if (!code) {
        setError('Google sign-in did not return an authorization code.');
        return;
      }

      const data = await exchangeGoogleAuthCode(code);
      setEmail('');
      setPassword('');
      setError('');
      onLoginSuccess?.(data.user);
    } catch (e) {
      const msg = e?.response?.data?.message
        || e?.response?.data?.errors?.code?.[0]
        || e?.message
        || 'Google sign-in failed. Please try again.';
      setError(msg);
    } finally {
      setGoogleLoading(false);
    }
  };

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
      if (!res.data?.token) {
        setError('Login response did not include an authentication token.');
        return;
      }

      await setStoredToken(res.data.token, { expiresAt: res.data.expires_at });

      // Clear fields BEFORE success callback
      setEmail('');
      setPassword('');
      setError('');

      // Propagate user data up — push token registration is handled
      // centrally in App.jsx handleLoginSuccess via notifications.js
      onLoginSuccess?.(res.data.user);
    } catch (e) {
      console.warn('LoginModal Error:', e?.response?.data || e?.message);

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
        msg = `Cannot reach SecureTS (tried ${getConfiguredEndpoints().join(' and ')}). Check your connection or VPN.`;
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

          {/* Logo */}
          <View style={styles.logoWrap}>
            <Image
              source={LOGO_ASSET}
              style={styles.logoImage}
              resizeMode="contain"
            />
            <Text style={styles.title}>Sign In to SecureTS</Text>
            <Text style={styles.subtitle}>Required to access your integrations</Text>
          </View>

          {/* Error */}
          {!!error && <Text style={styles.errorText}>{error}</Text>}

          {/* Email */}
          <Text style={styles.label}>EMAIL</Text>
          <TextInput
            style={[
              styles.input,
              focusedField === 'email' && styles.inputFocused,
            ]}
            placeholder="you@example.com"
            placeholderTextColor={C.muted}
            value={email}
            onChangeText={setEmail}
            onFocus={() => setFocusedField('email')}
            onBlur={() => setFocusedField(null)}
            autoCapitalize="none"
            autoCorrect={false}
            keyboardType="email-address"
            textContentType="emailAddress"
            importantForAutofill="yes"
            editable={!loading}
          />

          {/* Password */}
          <Text style={styles.label}>PASSWORD</Text>
          <View
            style={[
              styles.pwWrapper,
              focusedField === 'password' && styles.inputFocused,
            ]}
          >
            <TextInput
              style={styles.pwInput}
              placeholder="••••••••"
              placeholderTextColor={C.muted}
              value={password}
              onChangeText={setPassword}
              onFocus={() => setFocusedField('password')}
              onBlur={() => setFocusedField(null)}
              secureTextEntry={!showPw}
              autoCorrect={false}
              textContentType="password"
              importantForAutofill="yes"
              editable={!loading}
            />
            <TouchableOpacity
              style={styles.eyeBtn}
              onPress={() => setShowPw((v) => !v)}
              activeOpacity={0.7}
            >
              <Feather
                name={showPw ? 'eye-off' : 'eye'}
                size={18}
                color={showPw ? C.blueLight : C.muted}
              />
            </TouchableOpacity>
          </View>

          {/* Submit */}
          <TouchableOpacity
            style={[styles.btn, isBusy && styles.btnDisabled]}
            onPress={handleLogin}
            disabled={isBusy}
            activeOpacity={0.85}
          >
            {loading
              ? <ActivityIndicator color="#fff" size="small" />
              : <Text style={styles.btnText}>Sign In</Text>
            }
          </TouchableOpacity>

          {/* Divider */}
          <View style={styles.dividerRow}>
            <View style={styles.dividerLine} />
            <Text style={styles.dividerText}>OR</Text>
            <View style={styles.dividerLine} />
          </View>

          {/* Continue with Google */}
          <TouchableOpacity
            style={[styles.googleBtn, isBusy && styles.btnDisabled]}
            onPress={handleGoogleLogin}
            disabled={isBusy}
            activeOpacity={0.85}
          >
            {googleLoading ? (
              <ActivityIndicator color="#FFFFFF" size="small" />
            ) : (
              <>
                <View style={styles.googleBadge}>
                  <Text style={styles.googleBadgeText}>G</Text>
                </View>
                <Text style={styles.googleBtnText}>Continue with Google</Text>
              </>
            )}
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

const createStyles = (C) => StyleSheet.create({
  backdrop: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.6)',
    justifyContent: 'flex-end',
  },
  sheet: {
    backgroundColor: C.card,
    borderTopLeftRadius: 16,
    borderTopRightRadius: 16,
    padding: 24,
    paddingBottom: 40,
    borderTopWidth: 1,
    borderColor: C.border,
  },
  handle: {
    width: 36,
    height: 4,
    borderRadius: 2,
    backgroundColor: C.border,
    alignSelf: 'center',
    marginBottom: 20,
  },
  logoWrap: {
    alignItems: 'center',
    marginBottom: 20,
  },
  logoImage: {
    width: 160,
    height: 42,
  },
  title: {
    color: C.text,
    fontSize: 18,
    fontWeight: '700',
    marginTop: 12,
    letterSpacing: -0.3,
  },
  subtitle: {
    color: C.muted,
    fontSize: 12,
    marginTop: 4,
  },

  errorText: {
    color: C.danger,
    fontSize: 12,
    textAlign: 'center',
    marginBottom: 12,
    backgroundColor: 'rgba(239, 68, 68, 0.08)',
    borderWidth: 1,
    borderColor: 'rgba(239, 68, 68, 0.25)',
    borderRadius: 8,
    padding: 10,
  },

  label: {
    color: C.muted,
    fontSize: 11,
    fontWeight: '600',
    letterSpacing: 0.8,
    marginBottom: 6,
    marginTop: 14,
  },
  input: {
    backgroundColor: C.inputBg,
    borderWidth: 1,
    borderColor: C.border,
    borderRadius: 8,
    paddingHorizontal: 14,
    paddingVertical: 12,
    color: C.text,
    fontSize: 14,
    marginBottom: 0,
  },
  inputFocused: {
    borderColor: C.blueLight,
  },
  pwWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: C.inputBg,
    borderWidth: 1,
    borderColor: C.border,
    borderRadius: 8,
  },
  pwInput: {
    flex: 1,
    paddingHorizontal: 14,
    paddingVertical: 12,
    color: C.text,
    fontSize: 14,
  },
  eyeBtn: {
    paddingHorizontal: 14,
    paddingVertical: 12,
    justifyContent: 'center',
    alignItems: 'center',
  },

  btn: {
    backgroundColor: C.blue,
    borderRadius: 8,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 22,
  },
  btnDisabled: { opacity: 0.6 },
  btnText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: '600',
    letterSpacing: 0.2,
  },
  dividerRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 14,
    marginBottom: 14,
  },
  dividerLine: {
    flex: 1,
    height: 1,
    backgroundColor: 'rgba(255, 255, 255, 0.08)',
  },
  dividerText: {
    color: 'rgba(255, 255, 255, 0.6)',
    fontSize: 11,
    fontWeight: '600',
    letterSpacing: 0.8,
    paddingHorizontal: 12,
  },
  googleBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#0E1326',
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.08)',
    borderRadius: 8,
    paddingVertical: 13,
    paddingHorizontal: 14,
    gap: 10,
  },
  googleBadge: {
    width: 20,
    height: 20,
    borderRadius: 4,
    backgroundColor: '#1C2236',
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.08)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  googleBadgeText: {
    color: '#00BBFF',
    fontSize: 12,
    fontWeight: '600',
  },
  googleBtnText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: '600',
  },
  sessionNote: {
    color: C.muted,
    fontSize: 11,
    textAlign: 'center',
    lineHeight: 16,
    marginTop: 12,
  },

  cancelBtn: { alignItems: 'center', marginTop: 12, padding: 8 },
  cancelText: { color: C.muted, fontSize: 13, fontWeight: '500' },
});
