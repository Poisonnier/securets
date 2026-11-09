import React, { createContext, useContext, ReactNode } from 'react';

export interface ThemeTokens {
  colors: {
    background: string;
    surface: string;
    primary: string;
    accent: string;
    text: string;
    textMuted: string;
    border: string;
  };
  radius: {
    card: number;
    control: number;
  };
  spacing: {
    xs: number;
    sm: number;
    md: number;
    lg: number;
    xl: number;
  };
}

export const THEME: ThemeTokens = {
  colors: {
    background: '#0E1326',
    surface: '#1C2236',
    primary: '#4063D7',
    accent: '#00BBFF',
    text: '#FFFFFF',
    textMuted: 'rgba(255, 255, 255, 0.6)',
    border: 'rgba(255, 255, 255, 0.08)',
  },
  radius: {
    card: 12,
    control: 8,
  },
  spacing: {
    xs: 4,
    sm: 8,
    md: 12,
    lg: 16,
    xl: 24,
  },
};

const ThemeContext = createContext<ThemeTokens>(THEME);

export function ThemeProvider({ children }: { children: ReactNode }) {
  return <ThemeContext.Provider value={THEME}>{children}</ThemeContext.Provider>;
}

export const useTheme = (): ThemeTokens => useContext(ThemeContext);
