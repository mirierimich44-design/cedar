import { View, Text, TouchableOpacity, StyleSheet } from 'react-native'
import { useSafeAreaInsets } from 'react-native-safe-area-context'
import useCart from '../store/useCart'

const fmt = (n) => parseFloat(n || 0).toLocaleString('en-KE', { minimumFractionDigits: 2 })

export default function BottomBar({ cartOpen, onCartToggle, onPay, onExpense }) {
  const insets = useSafeAreaInsets()
  const items  = useCart(s => s.items)
  const total  = items.reduce((s, i) => s + i.price * i.qty, 0)
  const count  = items.reduce((s, i) => s + i.qty, 0)

  return (
    <View style={[styles.bar, { paddingBottom: insets.bottom + 12 }]}>

      {/* Cart trigger */}
      <TouchableOpacity style={styles.cartArea} onPress={onCartToggle} activeOpacity={0.8}>
        <Text style={styles.cartIcon}>🛒</Text>
        {count > 0 && (
          <View style={styles.badge}>
            <Text style={styles.badgeText}>{count}</Text>
          </View>
        )}
        <Text style={styles.cartLabel}>{cartOpen ? 'Close' : 'View Cart'}</Text>
        <Text style={styles.cartTotal}>  Ksh {fmt(total)}</Text>
      </TouchableOpacity>

      {/* Expense button */}
      <TouchableOpacity style={styles.expenseBtn} onPress={onExpense} activeOpacity={0.85}>
        <Text style={styles.expenseText}>+ Expense</Text>
      </TouchableOpacity>

      {/* Pay button */}
      <TouchableOpacity
        style={[styles.payBtn, !items.length && styles.payBtnOff]}
        onPress={onPay}
        disabled={!items.length}
        activeOpacity={0.85}
      >
        <Text style={styles.payText}>Pay</Text>
      </TouchableOpacity>

    </View>
  )
}

const styles = StyleSheet.create({
  bar: {
    position: 'absolute', bottom: 0, left: 0, right: 0,
    backgroundColor: '#1e293b', flexDirection: 'row',
    alignItems: 'center', paddingHorizontal: 16, paddingTop: 12,
    shadowColor: '#000', shadowOffset: { width: 0, height: -4 }, shadowOpacity: 0.18, shadowRadius: 14, elevation: 14,
  },
  cartArea: { flex: 1, flexDirection: 'row', alignItems: 'center' },
  cartIcon: { fontSize: 20 },
  badge: {
    backgroundColor: '#ef4444', borderRadius: 10, minWidth: 20, height: 20,
    alignItems: 'center', justifyContent: 'center', paddingHorizontal: 4, marginLeft: 4,
  },
  badgeText: { color: '#fff', fontSize: 11, fontWeight: '700' },
  cartLabel: { color: 'rgba(255,255,255,0.65)', fontSize: 13, marginLeft: 8 },
  cartTotal: { color: '#fff', fontWeight: '700', fontSize: 15 },

  expenseBtn:  { backgroundColor: '#475569', borderRadius: 14, paddingHorizontal: 14, paddingVertical: 13, marginRight: 8 },
  expenseText: { color: '#fff', fontWeight: '600', fontSize: 13 },
  payBtn:    { backgroundColor: '#2563eb', borderRadius: 14, paddingHorizontal: 26, paddingVertical: 13 },
  payBtnOff: { opacity: 0.45 },
  payText:   { color: '#fff', fontWeight: '700', fontSize: 15 },
})
