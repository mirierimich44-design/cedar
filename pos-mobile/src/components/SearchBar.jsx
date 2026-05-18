import { useState, useRef, useEffect, useCallback } from 'react'
import api from '../api/client'
import useCart from '../store/useCart'

export default function SearchBar({ locationId }) {
  const [term, setTerm] = useState('')
  const [results, setResults] = useState([])
  const [loading, setLoading] = useState(false)
  const [open, setOpen] = useState(false)
  const timerRef = useRef(null)
  const xhrRef = useRef(null)
  const addItem = useCart(s => s.addItem)

  const search = useCallback(async (q) => {
    if (!q) { setResults([]); setOpen(false); return }
    setLoading(true)
    try {
      const res = await api.get('/products/list', {
        params: { term: q, location_id: locationId, not_for_selling: 0 }
      })
      const data = res.data?.products || res.data || []
      // Filter out-of-stock
      const inStock = data.filter(p => p.enable_stock != 1 || parseFloat(p.qty_available || 0) > 0)
      setResults(inStock)
      setOpen(true)
    } catch { /* cancelled */ } finally {
      setLoading(false)
    }
  }, [locationId])

  useEffect(() => {
    clearTimeout(timerRef.current)
    if (!term.trim()) { setResults([]); setOpen(false); return }
    timerRef.current = setTimeout(() => search(term.trim()), 280)
    return () => clearTimeout(timerRef.current)
  }, [term, search])

  function pick(product) {
    addItem({
      variation_id: product.variation_id,
      name: product.name + (product.variation && product.variation !== 'DUMMY' ? ` · ${product.variation}` : ''),
      price: parseFloat(product.selling_price || 0),
      unit: product.unit || '',
    })
    setTerm('')
    setResults([])
    setOpen(false)
  }

  return (
    <div className="relative">
      {/* Input */}
      <div className="flex items-center bg-white border border-gray-200 rounded-2xl px-4 shadow-sm">
        <svg className="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input
          type="text"
          value={term}
          onChange={e => setTerm(e.target.value)}
          placeholder="Search products…"
          autoComplete="off"
          className="flex-1 py-3 px-3 text-sm text-gray-800 placeholder-gray-400 focus:outline-none bg-transparent"
        />
        {loading && (
          <svg className="w-4 h-4 text-blue-500 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"/>
            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
          </svg>
        )}
        {term && !loading && (
          <button onClick={() => { setTerm(''); setOpen(false) }} className="text-gray-400 p-0.5">
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        )}
      </div>

      {/* Dropdown */}
      {open && (
        <div className="absolute top-full left-0 right-0 z-50 mt-1 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden max-h-72 overflow-y-auto">
          {results.length === 0 ? (
            <p className="text-center text-gray-400 text-sm py-5">No products found</p>
          ) : results.map(p => (
            <button
              key={p.variation_id}
              onClick={() => pick(p)}
              className="w-full flex items-center justify-between px-4 py-3.5 border-b border-gray-50 last:border-0 active:bg-blue-50 text-left"
            >
              <span className="text-sm font-semibold text-gray-800 leading-snug flex-1 pr-3">
                {p.name}
                {p.variation && p.variation !== 'DUMMY' && (
                  <span className="font-normal text-gray-400"> · {p.variation}</span>
                )}
              </span>
              <span className="text-sm font-bold text-green-600 shrink-0">
                {parseFloat(p.selling_price || 0).toLocaleString('en-KE', { minimumFractionDigits: 2 })}
              </span>
            </button>
          ))}
        </div>
      )}
    </div>
  )
}
