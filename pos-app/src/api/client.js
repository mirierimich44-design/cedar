import axios from 'axios'
import * as SecureStore from 'expo-secure-store'

export const BASE = 'https://serengeti.apexpos.co.ke'
const TOKEN_KEY = 'pos_token'

export async function saveToken(token) {
  await SecureStore.setItemAsync(TOKEN_KEY, token)
}

export async function getToken() {
  return await SecureStore.getItemAsync(TOKEN_KEY)
}

export async function clearToken() {
  await SecureStore.deleteItemAsync(TOKEN_KEY)
}

const api = axios.create({
  baseURL: BASE,
  timeout: 15000,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

// Attach Bearer token to every request
api.interceptors.request.use(async (config) => {
  const token = await getToken()
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

// Handle 401 — token expired or revoked
let _unauthorizedHandler = null
export function setUnauthorizedHandler(fn) { _unauthorizedHandler = fn }

api.interceptors.response.use(
  response => response,
  async (error) => {
    if (error.response?.status === 401) {
      await clearToken()
      _unauthorizedHandler?.()
    }
    return Promise.reject(error)
  }
)

export default api
