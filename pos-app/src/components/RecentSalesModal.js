import { useState, useEffect, useCallback } from 'react'
import {
  View, Text, Modal, TouchableOpacity, StyleSheet,
  FlatList, ActivityIndicator, TouchableWithoutFeedback,
} from 'react-native'
import api from '../api/client'

const fmt = (n) => parseFloat(n || 0).toLocaleString('en-KE', { minimumFractionDigits: 2 })
const METHOD_ICONS = { cash: '💵', card: '💳', mpesa: '📱' }

function timeAgo(dateStr) {
  const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000)
  if (diff < 60)  return `${diff}s ago`
  if (diff < 3600) return `${Math.floor(diff / 60)}m ago`
  if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`
  return new Date(dateStr).toLocaleDateString('en-KE', { day: 'numeric', month: 'short' })
}

export default function RecentSalesModal({ open, onClose, locationId }) {
  const [sales,   setSales]   = useState([])
  const [loading, setLoading] = useState(false)
  const [error,   setError]   = useState('')

  const fetchSales = useCallback(async () => {
    if (!open) return
    setLoading(true); setError('')
    try {
      const res = await api.get('/api/mobile/recent-sales', {
        params: { location_id: locationId, limit: 20 },
      })
      setSales(res.data?.sales || [])
    } catch {
      setError('Could not load recent sales.')
    } finally {
      setLoading(false)
    }
  }, [open, locationId])

  useEffect(() => { fetchSales() }, [fetchSales])

  const renderSale = ({ item }) => (
    <View style={styles.saleRow}>
      <View style={styles.saleLeft}>
        <Text style={styles.saleInvoice}>{item.invoice_no}</Text>
        <Text style={styles.saleTime}>{timeAgo(item.created_at)}</Text>
      </View>
      <View style={styles.saleMethods}>
        {item.payments?.map(p => (
          <View key={p.method} style={styles.methodTag}>
            <Text style={styles.methodTagText}>{METHOD_ICONS[p.method] || '💰'} {p.method}</Text>
          </View>
        ))}
      </View>
      <Text style={styles.saleTotal}>Ksh {fmt(item.final_total)}</Text>
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
          <Text style={styles.title}>Recent Sales</Text>
          <TouchableOpacity onPress={onClose} style={styles.closeBtn} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}>
            <Text style={styles.closeText}>✕</Text>
          </TouchableOpacity>
        </View>

        {loading ? (
          <View style={styles.center}><ActivityIndicator size="large" color="#2563eb" /></View>
        ) : error ? (
          <View style={styles.center}>
            <Text style={styles.errorText}>{error}</Text>
            <TouchableOpacity style={styles.retryBtn} onPress={fetchSales}>
              <Text style={styles.retryText}>Retry</Text>
            </TouchableOpacity>
          </View>
        ) : sales.length === 0 ? (
          <View style={styles.center}>
            <Text style={styles.emptyText}>No sales today</Text>
          </View>
        ) : (
          <FlatList
            data={sales}
            keyExtractor={s => String(s.id)}
            renderItem={renderSale}
            showsVerticalScrollIndicator={false}
            contentContainerStyle={{ paddingBottom: 20 }}
          />
        )}
      </View>
    </Modal>
  )
}

const styles = StyleSheet.create({
  backdrop: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)' },
  sheet: {
    backgroundColor: '#fff', borderTopLeftRadius: 24, borderTopRightRadius: 24,
    maxHeight: '80%', paddingHorizontal: 16, paddingBottom: 24,
    shadowColor: '#000', shadowOffset: { width: 0, height: -4 }, shadowOpacity: 0.1, shadowRadius: 16, elevation: 20,
  },
  handle: { width: 40, height: 4, backgroundColor: '#e2e8f0', borderRadius: 2, alignSelf: 'center', marginVertical: 10 },
  header: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 14 },
  title:  { fontSize: 18, fontWeight: '800', color: '#0f172a' },
  closeBtn: { padding: 4 },
  closeText: { fontSize: 18, color: '#94a3b8', fontWeight: '600' },

  center:    { paddingVertical: 40, alignItems: 'center' },
  errorText: { color: '#dc2626', fontSize: 14, marginBottom: 12 },
  retryBtn:  { backgroundColor: '#2563eb', borderRadius: 12, paddingHorizontal: 20, paddingVertical: 10 },
  retryText: { color: '#fff', fontWeight: '600' },
  emptyText: { color: '#94a3b8', fontSize: 15 },

  saleRow: {
    flexDirection: 'row', alignItems: 'center', paddingVertical: 12,
    borderBottomWidth: 1, borderBottomColor: '#f1f5f9',
  },
  saleLeft:    { flex: 1 },
  saleInvoice: { fontSize: 13, fontWeight: '700', color: '#1e293b' },
  saleTime:    { fontSize: 11, color: '#94a3b8', marginTop: 2 },
  saleMethods: { flexDirection: 'row', gap: 4, marginHorizontal: 8 },
  methodTag:   { backgroundColor: '#f1f5f9', borderRadius: 8, paddingHorizontal: 6, paddingVertical: 3 },
  methodTagText:{ fontSize: 11, color: '#475569', fontWeight: '600' },
  saleTotal:   { fontSize: 14, fontWeight: '800', color: '#16a34a', minWidth: 80, textAlign: 'right' },
})
