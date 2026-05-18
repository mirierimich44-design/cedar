import { create } from 'zustand'

const useCart = create((set, get) => ({
  items: [],          // { variation_id, name, price, qty, unit }
  locationId: null,
  customerId: null,
  customerName: 'Walk-In Customer',

  setLocation: (id) => set({ locationId: id }),
  setCustomer: (id, name) => set({ customerId: id, customerName: name }),

  addItem(product) {
    const items = get().items
    const existing = items.find(i => i.variation_id === product.variation_id)
    if (existing) {
      set({ items: items.map(i =>
        i.variation_id === product.variation_id
          ? { ...i, qty: i.qty + 1 }
          : i
      )})
    } else {
      set({ items: [...items, { ...product, qty: 1 }] })
    }
  },

  removeItem(variation_id) {
    set({ items: get().items.filter(i => i.variation_id !== variation_id) })
  },

  updateQty(variation_id, qty) {
    if (qty <= 0) { get().removeItem(variation_id); return }
    set({ items: get().items.map(i =>
      i.variation_id === variation_id ? { ...i, qty } : i
    )})
  },

  updatePrice(variation_id, price) {
    set({ items: get().items.map(i =>
      i.variation_id === variation_id ? { ...i, price } : i
    )})
  },

  clear: () => set({ items: [], customerId: null, customerName: 'Walk-In Customer' }),

  get total() {
    return get().items.reduce((s, i) => s + i.price * i.qty, 0)
  },

  get count() {
    return get().items.reduce((s, i) => s + i.qty, 0)
  },
}))

export default useCart
