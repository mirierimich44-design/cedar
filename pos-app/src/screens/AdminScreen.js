import { useState, useEffect, useCallback } from 'react'
import {
  View, Text, ScrollView, TouchableOpacity, StyleSheet,
  ActivityIndicator, TextInput, RefreshControl, FlatList,
  Modal, Alert,
} from 'react-native'
import { SafeAreaView } from 'react-native-safe-area-context'
import api from '../api/client'

const fmt  = (n) => parseFloat(n || 0).toLocaleString('en-KE', { minimumFractionDigits: 2 })
const fmtN = (n) => parseFloat(n || 0).toLocaleString('en-KE', { minimumFractionDigits: 0 })

const TABS = [
  { key: 'dashboard',  label: '📊 Dashboard' },
  { key: 'stock',      label: '⚠️ Stock Alerts' },
  { key: 'staff',      label: '👥 Staff' },
  { key: 'sales',      label: '📈 Sales' },
  { key: 'expenses',   label: '💸 Expenses' },
  { key: 'transfer',   label: '🔄 Transfer' },
]

export default function AdminScreen({ navigation }) {
  const [tab, setTab] = useState('dashboard')

  return (
    <SafeAreaView style={styles.safe} edges={['top']}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()}>
          <Text style={styles.backText}>← Back</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Admin Panel</Text>
        <View style={{ width: 60 }} />
      </View>

      {/* Tab bar */}
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        style={styles.tabBar}
        contentContainerStyle={styles.tabBarContent}
      >
        {TABS.map(t => (
          <TouchableOpacity
            key={t.key}
            style={[styles.tabBtn, tab === t.key && styles.tabBtnActive]}
            onPress={() => setTab(t.key)}
          >
            <Text style={[styles.tabBtnText, tab === t.key && styles.tabBtnTextActive]}>
              {t.label}
            </Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      {/* Content */}
      {tab === 'dashboard'  && <DashboardTab />}
      {tab === 'stock'      && <StockAlertsTab />}
      {tab === 'staff'      && <StaffActivityTab />}
      {tab === 'sales'      && <SalesReportTab />}
      {tab === 'expenses'   && <ExpensesSummaryTab />}
      {tab === 'transfer'   && <StockTransferTab />}
    </SafeAreaView>
  )
}

// ─────────────────────────────────────────────────────────────────────────────
// DASHBOARD
// ─────────────────────────────────────────────────────────────────────────────
function DashboardTab() {
  const [data, setData]         = useState(null)
  const [loading, setLoading]   = useState(true)
  const [refreshing, setRefreshing] = useState(false)

  const load = useCallback(async () => {
    try {
      const res = await api.get('/api/mobile/admin/dashboard')
      setData(res.data)
    } catch { /* ignore */ } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }, [])

  useEffect(() => { load() }, [load])

  if (loading) return <Loader />

  const { today = {}, yesterday = {}, by_location = [], by_payment = [] } = data || {}

  return (
    <ScrollView
      style={styles.scroll}
      contentContainerStyle={styles.scrollContent}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load() }} colors={['#2563eb']} />}
    >
      {/* Today vs Yesterday */}
      <SectionTitle>Today vs Yesterday</SectionTitle>
      <View style={styles.row2}>
        <StatCard label="Today Sales" value={`Ksh ${fmt(today.total)}`} sub={`${fmtN(today.count)} transactions`} color="#2563eb" />
        <StatCard label="Yesterday" value={`Ksh ${fmt(yesterday.total)}`} sub={`${fmtN(yesterday.count)} transactions`} color="#64748b" />
      </View>

      {/* Per location */}
      <SectionTitle>By Branch — Today</SectionTitle>
      {by_location.length === 0
        ? <EmptyMsg>No sales today</EmptyMsg>
        : by_location.map(loc => (
          <View key={loc.id} style={styles.card}>
            <Text style={styles.cardTitle}>{loc.name}</Text>
            <View style={styles.cardRow}>
              <Text style={styles.cardLabel}>Sales</Text>
              <Text style={styles.cardValue}>Ksh {fmt(loc.total)}</Text>
            </View>
            <View style={styles.cardRow}>
              <Text style={styles.cardLabel}>Transactions</Text>
              <Text style={styles.cardValue}>{fmtN(loc.count)}</Text>
            </View>
          </View>
        ))
      }

      {/* Payment breakdown */}
      <SectionTitle>By Payment Method — Today</SectionTitle>
      {by_payment.length === 0
        ? <EmptyMsg>No payment data</EmptyMsg>
        : by_payment.map((p, i) => (
          <View key={i} style={styles.card}>
            <View style={styles.cardRow}>
              <Text style={styles.cardLabel}>{(p.method || 'Unknown').toUpperCase()}</Text>
              <Text style={styles.cardValue}>Ksh {fmt(p.total)}</Text>
            </View>
          </View>
        ))
      }
    </ScrollView>
  )
}

