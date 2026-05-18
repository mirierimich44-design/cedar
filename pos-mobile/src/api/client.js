import axios from 'axios'

// If VITE_API_URL is set (production .env.production) use it as the base.
// If blank (dev .env) use same-origin so Vite's proxy can forward the requests.
const BASE = import.meta.env.VITE_API_URL || ''

const api = axios.create({
  baseURL: BASE,
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

// Attach CSRF token to every mutating request
api.interceptors.request.use(cfg => {
  const token = document.cookie
    .split('; ')
    .find(r => r.startsWith('XSRF-TOKEN='))
    ?.split('=')[1]
  if (token) cfg.headers['X-XSRF-TOKEN'] = decodeURIComponent(token)
  return cfg
})

export default api
