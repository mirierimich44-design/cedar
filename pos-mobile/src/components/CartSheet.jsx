import useCart from '../store/useCart'

function fmt(n) {
  return parseFloat(n || 0).toLocaleString('en-KE', { minimumFractionDigits: 2 })
}

export default function CartSheet({ open, onClose }) {
  const items = useCart(s => s.items)
  const updateQty = useCart(s => s.updateQty)
  const removeItem = useCart(s => s.removeItem)
  const total = useCart(s => s.total)

  return (
    <>
      {/* Backdrop */}
      {open && (
        <div
          className="fixed inset-0 bg-black/40 z-40"
          onClick={onClose}
        />
      )}

      {/* Sheet */}
      <div className={`fixed left-0 right-0 bottom-[112px] z-50 bg-white rounded-t-3xl shadow-2xl transition-transform duration-300 ${open ? 'translate-y-0' : 'translate-y-full'}`}
        style={{ maxHeight: '78vh', display: 'flex', flexDirection: 'column' }}
      >
        {/* Handle */}
        <div className="flex justify-center pt-3 pb-1 shrink-0">
          <div className="w-10 h-1 bg-gray-200 rounded-full"/>
        </div>

        {/* Header */}
        <div className="flex items-center justify-between px-5 py-3 border-b border-gray-100 shrink-0">
          <h2 className="font-bold text-gray-800 text-base">Current Sale</h2>
          <button onClick={onClose} className="text-gray-400 p-1">
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
        </div>

        {/* Items */}
        <div className="flex-1 overflow-y-auto">
          {items.length === 0 ? (
            <div className="flex flex-col items-center justify-center py-12 text-gray-300">
              <svg className="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <p className="text-sm">Cart is empty</p>
            </div>
          ) : items.map(item => (
            <div key={item.variation_id} className="flex items-center gap-3 px-5 py-3.5 border-b border-gray-50">
              {/* Name */}
              <div className="flex-1 min-w-0">
                <p className="text-sm font-semibold text-gray-800 leading-snug">{item.name}</p>
                <p className="text-xs text-green-600 font-bold mt-0.5">Ksh {fmt(item.price)}</p>
              </div>

              {/* Qty controls */}
              <div className="flex items-center gap-1.5 shrink-0">
                <button
                  onClick={() => updateQty(item.variation_id, item.qty - 1)}
                  className="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 active:bg-gray-200 font-bold text-base"
                >−</button>
                <span className="w-8 text-center text-sm font-bold text-gray-800">{item.qty}</span>
                <button
                  onClick={() => updateQty(item.variation_id, item.qty + 1)}
                  className="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 active:bg-blue-200 font-bold text-base"
                >+</button>
              </div>

              {/* Line total */}
              <span className="w-20 text-right text-sm font-bold text-gray-700 shrink-0">
                {fmt(item.price * item.qty)}
              </span>

              {/* Delete */}
              <button
                onClick={() => removeItem(item.variation_id)}
                className="text-red-400 active:text-red-600 shrink-0 p-1"
              >
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          ))}
        </div>

        {/* Total row */}
        {items.length > 0 && (
          <div className="flex items-center justify-between px-5 py-3 border-t border-gray-100 shrink-0 bg-gray-50 rounded-b-3xl">
            <span className="text-sm font-semibold text-gray-500">Total</span>
            <span className="text-xl font-black text-blue-600">Ksh {fmt(total)}</span>
          </div>
        )}
      </div>
    </>
  )
}
