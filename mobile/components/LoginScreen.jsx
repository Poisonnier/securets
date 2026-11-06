import React, { useState } from 'react';
import {
  View,
  Text,
  Image,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
  Alert,
  Platform,
} from 'react-native';
import { Feather } from '@expo/vector-icons';
import API, { AUTH_SESSION_LABEL, setStoredToken } from '../src/api/client';

const LOGO_ASSET = require('./new-securets-logo-dark.png');

export default function LoginScreen({ onLoginSuccess }) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const [showPw, setShowPw] = useState(false);
  const [focusedField, setFocusedField] = useState(null);

  const handleLogin = async () => {
    if (!email || !password) {
      setErrorMessage('Please enter both email and password.');
      return;
    }

    setLoading(true);
    setErrorMessage('');

    try {
      const response = await API.post('/mobile/login', {
        email: email.trim(),
        password,
        device_name: `${Platform.OS}_mobile_app`,
      });

      if (response.data && response.data.token) {
        await setStoredToken(response.data.token, { expiresAt: response.data.expires_at });
        if (onLoginSuccess) {
          onLoginSuccess(response.data.user);
        }
      }
    } catch (error) {
      console.error('Login error:', error);
      const msg = error.response?.data?.message || 'Failed to connect to SecureTS backend.';
      setErrorMessage(msg);
      if (Platform.OS !== 'web') {
        Alert.alert('Authentication Failed', msg);
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <View style={styles.card}>
        {/* Logo Header */}
        <View style={styles.logoContainer}>
          <Image
            source={LOGO_ASSET}
            style={styles.logoImage}
            resizeMode="contain"
          />
          <Text style={styles.subtitle}>Mobile ThreatShield Gateway</Text>
        </View>

        {/* Input Form */}
        <View style={styles.formGroup}>
          <Text style={styles.label}>EMAIL ADDRESS</Text>
          <TextInput
            style={[
              styles.input,
              focusedField === 'email' && styles.inputFocused,
            ]}
            placeholder="you@example.com"
            placeholderTextColor="#64748B"
            value={email}
            onChangeText={setEmail}
            onFocus={() => setFocusedField('email')}
            onBlur={() => setFocusedField(null)}
            autoCapitalize="none"
            autoCorrect={false}
            keyboardType="email-address"
            textContentType="emailAddress"
            importantForAutofill="yes"
          />
        </View>

        <View style={styles.formGroup}>
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
              placeholderTextColor="#64748B"
              value={password}
              onChangeText={setPassword}
              onFocus={() => setFocusedField('password')}
              onBlur={() => setFocusedField(null)}
              secureTextEntry={!showPw}
              autoCorrect={false}
              textContentType="password"
              importantForAutofill="yes"
            />
            <TouchableOpacity
              style={styles.eyeBtn}
              onPress={() => setShowPw((v) => !v)}
              activeOpacity={0.7}
            >
              <Feather
                name={showPw ? 'eye-off' : 'eye'}
                size={18}
                color={showPw ? '#00BBFF' : '#94A3B8'}
              />
            </TouchableOpacity>
          </View>
        </View>

        {errorMessage ? (
          <Text style={styles.errorText}>{errorMessage}</Text>
        ) : null}

        {/* Submit Button */}
        <TouchableOpacity
          style={[styles.button, loading && styles.buttonDisabled]}
          onPress={handleLogin}
          disabled={loading}
          activeOpacity={0.85}
        >
          {loading ? (
            <ActivityIndicator color="#FFFFFF" size="small" />
          ) : (
            <Text style={styles.buttonText}>Sign In</Text>
          )}
        </TouchableOpacity>

        <View style={styles.footerNote}>
          <Text style={styles.footerText}>
            Protected by Laravel Sanctum Bearer Token Auth
          </Text>
          <Text style={styles.footerText}>
            Session stays signed in for up to {AUTH_SESSION_LABEL} after the app closes.
          </Text>
        </View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#0E1326',
    justifyContent: 'center',
    alignItems: 'center',
    padding: 24,
  },
  card: {
    width: '100%',
    maxWidth: 400,
    backgroundColor: '#1C2236',
    borderRadius: 12,
    padding: 28,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.08)',
  },
  logoContainer: {
    alignItems: 'center',
    marginBottom: 28,
  },
  logoImage: {
    width: 180,
    height: 48,
  },
  subtitle: {
    color: '#94A3B8',
    fontSize: 12,
    marginTop: 10,
    letterSpacing: 0.3,
  },
  formGroup: {
    marginBottom: 18,
  },
  label: {
    color: '#94A3B8',
    fontSize: 11,
    fontWeight: '600',
    marginBottom: 8,
    letterSpacing: 0.8,
  },
  input: {
    backgroundColor: '#0E1326',
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.1)',
    borderRadius: 8,
    paddingHorizontal: 14,
    paddingVertical: 12,
    color: '#F8FAFC',
    fontSize: 14,
  },
  inputFocused: {
    borderColor: '#00BBFF',
  },
  pwWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#0E1326',
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.1)',
    borderRadius: 8,
  },
  pwInput: {
    flex: 1,
    paddingHorizontal: 14,
    paddingVertical: 12,
    color: '#F8FAFC',
    fontSize: 14,
  },
  eyeBtn: {
    paddingHorizontal: 14,
    paddingVertical: 12,
    justifyContent: 'center',
    alignItems: 'center',
  },
  errorText: {
    color: '#F87171',
    fontSize: 12,
    marginBottom: 16,
    textAlign: 'center',
  },
  button: {
    backgroundColor: '#4063D7',
    borderRadius: 8,
    paddingVertical: 14,
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 8,
  },
  buttonDisabled: {
    opacity: 0.6,
  },
  buttonText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: '600',
    letterSpacing: 0.2,
  },
  footerNote: {
    marginTop: 20,
    alignItems: 'center',
    gap: 4,
  },
  footerText: {
    color: '#64748B',
    fontSize: 11,
    textAlign: 'center',
  },
});
