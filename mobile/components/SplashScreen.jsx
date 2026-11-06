import React, { useEffect, useRef } from 'react';
import {
  View,
  Image,
  StyleSheet,
  Animated,
  Easing,
} from 'react-native';

const LOGO_ASSET = require('./new-securets-logo-dark.png');

export default function SplashScreen({ onFinish }) {
  const scale      = useRef(new Animated.Value(0.88)).current;
  const opacity    = useRef(new Animated.Value(0)).current;
  const translateY = useRef(new Animated.Value(12)).current;
  const ringScale  = useRef(new Animated.Value(0.92)).current;

  useEffect(() => {
    // Smooth, premium entrance fade + scale
    Animated.parallel([
      Animated.timing(opacity, {
        toValue: 1,
        duration: 750,
        easing: Easing.out(Easing.cubic),
        useNativeDriver: true,
      }),
      Animated.spring(scale, {
        toValue: 1,
        friction: 7,
        tension: 50,
        useNativeDriver: true,
      }),
      Animated.timing(translateY, {
        toValue: 0,
        duration: 750,
        easing: Easing.out(Easing.cubic),
        useNativeDriver: true,
      }),
      Animated.timing(ringScale, {
        toValue: 1.06,
        duration: 1800,
        easing: Easing.inOut(Easing.quad),
        useNativeDriver: true,
      }),
    ]).start();

    // Smooth exit transition before handoff
    const timer = setTimeout(() => {
      Animated.parallel([
        Animated.timing(opacity, {
          toValue: 0,
          duration: 450,
          easing: Easing.in(Easing.cubic),
          useNativeDriver: true,
        }),
        Animated.timing(scale, {
          toValue: 1.04,
          duration: 450,
          easing: Easing.in(Easing.cubic),
          useNativeDriver: true,
        }),
      ]).start(() => onFinish && onFinish());
    }, 2800);

    return () => clearTimeout(timer);
  }, []);  // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <View style={styles.root}>
      <Animated.View style={[styles.container, { opacity }]}>
        <Animated.View
          style={[
            styles.logoWrap,
            { transform: [{ scale }, { translateY }] },
          ]}
        >
          <View style={styles.logoBadgeOuter}>
            <Animated.View
              style={[
                styles.logoGlowRing,
                { transform: [{ scale: ringScale }] },
              ]}
            />
            <View style={styles.logoCard}>
              <Image
                source={LOGO_ASSET}
                style={styles.logoImage}
                resizeMode="contain"
              />
            </View>
          </View>
        </Animated.View>
      </Animated.View>
    </View>
  );
}

const styles = StyleSheet.create({
  root: {
    flex: 1,
    backgroundColor: '#0E1326',
  },
  container: {
    ...StyleSheet.absoluteFillObject,
    flex: 1,
    backgroundColor: '#0E1326',
    alignItems: 'center',
    justifyContent: 'center',
    zIndex: 9999,
  },
  logoWrap: {
    alignItems: 'center',
    justifyContent: 'center',
  },
  logoBadgeOuter: {
    width: 260,
    height: 120,
    alignItems: 'center',
    justifyContent: 'center',
  },
  logoGlowRing: {
    position: 'absolute',
    width: 260,
    height: 120,
    borderRadius: 28,
    borderWidth: 1,
    borderColor: 'rgba(61, 90, 241, 0.28)',
    backgroundColor: 'rgba(61, 90, 241, 0.06)',
  },
  logoCard: {
    width: 240,
    height: 100,
    borderRadius: 22,
    backgroundColor: '#1C2236',
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.1)',
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 16,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.45,
    shadowRadius: 20,
    elevation: 10,
  },
  logoImage: {
    width: '100%',
    height: '100%',
  },
});
