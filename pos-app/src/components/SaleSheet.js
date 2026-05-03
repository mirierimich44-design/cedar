import { useState, useEffect } from 'react'
import {
  View, Text, Modal, TouchableOpacity, TextInput, StyleSheet,
  ScrollView, ActivityIndicator, TouchableWithoutFeedback, Alert,
} from 'react-native'
import { useSafeAreaInsets } from 'react-native-safe-area-context'
import api from '../api/client'
import useCart from '../store/useCart'

const fmt = (n) => parseFloat(n || 0).toLocaleString('en-KE', { minimumFractionDigits: 2 })
const METHOD_ICONS = { cash: '💵', card: '💳', mpesa: '📱' }
const DEFAULT_METHODS = [
  { id: 'cash',  label: 'Cash' },
  { id: 'card',  label: 'Card' },
  { id: 'mpesa', label: 'M-Pesa' },
]

export default function SaleSheet({ open, onClose, initialProduct, locationId, onSuccess }) {
  const insets = useSafeAreaInsets()

  const items      = useCart(s => s.items)
  const addItem    = useCart(s => s.addItem)
  const updateQty  = useCart(s => s.updateQty)
  const removeItem = useCart(s => s.removeItem)
  const clear      = useCart(s => s.clear)
  const total      = items.reduce((s, i) => s + i.price * i.qty, 0)
  const count      = items.reduce((s, i) => s + i.qty, 0)

  const [product,    setProduct]    = useState(null)
  const [pdQty,      setPdQty]      = useState(1)
  const [methods,    setMethods]    = useState(DEFAULT_METHODS)
  const [method,     setMethod]     = useState('cash')
  const [tendered,   setTendered]   = useState('')
  const [submitting, setSubmitting] = useState(false)
  const [error,      setError]      = useState('')

  const change = Math.max(0, parseFloat(tendered || 0) - total)

  useEffect(() => {
    if (!open) return
    setError('')
    setTendered('')
    setProduct(initialProduct || null)
    setPdQty(1)
    loadMethods()
  }, [open, initialProduct])

  async function loadMethods() {
    try {
      const res = await api.get('/api/mobile/payment-types')
      if (res.data?.payment_types?.length) setMethods(res.data.payment_types)
    } catch { /* use defaults */ }
  }

  function handleClose() {
    onClose()
  }

  function confirmAddProduct() {
    if (!product) return
    addItem({
      variation_id: product.variation_id,
      name: product.name + (product.variation && product.variation !== 'DUMMY' ? ` · ${product.variation}` : ''),
      price: parseFloat(product.selling_price || 0),
      unit: product.unit || '',
      qty: pdQty,
    })
    setProduct(null)
    setPdQty(1)
  }

  async function submitSale() {
    if (!items.length) return

    // Validate cash tendered
    if (method === 'cash' && tendered !== '') {
      const tenderedAmt = parseFloat(tendered)
      if (isNaN(tenderedAmt) || tenderedAmt < total) {
        setError(`Tendered amount must be at least Ksh ${fmt(total)}`)
        return
      }
    }

    setSubmitting(true); setError('')
    try {
      await api.post('/api/mobile/sale', {
        location_id:      locationId,
        transaction_date: new Date().toISOString().slice(0, 10),
        sale_note:        '',
        payment: [{ method, amount: total.toFixed(2), note: '' }],
        products: items.map(i => ({
          variation_id:         i.variation_id,
          quantity:             i.qty,
          unit_price:           i.price,
          line_discount_type:   'fixed',
          line_discount_amount: 0,
          item_tax:             0,
          tax_id:               '',
        })),
      })
      clear()
      setTendered('')
      onClose()
      onSuccess?.()       // triggers product list reload in POSScreen
    } catch (e) {
      setError(e.response?.data?.msg || e.response?.data?.message || 'Sale failed. Please try again.')
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <Modal visible={open} transparent animationType="slide" onRequestClose={handleClose}>
      <TouchableWithoutFeedback onPress={handleClose}>
        <View style={styles.backdrop} />
      </TouchableWithoutFeedback>

      <View style={[styles.sheet, { paddingBottom: insets.bottom + 12 }]}>

        {/* Header row */}
        <View style={styles.sheetHeader}>
          <View style={styles.handle} />
          <View style={styles.headerRow}>
            <Text style={styles.sheetTitle}>
              {count > 0 ? `Order  ·  ${count} item${count !== 1 ? 's' : ''}` : 'New Order'}
            </Text>
            <TouchableOpacity onPress={handleClose} style={styles.closeBtn} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}>
              <Text style={styles.closeText}>✕</Text>
            </TouchableOpacity>
          </View>
        </View>

        <ScrollView showsVerticalScrollIndicator={false} keyboardShouldPersistTaps="handled">

          {/* ── Adding product ── */}
          {product && (
            <View style={styles.addSection}>
              <View style={styles.addRow}>
                <View style={{ flex: 1 }}>
                  <Text style={styles.addName} numberOfLines={1}>
                    {product.name}{product.variation && product.variation !== 'DUMMY' ? ` · ${product.variation}` : ''}
                  </Text>
                  <Text style={styles.addPrice}>Ksh {fmt(product.selling_price)} each</Text>
                </View>
                <View style={styles.qtyRow}>
                  <TouchableOpacity style={styles.qtyBtn} onPress={() => setPdQty(q => Math.max(1, q - 1))}>
                    <Text style={styles.qtyBtnText}>−</Text>
                  </TouchableOpacity>
                  <TextInput
                    style={styles.qtyInput}
                    value={String(pdQty)}
                    onChangeText={v => setPdQty(Math.max(1, parseInt(v) || 1))}
                    keyboardType="number-pad"
                    selectTextOnFocus
                  />
                  <TouchableOpacity style={styles.qtyBtn} onPress={() => setPdQty(q => q + 1)}>
                    <Text style={styles.qtyBtnText}>+</Text>
                  </TouchableOpacity>
                </View>
              </View>
              <TouchableOpacity style={styles.addBtn} onPress={confirmAddProduct} activeOpacity={0.85}>
                <Text style={styles.addBtnText}>
                  Add to Order  ·  Ksh {fmt(parseFloat(product.selling_price || 0) * pdQty)}
                </Text>
              </TouchableOpacity>
            </View>
          )}

          {/* ── Order items ── */}
          {items.length > 0 && (
            <View style={styles.section}>
              <View style={styles.sectionHeader}>
                <Text style={styles.sectionLabel}>ITEMS</Text>
                <TouchableOpacity onPress={() => Alert.alert('Clear order', 'Remove all items?', [
                  { text: 'Cancel', style: 'cancel' },
                  { text: 'Clear', style: 'destructive', onPress: clear },
                ])}>
                  <Text style={styles.clearText}>Clear all</Text>
                </TouchableOpacity>
              </View>
              {items.map(item => (
                <View key={String(item.variation_id)} style={styles.itemRow}>
                  <View style={styles.itemLeft}>
                    <Text style={styles.itemName} numberOfLines={1}>{item.name}</Text>
                    <Text style={styles.itemPrice}>Ksh {fmt(item.price)}</Text>
                  </View>
                  <View style={styles.itemRight}>
                    <TouchableOpacity style={styles.itemQtyBtn} onPress={() => updateQty(item.variation_id, item.qty - 1)}>
                      <Text style={styles.itemQtyBtnText}>−</Text>
                    </TouchableOpacity>
                    <Text style={styles.itemQty}>{item.qty}</Text>
                    <TouchableOpacity style={styles.itemQtyBtn} onPress={() => updateQty(item.variation_id, item.qty + 1)}>
                      <Text style={styles.itemQtyBtnText}>+</Text>
                    </TouchableOpacity>
                    <Text style={styles.itemTotal}>Ksh {fmt(item.price * item.qty)}</Text>
                    <TouchableOpacity onPress={() => removeItem(item.variation_id)} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
                      <Text style={styles.delIcon}>🗑</Text>
                    </TouchableOpacity>
                  </View>
                </View>
              ))}
              {/* Subtotal */}
              <View style={styles.subtotalRow}>
                <Text style={styles.subtotalLabel}>Total</Text>
                <Text style={styles.subtotalAmt}>Ksh {fmt(total)}</Text>
              </View>
            </View>
          )}

          {/* ── Payment ── */}
          {items.length > 0 && (
            <View style={styles.section}>
              <Text style={styles.sectionLabel}>PAYMENT METHOD</Text>

              {!!error && <View style={styles.errorBox}><Text style={styles.errorText}>{error}</Text></View>}

              <View style={styles.methods}>
                {methods.map(m => (
                  <TouchableOpacity
                    key={m.id}
                    style={[styles.methodChip, method === m.id && styles.methodChipActive]}
                    onPress={() => setMethod(m.id)}
                    activeOpacity={0.8}
                  >
                    <Text style={styles.methodIcon}>{METHOD_ICONS[m.id] || '💰'}</Text>
                    <Text style={[styles.methodLabel, method === m.id && styles.methodLabelActive]}>{m.label}</Text>
                  </TouchableOpacity>
                ))}
              </View>

              {method === 'cash' && (
                <View style={{ marginBottom: 10 }}>
                  <TextInput
                    style={styles.tenderedInput}
                    value={tendered}
                    onChangeText={setTendered}
                    placeholder={`Tendered (${total.toFixed(2)})`}
                    placeholderTextColor="#9ca3af"
                    keyboardType="decimal-pad"
                  />
                  {!!tendered && (
                    <View style={styles.changeRow}>
                      <Text style={styles.changeLabel}>Change</Text>
                      <Text style={styles.changeAmt}>Ksh {fmt(change)}</Text>
                    </View>
                  )}
                </View>
              )}

              <TouchableOpacity
                style={[styles.chargeBtn, submitting && { opacity: 0.6 }]}
                onPress={submitSale}
                disabled={submitting}
                activeOpacity={0.85}
              >
                {submitting
                  ? <ActivityIndicator color="#fff" />
                  : <Text style={styles.chargeBtnText}>Charge  Ksh {fmt(total)}</Text>
                }
              </TouchableOpacity>
            </View>
          )}

          {/* Empty state */}
          {!product && items.length === 0 && (
            <View style={styles.emptyState}>
              <Text style={styles.emptyIcon}>🛒</Text>
              <Text style={styles.emptyText}>No items yet</Text>
              <Text style={styles.emptyHint}>Tap a product card to add it</Text>
            </View>
          )}

        </ScrollView>
      </View>
    </Modal>
  )
}

