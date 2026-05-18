import { useState } from 'react'
import {
  View, Text, Modal, TouchableOpacity, TextInput, StyleSheet,
  TouchableWithoutFeedback, ActivityIndicator,
} from 'react-native'
import useCart from '../store/useCart'
import api from '../api/client'

const fmt = (n) => parseFloat(n || 0).toLocaleString('en-KE', { minimumFractionDigits: 2 })

const METHODS = [
  { id: 'cash',  label: 'Cash',   icon: '💵' },
  { id: 'card',  label: 'Card',   icon: '💳' },
  { id: 'mpesa', label: 'M-Pesa', icon: '📱' },
]

export default function PaymentModal({ open, onClose, locationId, onSuccess }) {
  const items  = useCart(s => s.items)
  const total  = items.reduce((s, i) => s + i.price * i.qty, 0)
  const clear  = useCart(s => s.clear)

  const [method,   setMethod]   = useState('cash')
  const [tendered, setTendered] = useState('')
  const [loading,  setLoading]  = useState(false)
  const [error,    setError]    = useState('')

  const change = Math.max(0, parseFloat(tendered || 0) - total)

  async function submit() {
    if (!items.length) return
    setLoading(true); setError('')
    try {
      const body = {
        location_id:       locationId,
        contact_id:        '',
        transaction_date:  new Date().toISOString().slice(0, 10),
        sale_note:         '',
        discount_type:     'fixed',
        discount_amount:   '0',
        tax_rate_id:       '',
        status:            'final',
        is_quotation:      '0',
        payment: [{ method, amount: total.toFixed(2), note: '' }],
        products: items.map(i => ({
          variation_id:          i.variation_id,
          quantity:              i.qty,
          unit_price:            i.price,
          line_discount_type:    'fixed',
          line_discount_amount:  0,
          item_tax:              0,
          tax_id:                '',
        })),
      }

      await api.post('/api/mobile/sale', body)
      clear()
      onSuccess?.()
      onClose()
      setTendered('')
    } catch (err) {
      setError(err.response?.data?.msg || err.response?.data?.message || 'Sale failed. Please try again.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <Modal visible={open} transparent animationType="slide" onRequestClose={onClose}>
      <TouchableWithoutFeedback onPress={onClose}>
        <View style={styles.backdrop} />
      </TouchableWithoutFeedback>

      <View style={styles.sheet}>
        <View style={styles.handle} />
        <Text style={styles.title}>Complete Payment</Text>

        {!!error && <View style={styles.errorBox}><Text style={styles.errorText}>{error}</Text></View>}

        {/* Total */}
        <View style={styles.totalBox}>
          <Text style={styles.totalLabel}>Total Amount</Text>
          <Text style={styles.totalAmt}>Ksh {fmt(total)}</Text>
        </View>

        {/* Method selector */}
        <Text style={styles.sectionLabel}>PAYMENT METHOD</Text>
        <View style={styles.methods}>
          {METHODS.map(m => (
            <TouchableOpacity
              key={m.id}
              style={[styles.methodBtn, method === m.id && styles.methodActive]}
              onPress={() => setMethod(m.id)}
              activeOpacity={0.8}
            >
              <Text style={styles.methodIcon}>{m.icon}</Text>
              <Text style={[styles.methodLabel, method === m.id && styles.methodLabelActive]}>{m.label}</Text>
            </TouchableOpacity>
          ))}
        </View>

        {/* Cash tendered */}
        {method === 'cash' && (
          <View style={styles.tenderedWrap}>
            <Text style={styles.sectionLabel}>AMOUNT TENDERED</Text>
            <TextInput
              style={styles.tenderedInput}
              value={tendered}
              onChangeText={setTendered}
              placeholder={total.toFixed(2)}
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
          style={[styles.confirmBtn, loading && styles.confirmOff]}
          onPress={submit}
          disabled={loading}
          activeOpacity={0.85}
        >
          {loading
            ? <ActivityIndicator color="#fff" />
            : <Text style={styles.confirmText}>Confirm Payment · Ksh {fmt(total)}</Text>
          }
        </TouchableOpacity>
      </View>
    </Modal>
  )
}

const styles = StyleSheet.create({
  backdrop: { flex: 1, backgroundColor: 'rgba(0,0,0,0.55)' },
  sheet: {
    backgroundColor: '#fff', borderTopLeftRadius: 28, borderTopRightRadius: 28,
    padding: 22, paddingBottom: 40,
    shadowColor: '#000', shadowOffset: { width: 0, height: -4 }, shadowOpacity: 0.12, shadowRadius: 16, elevation: 20,
  },
  handle:    { width: 40, height: 4, backgroundColor: '#e2e8f0', borderRadius: 2, alignSelf: 'center', marginBottom: 18 },
  title:     { fontSize: 19, fontWeight: '800', color: '#0f172a', marginBottom: 16 },
  errorBox:  { backgroundColor: '#fef2f2', borderRadius: 12, padding: 12, marginBottom: 14 },
  errorText: { color: '#dc2626', fontSize: 13 },

  totalBox: {
    backgroundColor: '#eff6ff', borderRadius: 18, padding: 16,
    flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20,
  },
  totalLabel: { fontSize: 14, color: '#475569', fontWeight: '500' },
  totalAmt:   { fontSize: 26, fontWeight: '800', color: '#2563eb' },

  sectionLabel: { fontSize: 11, fontWeight: '700', color: '#64748b', letterSpacing: 1, marginBottom: 8 },
  methods:      { flexDirection: 'row', gap: 8, marginBottom: 20 },
  methodBtn: {
    flex: 1, borderWidth: 2, borderColor: '#e2e8f0', borderRadius: 14,
    paddingVertical: 10, alignItems: 'center',
  },
  methodActive:      { borderColor: '#2563eb', backgroundColor: '#eff6ff' },
  methodIcon:        { fontSize: 22, marginBottom: 4 },
  methodLabel:       { fontSize: 12, fontWeight: '600', color: '#64748b' },
  methodLabelActive: { color: '#2563eb' },

  tenderedWrap:  { marginBottom: 20 },
  tenderedInput: {
    borderWidth: 1.5, borderColor: '#e2e8f0', borderRadius: 14,
    paddingHorizontal: 16, paddingVertical: 13,
    fontSize: 20, fontWeight: '700', color: '#1e293b', marginBottom: 10,
  },
  changeRow:  { flexDirection: 'row', justifyContent: 'space-between', paddingHorizontal: 2 },
  changeLabel: { fontSize: 14, color: '#475569' },
  changeAmt:  { fontSize: 14, fontWeight: '700', color: '#16a34a' },

  confirmBtn:  { backgroundColor: '#2563eb', borderRadius: 16, paddingVertical: 16, alignItems: 'center' },
  confirmOff:  { opacity: 0.6 },
  confirmText: { color: '#fff', fontWeight: '700', fontSize: 15 },
})
