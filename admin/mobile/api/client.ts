import axios from 'axios';
import { Platform } from 'react-native';

const resolveBaseUrl = () => {
  const configured = process.env.EXPO_PUBLIC_ADMIN_API_URL?.trim();
  if (configured) return configured.replace(/\/$/, '');
  if (Platform.OS === 'android') return 'http://10.0.2.2:8001';
  return 'http://127.0.0.1:8001';
};

const configuredBaseUrl = resolveBaseUrl();
// Accept both forms so the same build works with the browser URL
// `http://localhost:8001/admin` and with a host-only mobile URL.
const adminPath = /\/admin$/i.test(configuredBaseUrl) ? '' : '/admin';

const API = axios.create({
  baseURL: configuredBaseUrl,
  timeout: 10000,
  headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
});

export type AdminDevice = {
  id: number;
  name: string;
  owner: string;
  location: string;
  status: string;
  state: string;
};

export type AdminOverview = {
  adminName: string;
  metrics: { users: number; sessions: number; devices: number; health: string };
  devices: AdminDevice[];
  lockdown: { active: boolean; activated_at?: string; reason?: string };
  audit: Array<{ time: string; actor: string; action: string; target: string; severity: string }>;
};

export const getAdminApiBaseUrl = () => `${configuredBaseUrl}${adminPath}`;

export const getOverview = () => API.get<AdminOverview>(`${adminPath}/api/overview`);

export const triggerLockdown = (reason = 'Emergency response from admin mobile') =>
  API.post(`${adminPath}/api/system/lockdown`, { confirmation: 'LOCKDOWN', reason });

export const restoreAccess = () =>
  API.post(`${adminPath}/api/system/unlock`, { confirmation: 'UNLOCK' });

export const deviceAction = (deviceId: number, action: 'remote_lock' | 'revoke_tokens' | 'mark_recovered') =>
  API.post(`${adminPath}/api/devices/${deviceId}/action`, { action });

export const generateBypassCode = (email: string) =>
  API.post(`${adminPath}/recovery/generate-code`, { email });

export default API;