// ─────────────────────────────────────────────────────────────────────────────
// STOCK ALERTS
// ─────────────────────────────────────────────────────────────────────────────
function StockAlertsTab() {
  const [data, setData]         = useState([])
  const [loading, setLoading]   = useState(true)
  const [refreshing, setRefreshing] = useState(false)

  const load = useCallback(async () => {
    try {
      const res = await api.get('/api/mobile/admin/stock-alerts')
      setData(res.data?.alerts || [])
    } catch { /* ignore */ } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }, [])

  useEffect(() => { load() }, [load])

  if (loading) return <Loader />

  const zero = data.filter(d => parseFloat(d.qty_available) <= 0)
  const low  = data.filter(d => parseFloat(d.qty_available) > 0)

  return (
    <ScrollView
      style={styles.scroll}
      contentContainerStyle={styles.scrollContent}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load() }} colors={['#2563eb']} />}
    >
      {data.length === 0 && <EmptyMsg>All stock levels are healthy 🎉</EmptyMsg>}

      {zero.length > 0 && <>
        <SectionTitle>Out of Stock ({zero.length})</SectionTitle>
        {zero.map((item, i) => (
          <View key={i} style={[styles.card, styles.cardDanger]}>
            <Text style={styles.cardTitle} numberOfLines={2}>{item.product_name}{item.variation && item.variation !== 'DUMMY' ? ` · ${item.variation}` : ''}</Text>
            <View style={styles.cardRow}>
              <Text style={styles.cardLabel}>{item.location_name}</Text>
              <Text style={[styles.cardValue, { color: '#ef4444' }]}>Stock: {parseFloat(item.qty_available || 0).toFixed(0)}</Text>
            </View>
          </View>
        ))}
      </>}

      {low.length > 0 && <>
        <SectionTitle>Low Stock ({low.length})</SectionTitle>
        {low.map((item, i) => (
          <View key={i} style={[styles.card, styles.cardWarn]}>
            <Text style={styles.cardTitle} numberOfLines={2}>{item.product_name}{item.variation && item.variation !== 'DUMMY' ? ` · ${item.variation}` : ''}</Text>
            <View style={styles.cardRow}>
              <Text style={styles.cardLabel}>{item.location_name}</Text>
              <Text style={[styles.cardValue, { color: '#f59e0b' }]}>Stock: {parseFloat(item.qty_available || 0).toFixed(1)} / Alert: {parseFloat(item.alert_quantity || 0).toFixed(0)}</Text>
            </View>
          </View>
        ))}
      </>}
    </ScrollView>
  )
}

