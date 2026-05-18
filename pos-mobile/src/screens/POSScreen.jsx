import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../api/client'
import useCart from '../store/useCart'
import SearchBar from '../components/SearchBar'
import CartSheet from '../components/CartSheet'
import BottomBar from '../components/BottomBar'
import PaymentModal from '../components/PaymentModal'

export default function POSScreen() {
  const nav = useNavigate()
  const [location, setLocation] = useState(null)
  const [user, setUser] = useState(null)
  const [cartOpen, setCartOpen] = useState(false)
  const [payOpen, setPayOpen] = useState(false)
  const [success, setSuccess] = useState(false)
  const setLocationStore = useCart(s => s.setLocation)
  const addItem = useCart(s => s.addItem)
  const items = useCart(s => s.items)
  const [products, setProducts] = useState([])
  const [loadingProducts, setLoadingProducts] = useState(false)

  // Load user + location on mount
  useEffect(() => {
    api.get('/mobile-pos-details')
      .then(r => {
        const data = r.data
        const loc = data.default_location || data.business_locations?.[0]
        setLocation(loc)
        setUser(data.user)
        if (loc?.id) {
          setLocationStore(loc.id)
          loadProducts('', loc.id)
        }
      })
      .catch(() => nav('/login'))
  }, [])

  // Auto-open cart when item added
  useEffect(() => {
    if (items.length > 0) setCartOpen(true)
  }, [items.length])

  async function loadProducts(term = '', locId) {
    setLoadingProducts(true)
    try {
      const res = await api.get('/products/list', {
        params: { term: term || '', location_id: locId || location?.id, not_for_selling: 0 }
      })
      const data = res.data?.products || res.data || []
      setProducts(data.filter(p => p.enable_stock != 1 || parseFloat(p.qty_available || 0) > 0))
    } catch { /* ignore */ } finally {
      setLoadingProducts(false)
    }
  }

  function handleSuccess() {
    setSuccess(true)
    setTimeout(() => setSuccess(false), 2500)
  }

  async function logout() {
    await api.post('/logout').catch(() => {})
    nav('/login')
  }

  return (
    <div className="min-h-svh bg-gray-50 flex flex-col" style={{ paddingBottom: 112 }}>
      {/* Header */}
      <div className="bg-gradient-to-r from-blue-600 to-blue-700 px-4 pt-safe flex items-center justify-between h-14 shrink-0 sticky top-0 z-20">
        <div className="flex items-center gap-2">
          <svg className="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <span className="text-white font-semibold text-sm truncate max-w-[130px]">
            {location?.name || 'Loading…'}
          </span>
        </div>

        <span className="text-white font-bold text-base tracking-tight absolute left-1/2 -translate-x-1/2">
          Serengeti
        </span>

        <button onClick={logout} className="text-white/70 active:text-white">
          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
        </button>
      </div>

      {/* Search */}
      <div className="px-4 pt-3 pb-2 sticky top-14 z-10 bg-gray-50">
        <SearchBar locationId={location?.id} />
      </div>

      {/* Product grid */}
      <div className="flex-1 px-3 pb-2">
        {loadingProducts ? (
          <div className="flex items-center justify-center py-16 text-gray-300">
            <svg className="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"/>
              <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
          </div>
        ) : (
          <div className="grid grid-cols-2 gap-2.5">
            {products.map(p => (
              <button
                key={p.variation_id}
                onClick={() => addItem({
                  variation_id: p.variation_id,
                  name: p.name + (p.variation && p.variation !== 'DUMMY' ? ` · ${p.variation}` : ''),
                  price: parseFloat(p.selling_price || 0),
                  unit: p.unit || '',
                })}
                className="bg-white rounded-2xl p-3.5 shadow-sm border border-gray-100 text-left active:scale-95 transition-transform"
              >
                <p className="text-sm font-semibold text-gray-800 leading-snug mb-1 line-clamp-2">
                  {p.name}
                  {p.variation && p.variation !== 'DUMMY' && (
                    <span className="text-gray-400 font-normal"> · {p.variation}</span>
                  )}
                </p>
                {p.sub_sku && (
                  <p className="text-[10px] text-gray-400 font-mono mb-2">{p.sub_sku}</p>
                )}
                <p className="text-base font-black text-green-600">
                  Ksh {parseFloat(p.selling_price || 0).toLocaleString('en-KE', { minimumFractionDigits: 2 })}
                </p>
              </button>
            ))}
          </div>
        )}
      </div>

      {/* Cart sheet */}
      <CartSheet open={cartOpen} onClose={() => setCartOpen(false)} />

      {/* Bottom bar */}
      <BottomBar
        cartOpen={cartOpen}
        onCartToggle={() => setCartOpen(o => !o)}
        onPay={() => { setCartOpen(false); setPayOpen(true) }}
        onCash={() => { setCartOpen(false); setPayOpen(true) }}
      />

      {/* Payment modal */}
      <PaymentModal
        open={payOpen}
        onClose={() => setPayOpen(false)}
        locationId={location?.id}
        onSuccess={handleSuccess}
      />

      {/* Success toast */}
      {success && (
        <div className="fixed top-20 left-1/2 -translate-x-1/2 z-[70] bg-green-500 text-white px-5 py-3 rounded-2xl shadow-xl font-semibold text-sm flex items-center gap-2">
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7"/>
          </svg>
          Sale completed!
        </div>
      )}
    </div>
  )
}
