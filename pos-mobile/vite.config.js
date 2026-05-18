import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

// All requests to these prefixes go to the Laravel backend via the dev-server
// proxy, so no CORS headers are needed during development.
const BACKEND = 'https://serengeti.apexpos.co.ke'
// cookieDomainRewrite strips the domain from Set-Cookie responses so the
// browser on localhost stores the XSRF-TOKEN cookie instead of rejecting it.
const proxyOpts = { target: BACKEND, changeOrigin: true, secure: false, cookieDomainRewrite: { '*': '' } }

export default defineConfig({
  plugins: [react(), tailwindcss()],
  server: {
    proxy: {
      '/sanctum':              proxyOpts,
      '/login':                proxyOpts,
      '/logout':               proxyOpts,
      '/mobile-pos-details':   proxyOpts,
      '/products':             proxyOpts,
      '/pos':                  proxyOpts,
      '/sells':                proxyOpts,
      '/contacts':             proxyOpts,
      '/api':                  proxyOpts,
    }
  }
})