// ─────────────────────────────────────────────────────────────────────────────
// STAFF ACTIVITY
// ─────────────────────────────────────────────────────────────────────────────
function StaffActivityTab() {
  const [data, setData]         = useState(null)
  const [loading, setLoading]   = useState(true)
  const [refreshing, setRefreshing] = useState(false)

  const load = useCallback(async () => {
    try {
      const res = await api.get('/api/mobile/admin/staff-activity')
      setData(res.data)
    } catch { /* ignore */ } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }, [])

  useEffect(() => { load() }, [load])

  if (loading) return <Loader />

  const { staff = [], registers = [] } = data || {}

  return (
    <ScrollView
      style={styles.scroll}
      contentContainerStyle={styles.scrollContent}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load() }} colors={['#2563eb']} />}
    >
      <SectionTitle>Staff Sales — Today</SectionTitle>
      {staff.length === 0
        ? <EmptyMsg>No staff sales today</EmptyMsg>
        : staff.map((s, i) => (
          <View key={i} style={styles.card}>
            <Text style={styles.cardTitle}>{s.name}</Text>
            <View style={styles.cardRow}>
              <Text style={styles.cardLabel}>Sales Amount</Text>
              <Text style={styles.cardValue}>Ksh {fmt(s.total)}</Text>
            </View>
            <View style={styles.cardRow}>
              <Text style={styles.cardLabel}>Transactions</Text>
              <Text style={styles.cardValue}>{fmtN(s.count)}</Text>
            </View>
          </View>
        ))
      }

      <SectionTitle>Register Status</SectionTitle>
      {registers.length === 0
        ? <EmptyMsg>No registers found</EmptyMsg>
        : registers.map((r, i) => (
          <View key={i} style={[styles.card, r.status === 'open' ? styles.cardSuccess : null]}>
            <View style={styles.cardRow}>
              <View>
                <Text style={styles.cardTitle}>{r.name}</Text>
                {r.location && <Text style={styles.cardLabel}>{r.location}</Text>}
              </View>
              <View style={[styles.badge, { backgroundColor: r.status === 'open' ? '#16a34a' : '#94a3b8' }]}>
                <Text style={styles.badgeText}>{r.status === 'open' ? 'OPEN' : 'CLOSED'}</Text>
              </View>
            </View>
            {r.opened_by && (
              <Text style={[styles.cardLabel, { marginTop: 4 }]}>Opened by: {r.opened_by}</Text>
            )}
          </View>
        ))
      }
    </ScrollView>
  )
}

// ─────────────────────────────────────────────────────────────────────────────
// SALES REPORT
// ─────────────────────────────────────────────────────────────────────────────
function SalesReportTab() {
  const [data, setData]         = useState(null)
  const [loading, setLoading]   = useState(true)
  const [refreshing, setRefreshing] = useState(false)
  const [period, setPeriod]     = useState('today')

  const load = useCallback(async (p) => {
    setLoading(true)
    try {
      const res = await api.get('/api/mobile/admin/sales-report', { params: { period: p || period } })
      setData(res.data)
    } catch { /* ignore */ } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }, [period])

  useEffect(() => { load(period) }, [period])

  const changePeriod = (p) => { setPeriod(p); load(p) }

  if (loading) return <Loader />

  const { top_products = [], hourly = [], slow_movers = [] } = data || {}

  return (
    <ScrollView
      style={styles.scroll}
      contentContainerStyle={styles.scrollContent}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(period) }} colors={['#2563eb']} />}
    >
      {/* Period selector */}
      <View style={styles.segmentRow}>
        {[['today','Today'],['week','This Week'],['month','This Month']].map(([k,l]) => (
          <TouchableOpacity key={k} style={[styles.segBtn, period===k && styles.segBtnActive]} onPress={() => changePeriod(k)}>
            <Text style={[styles.segBtnText, period===k && styles.segBtnTextActive]}>{l}</Text>
          </TouchableOpacity>
        ))}
      </View>

      <SectionTitle>Top 10 Products</SectionTitle>
      {top_products.length === 0
        ? <EmptyMsg>No sales data for this period</EmptyMsg>
        : top_products.map((p, i) => (
          <View key={i} style={styles.card}>
            <View style={styles.cardRow}>
              <View style={styles.rankBadge}><Text style={styles.rankText}>#{i+1}</Text></View>
              <Text style={[styles.cardTitle, { flex: 1, marginBottom: 0 }]} numberOfLines={1}>
                {p.name}{p.variation && p.variation !== 'DUMMY' ? ` · ${p.variation}` : ''}
              </Text>
            </View>
            <View style={styles.cardRow}>
              <Text style={styles.cardLabel}>Revenue</Text>
              <Text style={styles.cardValue}>Ksh {fmt(p.revenue)}</Text>
            </View>
            <View style={styles.cardRow}>
              <Text style={styles.cardLabel}>Qty Sold</Text>
              <Text style={styles.cardValue}>{fmtN(p.qty_sold)}</Text>
            </View>
          </View>
        ))
      }

      <SectionTitle>Hourly Sales</SectionTitle>
      {hourly.length === 0
        ? <EmptyMsg>No hourly data</EmptyMsg>
        : (
          <View style={styles.card}>
            {hourly.map((h, i) => {
              const maxTotal = Math.max(...hourly.map(x => parseFloat(x.total || 0)), 1)
              const pct = (parseFloat(h.total || 0) / maxTotal) * 100
              return (
                <View key={i} style={styles.barRow}>
                  <Text style={styles.barLabel}>{String(h.hour).padStart(2,'0')}:00</Text>
                  <View style={styles.barTrack}>
                    <View style={[styles.barFill, { width: `${pct}%` }]} />
                  </View>
                  <Text style={styles.barValue}>Ksh {fmt(h.total)}</Text>
                </View>
              )
            })}
          </View>
        )
      }

      <SectionTitle>Slow Movers (No sales in 7 days)</SectionTitle>
      {slow_movers.length === 0
        ? <EmptyMsg>All products are selling well 🎉</EmptyMsg>
        : slow_movers.slice(0, 20).map((p, i) => (
          <View key={i} style={[styles.card, styles.cardWarn]}>
            <Text style={styles.cardTitle} numberOfLines={2}>
              {p.name}{p.variation && p.variation !== 'DUMMY' ? ` · ${p.variation}` : ''}
            </Text>
            <Text style={styles.cardLabel}>Stock: {parseFloat(p.qty_available || 0).toFixed(0)}</Text>
          </View>
        ))
      }
    </ScrollView>
  )
}

