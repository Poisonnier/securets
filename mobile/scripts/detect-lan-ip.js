#!/usr/bin/env node
/**
 * detect-lan-ip.js
 * ----------------
 * Finds this machine's LAN IPv4 address and writes it into mobile/.env so a
 * phone running Expo Go can reach the Laravel API (port 8000) and the Reverb
 * WebSocket (port 8080).
 *
 * Why this exists: "127.0.0.1" inside Expo Go means the phone itself, not this
 * computer, so the API URL must be the machine's LAN address. That address is
 * a DHCP lease and changes between networks, so it is detected on every start.
 *
 * Runs automatically before `expo start` (see package.json), or manually:
 *   npm run ip
 */

const fs = require('fs');
const os = require('os');
const path = require('path');

const ENV_PATH = path.join(__dirname, '..', '.env');

// Adapters belonging to virtual machines, containers or VPNs. Their addresses
// are not reachable from a phone on the Wi-Fi network, so rank them last.
const VIRTUAL_PATTERN =
  /(virtualbox|vmware|hyper-?v|vethernet|docker|bluetooth|tap|tun|wsl|vpn|zscaler|radmin|hamachi)/i;

function isPrivate(ip) {
  return (
    /^192\.168\./.test(ip) ||
    /^10\./.test(ip) ||
    /^172\.(1[6-9]|2\d|3[01])\./.test(ip)
  );
}

/** Returns the best guess for the LAN address, or null if none was found. */
function detect() {
  const candidates = [];

  for (const [name, addresses] of Object.entries(os.networkInterfaces())) {
    for (const address of addresses || []) {
      // Node < 18.4 reports family as 'IPv4', newer versions report 4.
      const isIPv4 = address.family === 'IPv4' || address.family === 4;
      if (!isIPv4 || address.internal) continue;

      candidates.push({
        name,
        ip: address.address,
        score:
          (isPrivate(address.address) ? 2 : 0) +
          (VIRTUAL_PATTERN.test(name) ? 0 : 1),
      });
    }
  }

  candidates.sort((a, b) => b.score - a.score);

  return candidates[0] || null;
}

/** Replaces `KEY=...` in place, appending it when the key is absent. */
function upsert(contents, key, value) {
  const pattern = new RegExp(`^${key}=.*$`, 'm');

  return pattern.test(contents)
    ? contents.replace(pattern, `${key}=${value}`)
    : `${contents.replace(/\s*$/, '')}\n${key}=${value}\n`;
}

function main() {
  let contents = '';
  try {
    contents = fs.readFileSync(ENV_PATH, 'utf8');
  } catch {
    console.warn('[lan-ip] mobile/.env not found - creating it.');
  }

  // Escape hatch: set DETECT_LAN_IP=false when the app is pointed somewhere
  // other than this machine's LAN address (a tunnel, staging or production),
  // so the hand-written values below survive `npm start`.
  if (/^\s*DETECT_LAN_IP\s*=\s*(false|0|no)\s*$/im.test(contents)) {
    console.log('[lan-ip] DETECT_LAN_IP=false - leaving mobile/.env untouched.');
    return;
  }

  const found = detect();

  if (!found) {
    console.warn('[lan-ip] No LAN IPv4 address found - is Wi-Fi connected?');
    console.warn('[lan-ip] Leaving mobile/.env untouched.');
    return;
  }

  contents = upsert(contents, 'EXPO_PUBLIC_API_URL', `http://${found.ip}:8000/api`);
  contents = upsert(contents, 'EXPO_PUBLIC_REVERB_HOST', found.ip);

  fs.writeFileSync(ENV_PATH, contents);

  console.log(`[lan-ip] Detected ${found.ip} (${found.name})`);
  console.log(`[lan-ip]   API    -> http://${found.ip}:8000/api`);
  console.log(`[lan-ip]   Reverb -> ws://${found.ip}:8080`);
}

main();
