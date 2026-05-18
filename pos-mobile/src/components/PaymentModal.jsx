import { useState } from 'react'
import useCart from '../store/useCart'
import api from '../api/client'

function fmt(n) {
  return parseFloat(n || 0).toLocaleString('en-KE', { minimumFractionDigits: 2 })
}

export default function PaymentModal({ open, onClose, locationId, onSuccess }) {
  const items = useCart(s => s.items)
  const total = useCart(s => s.total)
  const customerId = useCart(s => s.customerId)
  const clear = useCart(s => s.clear)
  const [method, setMethod] = useState('cash')
  const [tendered, setTendered] = useState('')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')

  const change = Math.max(0, parseFloat(tendered || 0) - total)

  async function submit() {
    if (!items.length) return
    setLoading(true); setError('')
    try {
      const products = {}
      items.forEach((item, i) => {
        products[i] = {
          variation_id: item.variation_id,
          quantity: item.qty,
          unit_price: item.price,
          line_discount_type: 'fixed',
          line_discount_amount: 0,
          item_tax: 0,
          tax_id: '',
        }
      })

      const payload = new URLSearchParams()
      payload.set('location_id', locationId)
      payload.set('contact_id', customerId || '')
      payload.set('transaction_date', new Date().toISOString().slice(0, 10))
      payload.set('sale_note', '')
      payload.set('discount_type', 'fixed')
      payload.set('discount_amount', '0')
      payload.set('tax_rate_id', '')
      payload.set('status', 'final')
      payload.set('is_quotation', '0')
      payload.set('payment[0][method]', method)
      payload.set('payment[0][amount]', total.toFixed(2))
      payload.set('payment[0][note]', '')

      Object.entries(products).forEach(([i, p]) => {
        Object.entries(p).forEach(([k, v]) => {
          payload.set(`products[${i}][${k}]`, v)
        })
      })

      await api.post('/pos', payload, {
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      })

      clear()
      onSuccess?.()
      onClose()
    } catch (err) {
      setError(err.response?.data?.msg || 'Sale failed. Please try again.')
    } finally {
      setLoading(false)
    }
  }

  if (!open) return null

  const methods = [
    { id: 'cash',  label: 'Cash',   icon: '💵' },
    { id: 'card',  label: 'Card',   icon: '💳' },
    { id: 'mpesa', label: 'M-Pesa', icon: '📱' },
  ]

  return (
    <div className="fixed inset-0 z-[60] flex items-end">
      <div className="absolute inset-0 bg-black/50" onClick={onClose}/>
      <div className="relative w-full bg-white rounded-t-3xl shadow-2xl p-5 pb-8">
        {/* Handle */}
        <div className="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-4"/>

        <h2 className="text-lg font-bold text-gray-800 mb-4">Complete Payment</h2>

        {error && <p className="text-red-500 text-sm mb-3 bg-red-50 px-3 py-2 rounded-xl">{error}</p>}

        {/* Total */}
        <div className="flex justify-between items-center bg-blue-50 rounded-2xl px-4 py-3 mb-4">
          <span className="text-sm font-semibold text-gray-500">Total Amount</span>
          <span className="text-2xl font-black text-blue-600">Ksh {fmt(total)}</span>
        </div>

        {/* Method */}
        <p className="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Payment Method</p>
        <div className="flex gap-2 mb-4">
          {methods.map(m => (
            <button
              key={m.id}
              onClick={() => setMethod(m.id)}
              className={`flex-1 py-2.5 rounded-xl text-sm font-bold border-2 transition-all ${
                method === m.id
                  ? 'border-blue-500 bg-blue-50 text-blue-700'
                  : 'border-gray-200 text-gray-500'
              }`}
            >
              {m.icon} {m.label}
            </button>
          ))}
        </div>

        {/* Cash tendered */}
        {method === 'cash' && (
          <div className="mb-4">
            <label className="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 block">
              Amount Tendered
            </label>
            <input
              type="number"
              value={tendered}
              onChange={e => setTendered(e.target.value)}
              placeholder={total.toFixed(2)}
              className="w-full border border-gray-200 rounded-xl px-4 py-3 text-lg font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            {tendered && (
              <div className="flex justify-between mt-2 px-1">
                <span className="text-sm text-gray-500">Change</span>
                <span className="text-sm font-bold text-green-600">Ksh {fmt(change)}</span>
              </div>
            )}
          </div>
        )}

        <button
          onClick={submit}
          disabled={loading}
          className="w-full py-4 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-bold text-base rounded-2xl shadow-lg active:scale-95 transition-transform disabled:opacity-60"
        >
          {loading ? 'Processing…' : `Confirm Payment · Ksh ${fmt(total)}`}
        </button>
      </div>
    </div>
  )
}