// ─────────────────────────────────────────────────────────────────────────────
// EXPENSES SUMMARY
// ─────────────────────────────────────────────────────────────────────────────
function ExpensesSummaryTab() {
  const [data, setData]         = useState(null)
  const [loading, setLoading]   = useState(true)
  const [refreshing, setRefreshing] = useState(false)
  const [period, setPeriod]     = useState('today')

  const load = useCallback(async (p) => {
    setLoading(true)
    try {
      const res = await api.get('/api/mobile/admin/expenses-summary', { params: { period: p || period } })
      setData(res.data)
    } catch { /* ignore */ } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }, [period])

  useEffect(() => { load(period) }, [period])

  const changePeriod = (p) => { setPeriod(p); load(p) }

  if (loading) return <Loader />

  const { by_location = [], by_category = [], total = 0 } = data || {}

  return (
    <ScrollView
      style={styles.scroll}
      contentContainerStyle={styles.scrollContent}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); load(period) }} colors={['#2563eb']} />}
    >
      {/* Period selector */}
      <View style={styles.segmentRow}>
        {[['today','Today'],['week','This Week'],['month','This Month']].map(([k,l]) => (
          <TouchableOpacity key={k} style={[styles.segBtn, period===k && styles.segBtnActive]} onPress={() => changePeriod(k)}>
            <Text style={[styles.segBtnText, period===k && styles.segBtnTextActive]}>{l}</Text>
          </TouchableOpacity>
        ))}
      </View>

      {/* Total */}
      <View style={[styles.card, { backgroundColor: '#fef2f2', borderLeftWidth: 4, borderLeftColor: '#ef4444' }]}>
        <Text style={styles.cardLabel}>Total Expenses</Text>
        <Text style={[styles.cardTitle, { fontSize: 22, color: '#ef4444' }]}>Ksh {fmt(total)}</Text>
      </View>

      <SectionTitle>By Branch</SectionTitle>
      {by_location.length === 0
        ? <EmptyMsg>No expenses for this period</EmptyMsg>
        : by_location.map((loc, i) => (
          <View key={i} style={styles.card}>
            <View style={styles.cardRow}>
              <Text style={styles.cardTitle}>{loc.name}</Text>
              <Text style={[styles.cardValue, { color: '#ef4444' }]}>Ksh {fmt(loc.total)}</Text>
            </View>
            <Text style={styles.cardLabel}>{fmtN(loc.count)} expense{loc.count !== 1 ? 's' : ''}</Text>
          </View>
        ))
      }

      <SectionTitle>By Category</SectionTitle>
      {by_category.length === 0
        ? <EmptyMsg>No category data</EmptyMsg>
        : by_category.map((cat, i) => (
          <View key={i} style={styles.card}>
            <View style={styles.cardRow}>
              <Text style={styles.cardLabel}>{cat.category || 'Uncategorized'}</Text>
              <Text style={[styles.cardValue, { color: '#ef4444' }]}>Ksh {fmt(cat.total)}</Text>
            </View>
          </View>
        ))
      }
    </ScrollView>
  )
}

