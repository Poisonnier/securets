import React, { createContext, useContext, useMemo } from 'react';
import { useColorScheme } from 'react-native';

export const PALETTES = {
  dark: {
    mode: 'dark', bg: '#0A0F1E', surface: '#0F172A', card: '#1E293B',
    inputBg: 'rgba(255,255,255,0.07)', subtle: 'rgba(255,255,255,0.06)',
    border: 'rgba(255,255,255,0.08)', text: '#F8FAFC', muted: '#94A3B8',
    blue: '#3D5AF1', blueLight: '#818CF8', danger: '#EF4444',
    success: '#22C55E', warning: '#EAB308', statusBar: 'light-content',
  },
  light: {
    mode: 'light', bg: '#F1F5F9', surface: '#FFFFFF', card: '#FFFFFF',
    inputBg: '#F8FAFC', subtle: 'rgba(15,23,42,0.05)',
    border: 'rgba(15,23,42,0.10)', text: '#0F172A', muted: '#64748B',
    blue: '#3D5AF1', blueLight: '#3D5AF1', danger: '#DC2626',
    success: '#16A34A', warning: '#CA8A04', statusBar: 'dark-content',
  },
};

const ThemeContext = createContext(PALETTES.dark);

export function ThemeProvider({ preference = 'dark', children }) {
  const system = useColorScheme();
  const resolved = preference === 'system'
    ? (system === 'light' ? 'light' : 'dark')
    : (preference === 'light' ? 'light' : 'dark');

  const value = useMemo(() => PALETTES[resolved], [resolved]);

  return <ThemeContext.Provider value={value}>{children}</ThemeContext.Provider>;
}

export const useTheme = () => useContext(ThemeContext);
