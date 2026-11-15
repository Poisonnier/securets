#!/usr/bin/env node
const fs = require('fs');
const os = require('os');
const path = require('path');

const root = path.join(__dirname, '..');
const envPath = path.join(root, '.env');
const localPath = path.join(root, '.env.local');
const port = process.env.DETECT_API_PORT || '8001';
const args = process.argv.slice(2);
const existingEnv = fs.existsSync(envPath) ? fs.readFileSync(envPath, 'utf8') : '';

const isPrivate = (ip) => /^(10\.|192\.168\.|172\.(1[6-9]|2\d|3[01])\.)/.test(ip);
const interfaces = Object.entries(os.networkInterfaces());
const addresses = interfaces.flatMap(([name, values]) => (values || []).map((value) => ({ name, value })));
const tailscale = addresses.find(({ value }) => /^100\.(6[4-9]|[7-9]\d|1[01]\d|12[0-7])\./.test(value.address));
const lan = addresses.find(({ value }) => value.family === 'IPv4' && !value.internal && isPrivate(value.address));

if (args.includes('--tailscale') && tailscale) {
  fs.writeFileSync(envPath, `DETECT_LAN_IP=false\nEXPO_PUBLIC_ADMIN_API_URL=http://${tailscale.value.address}:${port}\n`);
  fs.writeFileSync(localPath, `REACT_NATIVE_PACKAGER_HOSTNAME=${tailscale.value.address}\n`);
  console.log(`[admin-lan-ip] Using Tailscale address ${tailscale.value.address}`);
} else if (/^DETECT_LAN_IP\s*=\s*false\s*$/im.test(existingEnv)) {
  console.log('[admin-lan-ip] DETECT_LAN_IP=false; keeping configured endpoint.');
} else if (lan) {
  fs.writeFileSync(envPath, `EXPO_PUBLIC_ADMIN_API_URL=http://${lan.value.address}:${port}\nDETECT_LAN_IP=true\n`);
  console.log(`[admin-lan-ip] Using ${lan.value.address} (${lan.name})`);
} else {
  console.log('[admin-lan-ip] No private IPv4 address found; keeping the configured endpoint.');
}
