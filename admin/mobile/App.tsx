import React from 'react';
import { StatusBar, StyleSheet, View } from 'react-native';
import AdminDashboard from './screens/AdminDashboard';

export default function App() {
  return (
    <View style={styles.root}>
      <StatusBar barStyle="light-content" backgroundColor="#0E1326" />
      <AdminDashboard />
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: '#0E1326' },
});
