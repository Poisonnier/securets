import axios from 'axios';
import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';

// API base URL — set EXPO_PUBLIC_API_URL in mobile/.env
// Examples:
//   Local dev (web/iOS sim):    http://127.0.0.1:8000/api
//   Android emulator:           http://10.0.2.2:8000/api
//   Physical device (Expo Go):  http://192.168.x.x:8000/api
//   Production:                 https://yourdomain.com/api
export const API_BASE_URL =
  process.env.EXPO_PUBLIC_API_URL ?? 'http://127.0.0.1:8000/api';

export const AUTH_SESSION_MINUTES = Number.parseInt(
  process.env.EXPO_PUBLIC_AUTH_SESSION_MINUTES ?? '120',
  10,
);

export const AUTH_SESSION_LABEL = AUTH_SESSION_MINUTES % 60 === 0
  ? `${AUTH_SESSION_MINUTES / 60} hour${AUTH_SESSION_MINUTES === 60 ? '' : 's'}`
  : `${AUTH_SESSION_MINUTES} minutes`;

const TOKEN_KEY = 'user_token';
const TOKEN_META_KEY = 'user_token_meta';


const API = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  timeout: 10000,
});

const getStorageItem = async (key) => {
  try {
    if (Platform.OS === 'web') {
      return localStorage.getItem(key);
    }
    return await SecureStore.getItemAsync(key);
  } catch (error) {
    console.error(`Error reading ${key}:`, error);
    return null;
  }
};

const setStorageItem = async (key, value) => {
  try {
    if (Platform.OS === 'web') {
      localStorage.setItem(key, value);
    } else {
      await SecureStore.setItemAsync(key, value);
    }
  } catch (error) {
    console.error(`Error saving ${key}:`, error);
  }
};

const removeStorageItem = async (key) => {
  try {
    if (Platform.OS === 'web') {
      localStorage.removeItem(key);
    } else {
      await SecureStore.deleteItemAsync(key);
    }
  } catch (error) {
    console.error(`Error removing ${key}:`, error);
  }
};

export const clearStoredSession = async () => {
  await removeStorageItem(TOKEN_KEY);
  await removeStorageItem(TOKEN_META_KEY);
};

const getStoredTokenMeta = async () => {
  const raw = await getStorageItem(TOKEN_META_KEY);

  if (!raw) {
    return null;
  }

  try {
    return JSON.parse(raw);
  } catch (error) {
    console.error('Error parsing auth token metadata:', error);
    await clearStoredSession();
    return null;
  }
};

export const getStoredToken = async () => {
  const token = await getStorageItem(TOKEN_KEY);

  if (!token) {
    return null;
  }

  const meta = await getStoredTokenMeta();
  const expiresAt = meta?.expiresAt ? Date.parse(meta.expiresAt) : NaN;

  // Allow a 30-second grace window to prevent clock-drift false-expiries.
  const GRACE_MS = 30_000;
  if (!meta || Number.isNaN(expiresAt) || expiresAt <= Date.now() - GRACE_MS) {
    await clearStoredSession();
    return null;
  }

  return token;
};

export const setStoredToken = async (token, { expiresAt } = {}) => {
  if (!token) {
    await clearStoredSession();
    return;
  }

  let finalExpiresAt;
  if (expiresAt) {
    finalExpiresAt = new Date(expiresAt).toISOString();
  } else if (AUTH_SESSION_MINUTES > 0) {
    finalExpiresAt = new Date(Date.now() + (AUTH_SESSION_MINUTES * 60 * 1000)).toISOString();
  } else {
    // Server returned 0 = infinite token; default to 1 year on the client side
    // so we still clear stale storage on yearly basis.
    finalExpiresAt = new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toISOString();
  }

  await setStorageItem(TOKEN_KEY, token);
  await setStorageItem(TOKEN_META_KEY, JSON.stringify({ expiresAt: finalExpiresAt }));
};

// Interceptor: Automatically attach Bearer token to all outgoing requests
API.interceptors.request.use(async (config) => {
  const token = await getStoredToken();
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, (error) => {
  return Promise.reject(error);
});

API.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error?.response?.status === 401) {
      await clearStoredSession();
    }

    return Promise.reject(error);
  },
);

export default API;
