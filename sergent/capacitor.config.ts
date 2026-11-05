import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.sergent.coach',
  appName: 'Sergent',
  webDir: 'dist',
  android: {
    backgroundColor: '#0a0c0b',
  },
  plugins: {
    SystemBars: {
      insetsHandling: 'css',
      style: 'DARK',
    },
    SplashScreen: {
      launchShowDuration: 700,
      launchAutoHide: true,
      backgroundColor: '#0a0c0b',
      showSpinner: false,
    },
    LocalNotifications: {
      smallIcon: 'ic_stat_sergent',
      iconColor: '#FF6A2B',
    },
  },
};

export default config;
