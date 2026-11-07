import React, { useState, useRef } from 'react';
import {
  View,
  Text,
  Image,
  StyleSheet,
  Dimensions,
  TouchableOpacity,
  FlatList,
  Animated,
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';

const { width, height } = Dimensions.get('window');
const LOGO_ASSET = require('./new-securets-logo-dark.png');

const SLIDES = [
  {
    id: '1',
    tag: '01 // CORE_PLATFORM',
    title: 'Welcome to SecureTS',
    description:
      'SecureTS ThreatShield is a cutting-edge Multi-Factor Authentication platform that keeps all your linked accounts protected from modern threats.',
  },
  {
    id: '2',
    tag: '02 // MULTI_FACTOR',
    title: 'MFA Protection',
    description:
      'Seamlessly verify your identity using secure one-time codes, biometrics, and hardware keys — all in one place.',
  },
  {
    id: '3',
    tag: '03 // ZERO_KNOWLEDGE',
    title: 'Zero Passwords',
    description:
      'No stored passwords, no DB credentials exposed. SecureTS uses token-based authentication so your data stays private.',
  },
  {
    id: '4',
    tag: '04 // DEVICE_BOUND',
    title: 'Phishing Immune',
    description:
      'Cryptographic keys bound to your device mean phishing attacks can\'t steal your access — ever.',
  },
];

export default function OnboardingScreen({ onFinish }) {
  const [currentIndex, setCurrentIndex] = useState(0);
  const flatListRef = useRef(null);
  const scrollX = useRef(new Animated.Value(0)).current;
  const viewabilityConfig = useRef({ itemVisiblePercentThreshold: 50 }).current;

  const safeIndex =
    typeof currentIndex === 'number' && !Number.isNaN(currentIndex)
      ? Math.max(0, Math.min(currentIndex, SLIDES.length - 1))
      : 0;
  const isLastSlide = safeIndex === SLIDES.length - 1;

  const handleGetStarted = async () => {
    try {
      await AsyncStorage.setItem('onboarding_complete', 'true');
    } catch (_) { /* ignore */ }
    onFinish && onFinish();
  };

  const handlePrimaryAction = () => {
    if (isLastSlide) {
      handleGetStarted();
    } else {
      const nextIndex = Math.min(safeIndex + 1, SLIDES.length - 1);
      flatListRef.current?.scrollToOffset({
        offset: nextIndex * width,
        animated: true,
      });
      setCurrentIndex(nextIndex);
    }
  };

  const handleScroll = (event) => {
    const offsetX = event?.nativeEvent?.contentOffset?.x ?? 0;
    scrollX.setValue(offsetX);
    if (width > 0) {
      const nextIndex = Math.round(offsetX / width);
      if (nextIndex >= 0 && nextIndex < SLIDES.length) {
        setCurrentIndex((prev) => (prev === nextIndex ? prev : nextIndex));
      }
    }
  };

  const onViewableItemsChanged = useRef(({ viewableItems }) => {
    if (Array.isArray(viewableItems) && viewableItems.length > 0) {
      const idx = viewableItems[0]?.index;
      if (typeof idx === 'number' && idx >= 0 && idx < SLIDES.length) {
        setCurrentIndex(idx);
      }
    }
  }).current;

  const renderItem = ({ item }) => (
    <View style={styles.slide}>
      <View style={styles.card}>
        <View style={styles.tagBadge}>
          <View style={styles.tagDot} />
          <Text style={styles.tagText}>{item?.tag ?? ''}</Text>
        </View>
        <Text style={styles.slideTitle}>{item?.title ?? ''}</Text>
        <View style={styles.divider} />
        <Text style={styles.slideDesc}>{item?.description ?? ''}</Text>
      </View>
    </View>
  );

  return (
    <View style={styles.container}>
      {/* Top minimalist header bar */}
      <View style={styles.topBar}>
        <Image
          source={LOGO_ASSET}
          style={styles.topLogo}
          resizeMode="contain"
        />
        <View style={styles.pageIndicatorWrap}>
          <View style={styles.dotsContainer}>
            {SLIDES.map((_, index) => (
              <View
                key={index}
                style={[
                  styles.dot,
                  index === safeIndex ? styles.dotActive : styles.dotInactive,
                ]}
              />
            ))}
          </View>
          <View style={styles.stepPill}>
            <Text style={styles.stepPillText}>
              {safeIndex + 1} / {SLIDES.length}
            </Text>
          </View>
        </View>
      </View>

      {/* Slides */}
      <FlatList
        ref={flatListRef}
        data={SLIDES}
        keyExtractor={(item, index) => item?.id ?? String(index)}
        renderItem={renderItem}
        horizontal
        pagingEnabled
        showsHorizontalScrollIndicator={false}
        onScroll={handleScroll}
        scrollEventThrottle={16}
        onViewableItemsChanged={onViewableItemsChanged}
        viewabilityConfig={viewabilityConfig}
      />

      {/* Footer controls */}
      <View style={styles.footer}>
        {/* Primary action button */}
        <TouchableOpacity
          style={styles.getStartedBtn}
          onPress={handlePrimaryAction}
          activeOpacity={0.85}
          id="getStartedBtn"
        >
          <Text style={styles.getStartedText}>
            {isLastSlide ? 'Get Started' : 'Next'}
          </Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#0E1326',
    justifyContent: 'space-between',
  },

  // Top Header
  topBar: {
    width: '100%',
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 24,
    paddingTop: 56,
    paddingBottom: 12,
  },
  topLogo: {
    width: 70,
    height: 36,
    alignSelf: 'flex-start',
  },
  pageIndicatorWrap: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  stepPill: {
    backgroundColor: '#1C2236',
    paddingVertical: 5,
    paddingHorizontal: 12,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.08)',
  },
  stepPillText: {
    fontSize: 11,
    fontWeight: '700',
    color: '#94A3B8',
    letterSpacing: 1,
  },

  // Slides & Card
  slide: {
    width,
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 24,
  },
  card: {
    width: '100%',
    backgroundColor: '#1C2236',
    borderRadius: 16,
    padding: 28,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.08)',
    alignItems: 'flex-start',
  },
  tagBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: '#0E1326',
    paddingVertical: 6,
    paddingHorizontal: 10,
    borderRadius: 6,
    borderWidth: 1,
    borderColor: 'rgba(61, 90, 241, 0.3)',
    marginBottom: 20,
  },
  tagDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: '#3D5AF1',
  },
  tagText: {
    fontSize: 10,
    fontWeight: '700',
    color: '#818CF8',
    letterSpacing: 1.2,
  },
  slideTitle: {
    fontSize: 24,
    fontWeight: '700',
    color: '#F8FAFC',
    marginBottom: 14,
    letterSpacing: -0.4,
  },
  divider: {
    width: 36,
    height: 2,
    backgroundColor: '#3D5AF1',
    borderRadius: 1,
    marginBottom: 16,
  },
  slideDesc: {
    fontSize: 14,
    color: '#94A3B8',
    lineHeight: 22,
  },

  // Footer
  footer: {
    width: '100%',
    paddingHorizontal: 24,
    paddingBottom: 40,
    alignItems: 'center',
    gap: 20,
  },

  // Dots
  dotsContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  dot: {
    height: 6,
    borderRadius: 3,
  },
  dotActive: {
    backgroundColor: '#3D5AF1',
    width: 24,
  },
  dotInactive: {
    backgroundColor: '#1C2236',
    width: 8,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.12)',
  },

  // Button
  getStartedBtn: {
    width: '100%',
    backgroundColor: '#00BBFF',
    borderWidth: 1,
    borderColor: '#00BBFF',
    borderRadius: 12,
    paddingVertical: 16,
    paddingHorizontal: 32,
    alignItems: 'center',
    justifyContent: 'center',
  },
  getStartedText: {
    color: '#F8FAFC',
    fontSize: 15,
    fontWeight: '700',
    letterSpacing: 0.4,
  },
});
