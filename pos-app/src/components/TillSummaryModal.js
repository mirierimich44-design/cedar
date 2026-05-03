import { useState, useEffect, useCallback } from 'react'
import {
  View, Text, Modal, TouchableOpacity, StyleSheet,
  ScrollView, ActivityIndicator, TouchableWithoutFeedback, Alert,
} from 'react-native'
import api from '../api/client'

const fmt = (n) => parseFloat(n || 0).toLocaleString('en-KE', { minimumFractionDigits: 2 })

const METHOD_ICONS = {
  cash:  '💵',
  card:  '💳',
  mpesa: '📱',
}

export default function TillSummaryModal({ open, onClose, locationId, onLogout }) {
  const [loading,  setLoading]  = useState(false)
  const [closing,  setClosing]  = useState(false)
  const [data,     setData]     = useState(null)
  const [error,    setError]    = useState('')

  const fetchSummary = useCallback(async () => {
    if (!open) return
    setLoading(true); setError('')
    try {
      const res = await api.get('/api/mobile/till-summary', {
        params: { location_id: locationId },
      })
      setData(res.data)
    } catch (e) {
      setError(e.response?.data?.message || 'Could not load summary.')
    } finally {
      setLoading(false)
    }
  }, [open, locationId])

  useEffect(() => { fetchSummary() }, [fetchSummary])

  async function handleCloseTill() {
    Alert.alert(
      'Close Till',
      'Are you sure you want to close the till for today?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Close Till', style: 'destructive',
          onPress: async () => {
            setClosing(true)
            try {
              await api.post('/api/mobile/close-till')
              await fetchSummary()
              Alert.alert('Done', 'Till closed successfully.')
            } catch (e) {
              Alert.alert('Error', e.response?.data?.message || 'Could not close till.')
            } finally {
              setClosing(false)
            }
          },
        },
      ]
    )
  }

  return (
    <Modal visible={open} transparent animationType="slide" onRequestClose={onClose}>
      <TouchableWithoutFeedback onPress={onClose}>
        <View style={styles.backdrop} />
      </TouchableWithoutFeedback>

      <View style={styles.sheet}>
        <View style={styles.handle} />

        <View style={styles.titleRow}>
          <Text style={styles.title}>Till Summary</Text>
          <TouchableOpacity onPress={onClose} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}>
            <Text style={styles.closeX}>✕</Text>
          </TouchableOpacity>
        </View>

        {loading ? (
          <View style={styles.center}>
            <ActivityIndicator size="large" color="#2563eb" />
          </View>
        ) : error ? (
          <View style={styles.center}>
            <Text style={styles.errorText}>{error}</Text>
            <TouchableOpacity style={styles.retryBtn} onPress={fetchSummary}>
              <Text style={styles.retryText}>Retry</Text>
            </TouchableOpacity>
          </View>
        ) : data ? (
          <ScrollView showsVerticalScrollIndicator={false}>

            {/* Date */}
            <Text style={styles.dateLabel}>📅 {data.date}</Text>

            {/* Totals */}
            <View style={styles.totalsRow}>
              <View style={styles.totalBox}>
                <Text style={styles.totalNum}>{data.sales_count}</Text>
                <Text style={styles.totalSub}>Sales</Text>
              </View>
              <View style={[styles.totalBox, styles.totalBoxBlue]}>
                <Text style={[styles.totalNum, styles.totalNumBlue]}>Ksh {fmt(data.sales_total)}</Text>
                <Text style={[styles.totalSub, { color: '#3b82f6' }]}>Total Revenue</Text>
              </View>
            </View>

            {/* Payment breakdown */}
            <Text style={styles.sectionLabel}>PAYMENT METHODS</Text>
            {data.payment_breakdown?.length ? (
              data.payment_breakdown.map((pm) => (
                <View key={pm.method} style={styles.pmRow}>
                  <Text style={styles.pmIcon}>{METHOD_ICONS[pm.method] || '💰'}</Text>
                  <Text style={styles.pmName}>{pm.method?.toUpperCase()}</Text>
                  <Text style={styles.pmAmount}>Ksh {fmt(pm.total)}</Text>
                </View>
              ))
            ) : (
              <Text style={styles.noPayments}>No payments recorded today</Text>
            )}

            {/* Till status */}
            <View style={styles.tillStatus}>
              <View style={[styles.statusDot, { backgroundColor: data.register_open ? '#16a34a' : '#94a3b8' }]} />
              <Text style={styles.statusText}>
                Till is {data.register_open ? 'open' : 'closed'}
              </Text>
            </View>

            {/* Actions */}
            <View style={styles.actions}>
              {data.register_open && (
                <TouchableOpacity
                  style={[styles.actionBtn, styles.closeTillBtn, closing && { opacity: 0.6 }]}
                  onPress={handleCloseTill}
                  disabled={closing}
                >
                  {closing
                    ? <ActivityIndicator color="#fff" />
                    : <Text style={styles.actionBtnText}>🔒 Close Till</Text>
                  }
                </TouchableOpacity>
              )}

              <TouchableOpacity style={[styles.actionBtn, styles.logoutBtn]} onPress={onLogout}>
                <Text style={styles.actionBtnText}>Logout</Text>
              </TouchableOpacity>
            </View>

          </ScrollView>
        ) : null}
      </View>
    </Modal>
  )
}

