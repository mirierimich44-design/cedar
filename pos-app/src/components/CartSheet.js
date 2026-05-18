import {
  View, Text, Modal, FlatList, TouchableOpacity,
  StyleSheet, TouchableWithoutFeedback,
} from 'react-native'
import useCart from '../store/useCart'

const fmt = (n) => parseFloat(n || 0).toLocaleString('en-KE', { minimumFractionDigits: 2 })

export default function CartSheet({ open, onClose }) {
  const items      = useCart(s => s.items)
  const updateQty  = useCart(s => s.updateQty)
  const removeItem = useCart(s => s.removeItem)
  const total      = items.reduce((s, i) => s + i.price * i.qty, 0)

  const renderItem = ({ item }) => (
    <View style={styles.row}>
      <View style={styles.info}>
        <Text style={styles.name} numberOfLines={2}>{item.name}</Text>
        <Text style={styles.unitPrice}>Ksh {fmt(item.price)} each</Text>
      </View>
      <View style={styles.controls}>
        <TouchableOpacity style={styles.qtyBtn} onPress={() => updateQty(item.variation_id, item.qty - 1)}>
          <Text style={styles.qtyBtnText}>−</Text>
        </TouchableOpacity>
        <Text style={styles.qty}>{item.qty}</Text>
        <TouchableOpacity style={styles.qtyBtn} onPress={() => updateQty(item.variation_id, item.qty + 1)}>
          <Text style={styles.qtyBtnText}>+</Text>
        </TouchableOpacity>
        <Text style={styles.lineTotal}>Ksh {fmt(item.price * item.qty)}</Text>
        <TouchableOpacity onPress={() => removeItem(item.variation_id)} style={styles.del}>
          <Text style={styles.delText}>🗑</Text>
        </TouchableOpacity>
      </View>
    </View>
  )

  return (
    <Modal visible={open} transparent animationType="slide" onRequestClose={onClose}>
      <TouchableWithoutFeedback onPress={onClose}>
        <View style={styles.backdrop} />
      </TouchableWithoutFeedback>

      <View style={styles.sheet}>
        <View style={styles.handle} />

        <View style={styles.header}>
          <Text style={styles.title}>Cart</Text>
          <Text style={styles.count}>{items.reduce((s, i) => s + i.qty, 0)} items</Text>
        </View>

        {items.length === 0
          ? <View style={styles.empty}><Text style={styles.emptyText}>🛒  Cart is empty</Text></View>
          : <FlatList
              data={items}
              keyExtractor={i => String(i.variation_id)}
              renderItem={renderItem}
              style={styles.list}
              showsVerticalScrollIndicator={false}
            />
        }

        <View style={styles.totalRow}>
          <Text style={styles.totalLabel}>Total</Text>
          <Text style={styles.totalAmt}>Ksh {fmt(total)}</Text>
        </View>
      </View>
    </Modal>
  )
}

const styles = StyleSheet.create({
  backdrop: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)' },
  sheet: {
    backgroundColor: '#fff', borderTopLeftRadius: 28, borderTopRightRadius: 28,
    maxHeight: '82%', paddingBottom: 110,
    shadowColor: '#000', shadowOffset: { width: 0, height: -4 }, shadowOpacity: 0.1, shadowRadius: 16, elevation: 20,
  },
  handle: { width: 40, height: 4, backgroundColor: '#e2e8f0', borderRadius: 2, alignSelf: 'center', marginVertical: 10 },
  header: {
    flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center',
    paddingHorizontal: 20, paddingBottom: 12, borderBottomWidth: 1, borderBottomColor: '#f1f5f9',
  },
  title: { fontSize: 18, fontWeight: '800', color: '#0f172a' },
  count: { fontSize: 13, color: '#64748b', fontWeight: '500' },
  empty: { alignItems: 'center', paddingVertical: 48 },
  emptyText: { color: '#94a3b8', fontSize: 16 },
  list: { maxHeight: 420 },

  row: { paddingHorizontal: 16, paddingVertical: 12, borderBottomWidth: 1, borderBottomColor: '#f8fafc' },
  info: { marginBottom: 8 },
  name: { fontSize: 14, fontWeight: '600', color: '#1e293b', lineHeight: 20 },
  unitPrice: { fontSize: 12, color: '#64748b', marginTop: 2 },
  controls: { flexDirection: 'row', alignItems: 'center', gap: 8 },
  qtyBtn: {
    width: 32, height: 32, borderRadius: 9, backgroundColor: '#f1f5f9',
    alignItems: 'center', justifyContent: 'center',
  },
  qtyBtnText: { fontSize: 20, color: '#334155', lineHeight: 24 },
  qty: { fontSize: 15, fontWeight: '700', color: '#1e293b', minWidth: 26, textAlign: 'center' },
  lineTotal: { fontSize: 13, fontWeight: '700', color: '#0f172a', flex: 1, textAlign: 'right' },
  del: { padding: 4 },
  delText: { fontSize: 17 },

  totalRow: {
    flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center',
    paddingHorizontal: 20, paddingVertical: 16,
    borderTopWidth: 1, borderTopColor: '#e2e8f0', backgroundColor: '#fff',
  },
  totalLabel: { fontSize: 15, fontWeight: '600', color: '#475569' },
  totalAmt:   { fontSize: 22, fontWeight: '800', color: '#2563eb' },
})