const styles = StyleSheet.create({
  backdrop: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)' },
  sheet: {
    backgroundColor: '#f1f5f9', borderTopLeftRadius: 24, borderTopRightRadius: 24,
    maxHeight: '90%', paddingHorizontal: 12,
    shadowColor: '#000', shadowOffset: { width: 0, height: -4 }, shadowOpacity: 0.12, shadowRadius: 16, elevation: 20,
  },

  sheetHeader: { paddingTop: 8, marginBottom: 10 },
  handle:      { width: 36, height: 4, backgroundColor: '#cbd5e1', borderRadius: 2, alignSelf: 'center', marginBottom: 10 },
  headerRow:   { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  sheetTitle:  { fontSize: 16, fontWeight: '800', color: '#0f172a' },
  closeBtn:    { backgroundColor: '#e2e8f0', borderRadius: 10, width: 28, height: 28, alignItems: 'center', justifyContent: 'center' },
  closeText:   { fontSize: 13, color: '#64748b', fontWeight: '700' },

  section: {
    backgroundColor: '#fff', borderRadius: 16, padding: 12, marginBottom: 10,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.04, shadowRadius: 3, elevation: 1,
  },
  addSection: {
    backgroundColor: '#eff6ff', borderRadius: 16, padding: 12, marginBottom: 10,
    borderWidth: 1.5, borderColor: '#bfdbfe',
  },
  sectionHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 8 },
  sectionLabel:  { fontSize: 10, fontWeight: '700', color: '#94a3b8', letterSpacing: 0.8 },
  clearText:     { fontSize: 12, color: '#ef4444', fontWeight: '600' },

  addRow:   { flexDirection: 'row', alignItems: 'center', gap: 10, marginBottom: 10 },
  addName:  { fontSize: 13, fontWeight: '700', color: '#1e293b' },
  addPrice: { fontSize: 11, color: '#64748b', marginTop: 1 },

  qtyRow:    { flexDirection: 'row', alignItems: 'center', gap: 6 },
  qtyBtn:    { width: 30, height: 30, borderRadius: 8, backgroundColor: '#fff', alignItems: 'center', justifyContent: 'center', elevation: 1 },
  qtyBtnText:{ fontSize: 18, color: '#1e293b', lineHeight: 22 },
  qtyInput:  { width: 38, textAlign: 'center', fontSize: 15, fontWeight: '800', color: '#1e293b', borderWidth: 1.5, borderColor: '#dbeafe', borderRadius: 8, paddingVertical: 4, backgroundColor: '#fff' },

  addBtn:     { backgroundColor: '#2563eb', borderRadius: 12, paddingVertical: 11, alignItems: 'center' },
  addBtnText: { color: '#fff', fontWeight: '700', fontSize: 13 },

  itemRow:   { flexDirection: 'row', alignItems: 'center', paddingVertical: 7, borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  itemLeft:  { flex: 1, paddingRight: 8 },
  itemName:  { fontSize: 13, fontWeight: '600', color: '#1e293b' },
  itemPrice: { fontSize: 11, color: '#64748b', marginTop: 1 },
  itemRight: { flexDirection: 'row', alignItems: 'center', gap: 5 },
  itemQtyBtn:    { width: 26, height: 26, borderRadius: 7, backgroundColor: '#f1f5f9', alignItems: 'center', justifyContent: 'center' },
  itemQtyBtnText:{ fontSize: 16, color: '#334155', lineHeight: 20 },
  itemQty:   { fontSize: 13, fontWeight: '700', color: '#1e293b', minWidth: 20, textAlign: 'center' },
  itemTotal: { fontSize: 12, fontWeight: '700', color: '#0f172a', minWidth: 72, textAlign: 'right' },
  delIcon:   { fontSize: 14 },

  subtotalRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 10, paddingTop: 8, borderTopWidth: 1, borderTopColor: '#e2e8f0' },
  subtotalLabel:{ fontSize: 13, color: '#64748b', fontWeight: '500' },
  subtotalAmt:  { fontSize: 18, fontWeight: '800', color: '#2563eb' },

  errorBox:  { backgroundColor: '#fef2f2', borderRadius: 10, padding: 8, marginBottom: 10 },
  errorText: { color: '#dc2626', fontSize: 12 },

  methods:          { flexDirection: 'row', gap: 6, marginBottom: 10, marginTop: 6 },
  methodChip:       { flex: 1, borderWidth: 1.5, borderColor: '#e2e8f0', borderRadius: 12, paddingVertical: 8, alignItems: 'center' },
  methodChipActive: { borderColor: '#2563eb', backgroundColor: '#eff6ff' },
  methodIcon:       { fontSize: 18, marginBottom: 2 },
  methodLabel:      { fontSize: 11, fontWeight: '600', color: '#64748b' },
  methodLabelActive:{ color: '#2563eb' },

  tenderedInput: {
    borderWidth: 1.5, borderColor: '#e2e8f0', borderRadius: 12,
    paddingHorizontal: 14, paddingVertical: 10,
    fontSize: 18, fontWeight: '700', color: '#1e293b', marginBottom: 6,
  },
  changeRow:  { flexDirection: 'row', justifyContent: 'space-between', paddingHorizontal: 2, marginBottom: 6 },
  changeLabel:{ fontSize: 13, color: '#64748b' },
  changeAmt:  { fontSize: 13, fontWeight: '700', color: '#16a34a' },

  chargeBtn:     { backgroundColor: '#16a34a', borderRadius: 14, paddingVertical: 14, alignItems: 'center' },
  chargeBtnText: { color: '#fff', fontWeight: '800', fontSize: 15 },

  emptyState: { alignItems: 'center', paddingVertical: 36 },
  emptyIcon:  { fontSize: 36, marginBottom: 10 },
  emptyText:  { fontSize: 15, fontWeight: '700', color: '#1e293b', marginBottom: 4 },
  emptyHint:  { fontSize: 12, color: '#94a3b8' },
})
