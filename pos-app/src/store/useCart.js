import { create } from 'zustand'

const useCart = create((set, get) => ({
  items: [],
  locationId: null,

  setLocation: (id) => set({ locationId: id }),

  addItem: (product) => {
    const items = get().items
    const addQty = product.qty || 1
    const idx = items.findIndex(i => i.variation_id === product.variation_id)
    if (idx >= 0) {
      const updated = [...items]
      updated[idx] = { ...updated[idx], qty: updated[idx].qty + addQty }
      set({ items: updated })
    } else {
      set({ items: [...items, { ...product, qty: addQty }] })
    }
  },

  updateQty: (variation_id, qty) => {
    if (qty < 1) {
      set({ items: get().items.filter(i => i.variation_id !== variation_id) })
    } else {
      set({ items: get().items.map(i =>
        i.variation_id === variation_id ? { ...i, qty } : i
      )})
    }
  },

  removeItem: (variation_id) =>
    set({ items: get().items.filter(i => i.variation_id !== variation_id) }),

  clear: () => set({ items: [] }),
}))

export default useCart