// ─────────────────────────────────────────────────────────────────────────────
// STOCK TRANSFER
// ─────────────────────────────────────────────────────────────────────────────
function StockTransferTab() {
  const [locations, setLocations]   = useState([])
  const [products, setProducts]     = useState([])
  const [fromLoc, setFromLoc]       = useState(null)
  const [toLoc, setToLoc]           = useState(null)
  const [search, setSearch]         = useState('')
  const [selectedProduct, setSelectedProduct] = useState(null)
  const [qty, setQty]               = useState('')
  const [submitting, setSubmitting] = useState(false)
  const [loadingInit, setLoadingInit] = useState(true)

  const [locPickerVisible, setLocPickerVisible] = useState(false)
  const [locPickerFor, setLocPickerFor]         = useState(null) // 'from' | 'to'
  const [prodPickerVisible, setProdPickerVisible] = useState(false)

  useEffect(() => {
    ;(async () => {
      try {
        const [detRes, prodRes] = await Promise.all([
          api.get('/api/mobile/pos-details'),
          api.get('/api/mobile/products', { params: { term: '' } }),
        ])
        setLocations(detRes.data?.locations || [])
        setProducts(prodRes.data?.products || [])
      } catch { /* ignore */ } finally {
        setLoadingInit(false)
      }
    })()
  }, [])

  const filtered = search.trim().length > 0
    ? products.filter(p => p.name.toLowerCase().includes(search.toLowerCase()))
    : products

  async function submit() {
    if (!fromLoc || !toLoc) { Alert.alert('Error', 'Select source and destination branch'); return }
    if (fromLoc.id === toLoc.id) { Alert.alert('Error', 'Source and destination must be different'); return }
    if (!selectedProduct) { Alert.alert('Error', 'Select a product'); return }
    const qtyNum = parseFloat(qty)
    if (!qty || isNaN(qtyNum) || qtyNum <= 0) { Alert.alert('Error', 'Enter a valid quantity'); return }

    setSubmitting(true)
    try {
      const res = await api.post('/api/mobile/admin/stock-transfer', {
        from_location_id: fromLoc.id,
        to_location_id:   toLoc.id,
        variation_id:     selectedProduct.variation_id,
        product_id:       selectedProduct.product_id,
        quantity:         qtyNum,
      })
      Alert.alert('Success', res.data?.message || 'Stock transferred successfully')
      setSelectedProduct(null)
      setQty('')
    } catch (err) {
      const msg = err.response?.data?.error || err.response?.data?.message || 'Transfer failed'
      Alert.alert('Error', msg)
    } finally {
      setSubmitting(false)
    }
  }

  if (loadingInit) return <Loader />

  return (
    <ScrollView style={styles.scroll} contentContainerStyle={styles.scrollContent}>
      <SectionTitle>Stock Transfer</SectionTitle>

      {/* From location */}
      <Text style={styles.fieldLabel}>From Branch</Text>
      <TouchableOpacity style={styles.selectBtn} onPress={() => { setLocPickerFor('from'); setLocPickerVisible(true) }}>
        <Text style={fromLoc ? styles.selectBtnText : styles.selectBtnPlaceholder}>
          {fromLoc ? fromLoc.name : 'Select source branch…'}
        </Text>
        <Text style={styles.selectArrow}>▾</Text>
      </TouchableOpacity>

      {/* To location */}
      <Text style={styles.fieldLabel}>To Branch</Text>
      <TouchableOpacity style={styles.selectBtn} onPress={() => { setLocPickerFor('to'); setLocPickerVisible(true) }}>
        <Text style={toLoc ? styles.selectBtnText : styles.selectBtnPlaceholder}>
          {toLoc ? toLoc.name : 'Select destination branch…'}
        </Text>
        <Text style={styles.selectArrow}>▾</Text>
      </TouchableOpacity>

      {/* Product */}
      <Text style={styles.fieldLabel}>Product</Text>
      <TouchableOpacity style={styles.selectBtn} onPress={() => setProdPickerVisible(true)}>
        <Text style={selectedProduct ? styles.selectBtnText : styles.selectBtnPlaceholder} numberOfLines={1}>
          {selectedProduct
            ? `${selectedProduct.name}${selectedProduct.variation && selectedProduct.variation !== 'DUMMY' ? ` · ${selectedProduct.variation}` : ''}`
            : 'Select product…'}
        </Text>
        <Text style={styles.selectArrow}>▾</Text>
      </TouchableOpacity>
      {selectedProduct && fromLoc && (
        <Text style={styles.stockHint}>
          Available at {fromLoc.name}: {parseFloat(selectedProduct.qty_available || 0).toFixed(0)} {selectedProduct.unit || ''}
        </Text>
      )}

      {/* Quantity */}
      <Text style={styles.fieldLabel}>Quantity</Text>
      <TextInput
        style={styles.input}
        value={qty}
        onChangeText={setQty}
        keyboardType="decimal-pad"
        placeholder="Enter quantity to transfer"
        placeholderTextColor="#94a3b8"
      />

      <TouchableOpacity
        style={[styles.submitBtn, submitting && { opacity: 0.6 }]}
        onPress={submit}
        disabled={submitting}
        activeOpacity={0.85}
      >
        {submitting
          ? <ActivityIndicator color="#fff" />
          : <Text style={styles.submitBtnText}>Transfer Stock</Text>
        }
      </TouchableOpacity>

      {/* Location picker modal */}
      <Modal visible={locPickerVisible} transparent animationType="slide" onRequestClose={() => setLocPickerVisible(false)}>
        <View style={styles.modalOverlay}>
          <View style={styles.modalSheet}>
            <Text style={styles.modalTitle}>{locPickerFor === 'from' ? 'Source Branch' : 'Destination Branch'}</Text>
            <ScrollView>
              {locations.map(loc => (
                <TouchableOpacity
                  key={loc.id}
                  style={styles.modalItem}
                  onPress={() => {
                    if (locPickerFor === 'from') setFromLoc(loc)
                    else setToLoc(loc)
                    setLocPickerVisible(false)
                  }}
                >
                  <Text style={styles.modalItemText}>{loc.name}</Text>
                </TouchableOpacity>
              ))}
            </ScrollView>
            <TouchableOpacity style={styles.modalCancel} onPress={() => setLocPickerVisible(false)}>
              <Text style={styles.modalCancelText}>Cancel</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>

      {/* Product picker modal */}
      <Modal visible={prodPickerVisible} transparent animationType="slide" onRequestClose={() => setProdPickerVisible(false)}>
        <View style={styles.modalOverlay}>
          <View style={styles.modalSheet}>
            <Text style={styles.modalTitle}>Select Product</Text>
            <View style={styles.modalSearch}>
              <TextInput
                style={styles.modalSearchInput}
                value={search}
                onChangeText={setSearch}
                placeholder="Search products…"
                placeholderTextColor="#94a3b8"
                autoFocus
              />
            </View>
            <ScrollView style={{ maxHeight: 320 }} keyboardShouldPersistTaps="handled">
              {filtered.slice(0, 60).map(p => (
                <TouchableOpacity
                  key={String(p.variation_id)}
                  style={styles.modalItem}
                  onPress={() => { setSelectedProduct(p); setProdPickerVisible(false); setSearch('') }}
                >
                  <Text style={styles.modalItemText} numberOfLines={1}>
                    {p.name}{p.variation && p.variation !== 'DUMMY' ? ` · ${p.variation}` : ''}
                  </Text>
                  {p.enable_stock == 1 && (
                    <Text style={styles.modalItemSub}>Stock: {parseFloat(p.qty_available || 0).toFixed(0)}</Text>
                  )}
                </TouchableOpacity>
              ))}
            </ScrollView>
            <TouchableOpacity style={styles.modalCancel} onPress={() => { setProdPickerVisible(false); setSearch('') }}>
              <Text style={styles.modalCancelText}>Cancel</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </ScrollView>
  )
}