const styles = StyleSheet.create({
  backdrop: { flex: 1, backgroundColor: 'rgba(0,0,0,0.55)' },
  sheet: {
    backgroundColor: '#fff', borderTopLeftRadius: 28, borderTopRightRadius: 28,
    padding: 22, paddingBottom: 40, maxHeight: '85%',
    shadowColor: '#000', shadowOffset: { width: 0, height: -4 }, shadowOpacity: 0.12, shadowRadius: 16, elevation: 20,
  },
  handle:   { width: 40, height: 4, backgroundColor: '#e2e8f0', borderRadius: 2, alignSelf: 'center', marginBottom: 16 },
  titleRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 },
  title:    { fontSize: 20, fontWeight: '800', color: '#0f172a' },
  closeX:   { fontSize: 18, color: '#94a3b8', fontWeight: '600' },

  center:    { paddingVertical: 40, alignItems: 'center' },
  errorText: { color: '#dc2626', fontSize: 14, textAlign: 'center', marginBottom: 12 },
  retryBtn:  { backgroundColor: '#2563eb', borderRadius: 12, paddingHorizontal: 20, paddingVertical: 10 },
  retryText: { color: '#fff', fontWeight: '600' },

  dateLabel: { fontSize: 13, color: '#64748b', marginBottom: 14 },

  totalsRow:     { flexDirection: 'row', gap: 10, marginBottom: 22 },
  totalBox:      { flex: 1, backgroundColor: '#f8fafc', borderRadius: 16, padding: 16, alignItems: 'center' },
  totalBoxBlue:  { backgroundColor: '#eff6ff' },
  totalNum:      { fontSize: 22, fontWeight: '800', color: '#1e293b', marginBottom: 2 },
  totalNumBlue:  { fontSize: 18, color: '#2563eb' },
  totalSub:      { fontSize: 12, color: '#64748b', fontWeight: '500' },

  sectionLabel: { fontSize: 11, fontWeight: '700', color: '#64748b', letterSpacing: 1, marginBottom: 10 },

  pmRow: {
    flexDirection: 'row', alignItems: 'center',
    backgroundColor: '#f8fafc', borderRadius: 14, paddingHorizontal: 16, paddingVertical: 13,
    marginBottom: 8,
  },
  pmIcon:   { fontSize: 20, marginRight: 10 },
  pmName:   { flex: 1, fontSize: 14, fontWeight: '600', color: '#1e293b' },
  pmAmount: { fontSize: 15, fontWeight: '800', color: '#16a34a' },

  noPayments: { color: '#94a3b8', fontSize: 14, textAlign: 'center', paddingVertical: 16 },

  tillStatus: { flexDirection: 'row', alignItems: 'center', marginTop: 18, marginBottom: 6 },
  statusDot:  { width: 10, height: 10, borderRadius: 5, marginRight: 8 },
  statusText: { fontSize: 13, color: '#64748b', fontWeight: '500' },

  actions:     { marginTop: 18, gap: 10 },
  actionBtn:   { borderRadius: 16, paddingVertical: 15, alignItems: 'center' },
  closeTillBtn: { backgroundColor: '#dc2626' },
  logoutBtn:   { backgroundColor: '#475569' },
  actionBtnText: { color: '#fff', fontWeight: '700', fontSize: 15 },
})
