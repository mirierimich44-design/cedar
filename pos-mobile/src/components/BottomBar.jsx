import useCart from '../store/useCart'

function fmt(n) {
  return parseFloat(n || 0).toLocaleString('en-KE', { minimumFractionDigits: 2 })
}

export default function BottomBar({ onCartToggle, cartOpen, onPay, onCash }) {
  const items  = useCart(s => s.items)
  const total  = useCart(s => s.total)
  const count  = items.reduce((s, i) => s + i.qty, 0)

  return (
    <div className="fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-100 shadow-[0_-4px_20px_rgba(0,0,0,0.08)]">
      {/* Cart trigger */}
      <button
        onClick={onCartToggle}
        className="w-full flex items-center justify-between px-4 py-2.5 bg-blue-50 border-b border-blue-100 active:bg-blue-100"
      >
        <div className="flex items-center gap-2 text-blue-600">
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span className="text-xs font-bold">
            {count > 0 ? `${count} item${count > 1 ? 's' : ''} in cart` : 'View Cart'}
          </span>
          {count > 0 && (
            <span className="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{count}</span>
          )}
          <svg className={`w-3.5 h-3.5 ml-1 transition-transform ${cartOpen ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7"/>
          </svg>
        </div>
        <span className="text-lg font-black text-blue-700">Ksh {fmt(total)}</span>
      </button>

      {/* Action buttons */}
      <div className="flex gap-2 px-3 py-2.5">
        <button
          onClick={onPay}
          disabled={count === 0}
          className="flex-[2] py-3.5 bg-gradient-to-r from-blue-400 to-blue-600 text-white font-bold rounded-2xl shadow-md active:scale-95 transition-transform disabled:opacity-40 flex items-center justify-center gap-2 text-sm"
        >
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
          </svg>
          Pay
        </button>

        <button
          onClick={onCash}
          disabled={count === 0}
          className="flex-1 py-3.5 bg-gradient-to-r from-teal-500 to-cyan-600 text-white font-bold rounded-2xl shadow-md active:scale-95 transition-transform disabled:opacity-40 flex items-center justify-center gap-2 text-sm"
        >
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          Cash
        </button>
      </div>
    </div>
  )
}