// ─────────────────────────────────────────────────────────────────────────────
// Shared helpers
// ─────────────────────────────────────────────────────────────────────────────
function Loader() {
  return (
    <View style={styles.loaderWrap}>
      <ActivityIndicator size="large" color="#2563eb" />
    </View>
  )
}

function SectionTitle({ children }) {
  return <Text style={styles.sectionTitle}>{children}</Text>
}

function EmptyMsg({ children }) {
  return <Text style={styles.emptyMsg}>{children}</Text>
}

function StatCard({ label, value, sub, color = '#2563eb' }) {
  return (
    <View style={[styles.statCard, { borderTopColor: color }]}>
      <Text style={styles.statLabel}>{label}</Text>
      <Text style={[styles.statValue, { color }]}>{value}</Text>
      {sub ? <Text style={styles.statSub}>{sub}</Text> : null}
    </View>
  )
}

// ─────────────────────────────────────────────────────────────────────────────
const styles = StyleSheet.create({
  safe:       { flex: 1, backgroundColor: '#f1f5f9' },
  scroll:     { flex: 1 },
  scrollContent: { padding: 12, paddingBottom: 40 },
  loaderWrap: { flex: 1, alignItems: 'center', justifyContent: 'center' },

  header: {
    backgroundColor: '#1e293b', flexDirection: 'row', alignItems: 'center',
    justifyContent: 'space-between', paddingHorizontal: 14, paddingVertical: 12,
  },
  backBtn:     { paddingVertical: 4, paddingRight: 12 },
  backText:    { color: '#93c5fd', fontSize: 14, fontWeight: '600' },
  headerTitle: { color: '#fff', fontWeight: '800', fontSize: 16 },

  tabBar:        { backgroundColor: '#fff', maxHeight: 50, flexGrow: 0, borderBottomWidth: 1, borderBottomColor: '#e2e8f0' },
  tabBarContent: { paddingHorizontal: 8, alignItems: 'center', gap: 4 },
  tabBtn:        { paddingHorizontal: 14, paddingVertical: 12, borderRadius: 0, borderBottomWidth: 2, borderBottomColor: 'transparent' },
  tabBtnActive:  { borderBottomColor: '#2563eb' },
  tabBtnText:    { fontSize: 13, color: '#64748b', fontWeight: '500' },
  tabBtnTextActive: { color: '#2563eb', fontWeight: '700' },

  sectionTitle: { fontSize: 13, fontWeight: '700', color: '#64748b', textTransform: 'uppercase', letterSpacing: 0.5, marginTop: 16, marginBottom: 8 },
  emptyMsg:     { color: '#94a3b8', textAlign: 'center', paddingVertical: 24, fontSize: 14 },

  row2: { flexDirection: 'row', gap: 8 },
  statCard: {
    flex: 1, backgroundColor: '#fff', borderRadius: 12, padding: 14,
    borderTopWidth: 3, marginBottom: 8,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.06, shadowRadius: 4, elevation: 2,
  },
  statLabel: { fontSize: 11, color: '#64748b', fontWeight: '600', textTransform: 'uppercase', marginBottom: 4 },
  statValue: { fontSize: 18, fontWeight: '800' },
  statSub:   { fontSize: 11, color: '#94a3b8', marginTop: 2 },

  card: {
    backgroundColor: '#fff', borderRadius: 12, padding: 14, marginBottom: 8,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.06, shadowRadius: 4, elevation: 2,
  },
  cardDanger:  { borderLeftWidth: 4, borderLeftColor: '#ef4444' },
  cardWarn:    { borderLeftWidth: 4, borderLeftColor: '#f59e0b' },
  cardSuccess: { borderLeftWidth: 4, borderLeftColor: '#16a34a' },
  cardTitle:   { fontSize: 14, fontWeight: '700', color: '#1e293b', marginBottom: 6 },
  cardRow:     { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 4 },
  cardLabel:   { fontSize: 12, color: '#64748b' },
  cardValue:   { fontSize: 13, fontWeight: '700', color: '#1e293b' },

  badge:       { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 20 },
  badgeText:   { color: '#fff', fontSize: 11, fontWeight: '700' },

  rankBadge:   { backgroundColor: '#eff6ff', borderRadius: 8, width: 30, height: 30, alignItems: 'center', justifyContent: 'center', marginRight: 10 },
  rankText:    { color: '#2563eb', fontWeight: '800', fontSize: 12 },

  segmentRow:  { flexDirection: 'row', gap: 8, marginBottom: 4 },
  segBtn:      { flex: 1, paddingVertical: 9, borderRadius: 10, backgroundColor: '#e2e8f0', alignItems: 'center' },
  segBtnActive:{ backgroundColor: '#2563eb' },
  segBtnText:  { fontSize: 12, fontWeight: '600', color: '#475569' },
  segBtnTextActive: { color: '#fff' },

  barRow:      { flexDirection: 'row', alignItems: 'center', marginVertical: 3 },
  barLabel:    { fontSize: 11, color: '#64748b', width: 44 },
  barTrack:    { flex: 1, height: 10, backgroundColor: '#f1f5f9', borderRadius: 5, marginHorizontal: 8, overflow: 'hidden' },
  barFill:     { height: '100%', backgroundColor: '#2563eb', borderRadius: 5 },
  barValue:    { fontSize: 11, color: '#64748b', width: 80, textAlign: 'right' },

  fieldLabel:  { fontSize: 13, fontWeight: '600', color: '#374151', marginBottom: 6, marginTop: 12 },
  selectBtn:   {
    backgroundColor: '#fff', borderRadius: 12, borderWidth: 1, borderColor: '#e2e8f0',
    paddingHorizontal: 14, paddingVertical: 14, flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center',
  },
  selectBtnText:        { fontSize: 14, color: '#1e293b', flex: 1 },
  selectBtnPlaceholder: { fontSize: 14, color: '#94a3b8', flex: 1 },
  selectArrow:          { fontSize: 16, color: '#94a3b8' },
  stockHint:            { fontSize: 12, color: '#16a34a', marginTop: 4, marginLeft: 4 },

  input: {
    backgroundColor: '#fff', borderRadius: 12, borderWidth: 1, borderColor: '#e2e8f0',
    paddingHorizontal: 14, paddingVertical: 14, fontSize: 14, color: '#1e293b',
  },
  submitBtn: {
    backgroundColor: '#2563eb', borderRadius: 14, paddingVertical: 15,
    alignItems: 'center', marginTop: 24,
    shadowColor: '#2563eb', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.3, shadowRadius: 8, elevation: 6,
  },
  submitBtnText: { color: '#fff', fontWeight: '800', fontSize: 15 },

  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
  modalSheet:   { backgroundColor: '#fff', borderTopLeftRadius: 20, borderTopRightRadius: 20, paddingTop: 12, paddingBottom: 30, maxHeight: '75%' },
  modalTitle:   { fontSize: 15, fontWeight: '700', color: '#1e293b', textAlign: 'center', paddingBottom: 12, borderBottomWidth: 1, borderBottomColor: '#f1f5f9' },
  modalSearch:  { padding: 12 },
  modalSearchInput: {
    backgroundColor: '#f1f5f9', borderRadius: 12, paddingHorizontal: 14, paddingVertical: 10,
    fontSize: 14, color: '#1e293b',
  },
  modalItem:    { paddingHorizontal: 20, paddingVertical: 14, borderBottomWidth: 1, borderBottomColor: '#f8fafc' },
  modalItemText:{ fontSize: 14, fontWeight: '600', color: '#1e293b' },
  modalItemSub: { fontSize: 11, color: '#64748b', marginTop: 2 },
  modalCancel:  { marginTop: 8, marginHorizontal: 20, paddingVertical: 14, backgroundColor: '#f1f5f9', borderRadius: 12, alignItems: 'center' },
  modalCancelText: { color: '#64748b', fontWeight: '700', fontSize: 14 },
})
