import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  base: './',
  build: {
    outDir: 'dist',
    rollupOptions: {
      output: {
        // 将 React + React Router 单独打包，版本不变时可被浏览器长期缓存
        manualChunks(id) {
          if (id.includes('node_modules') && (
            id.includes('react') || id.includes('react-dom') || id.includes('react-router')
          )) {
            return 'vendor';
          }
        },
      },
    },
  },
})
