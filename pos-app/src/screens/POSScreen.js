import { useState, useEffect, useCallback } from 'react'
import {
  View, Text, FlatList, TouchableOpacity, StyleSheet,
  ActivityIndicator, TextInput, ScrollView, RefreshControl,
} from 'react-native'
import { SafeAreaView } from 'react-native-safe-area-context'
import api, { clearToken, setUnauthorizedHandler } from '../api/client'
import useCart from '../store/useCart'
import SaleSheet from '../components/SaleSheet'
import ExpenseModal from '../components/ExpenseModal'
import TillSummaryModal from '../components/TillSummaryModal'
import RecentSalesModal from '../components/RecentSalesModal'

const fmt = (n) => parseFloat(n || 0).toLocaleString('en-KE', { minimumFractionDigits: 2 })
const CARD_COLORS = ['#eff6ff', '#f0fdf4']

export default function POSScreen({ navigation }) {
  const [location, setLocation]           = useState(null)
  const [isAdmin, setIsAdmin]             = useState(false)
  const [products, setProducts]           = useState([])
  const [loadingInit, setLoadingInit]     = useState(true)
  const [refreshing, setRefreshing]       = useState(false)
  const [search, setSearch]               = useState('')
  const [searching, setSearching]         = useState(false)
  const [searchResults, setSearchResults] = useState([])
  const [saleOpen, setSaleOpen]           = useState(false)
  const [saleProduct, setSaleProduct]     = useState(null)
  const [expenseOpen, setExpenseOpen]     = useState(false)
  const [tillOpen, setTillOpen]           = useState(false)
  const [recentOpen, setRecentOpen]       = useState(false)
  const [successMsg, setSuccessMsg]       = useState('')

  const items         = useCart(s => s.items)
  const setLocationId = useCart(s => s.setLocation)
  const count         = items.reduce((s, i) => s + i.qty, 0)
  const total         = items.reduce((s, i) => s + i.price * i.qty, 0)

  useEffect(() => {
    // Handle expired/revoked token globally
    setUnauthorizedHandler(async () => {
      await clearToken()
      navigation.replace('Login')
    })

    ;(async () => {
      try {
        const res = await api.get('/api/mobile/pos-details')
        const loc = res.data.default_location
        setLocation(loc)
        setIsAdmin(!!res.data.user?.is_admin)
        if (loc?.id) {
          setLocationId(loc.id)
          await loadProducts('', loc.id)
        }
      } catch {
        await clearToken()
        navigation.replace('Login')
      } finally {
        setLoadingInit(false)
      }
    })()
  }, [])

  async function loadProducts(term = '', locId) {
    try {
      const res = await api.get('/api/mobile/products', {
        params: { term, location_id: locId || location?.id },
      })
      setProducts(res.data?.products || [])
    } catch { /* ignore */ }
  }

  async function onRefresh() {
    setRefreshing(true)
    await loadProducts('', location?.id)
    setRefreshing(false)
  }

  useEffect(() => {
    if (!search.trim()) { setSearchResults([]); setSearching(false); return }
    setSearching(true)
    const t = setTimeout(async () => {
      try {
        const res = await api.get('/api/mobile/products', {
          params: { term: search.trim(), location_id: location?.id },
        })
        setSearchResults(res.data?.products || [])
      } catch { /* ignore */ } finally {
        setSearching(false)
      }
    }, 300)
    return () => clearTimeout(t)
  }, [search, location?.id])

  function openProduct(p) {
    setSaleProduct(p)
    setSaleOpen(true)
    setSearch('')
  }

  function openCart() {
    setSaleProduct(null)
    setSaleOpen(true)
  }

  async function logout() {
    try { await api.post('/api/mobile/logout') } catch { /* ignore */ }
    await clearToken()
    navigation.replace('Login')
  }

  function onSaleSuccess() {
    setSuccessMsg('Sale completed!')
    setTimeout(() => setSuccessMsg(''), 2500)
    // Refresh product stock counts
    loadProducts('', location?.id)
  }

  const renderProduct = useCallback(({ item: p, index }) => {
    const bg  = CARD_COLORS[index % CARD_COLORS.length]
    const qty = parseFloat(p.qty_available || 0)
    return (
      <TouchableOpacity style={[styles.card, { backgroundColor: bg }]} onPress={() => openProduct(p)} activeOpacity={0.75}>
        <Text style={styles.cardName} numberOfLines={2}>
          {p.name}
          {p.variation && p.variation !== 'DUMMY'
            ? <Text style={styles.cardVariation}> · {p.variation}</Text>
            : null}
        </Text>
        <Text style={styles.cardPrice}>Ksh {fmt(p.selling_price)}</Text>
        {p.enable_stock == 1
          ? <Text style={[styles.cardStock, { color: qty > 5 ? '#16a34a' : qty > 0 ? '#f59e0b' : '#ef4444' }]}>
              Stock: {qty.toFixed(0)} {p.unit || ''}
            </Text>
          : null}
      </TouchableOpacity>
    )
  }, [])

  if (loadingInit) {
    return (
      <View style={styles.loadingFull}>
        <ActivityIndicator size="large" color="#2563eb" />
        <Text style={{ marginTop: 12, color: '#64748b' }}>Loading…</Text>
      </View>
    )
  }

  return (
    <SafeAreaView style={styles.safe} edges={['top']}>

      {/* Header */}
      <View style={styles.header}>
        <View style={{ flexDirection: 'row', gap: 6 }}>
          <TouchableOpacity style={styles.headerPill} onPress={() => setTillOpen(true)}>
            <Text style={styles.headerPillText}>Till</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.headerPill} onPress={() => setRecentOpen(true)}>
            <Text style={styles.headerPillText}>Sales</Text>
          </TouchableOpacity>
          {isAdmin && (
            <TouchableOpacity style={[styles.headerPill, styles.headerPillAdmin]} onPress={() => navigation.navigate('Admin')}>
              <Text style={styles.headerPillText}>Admin</Text>
            </TouchableOpacity>
          )}
        </View>
        <View style={styles.headerCenter}>
          <Text style={styles.headerTitle}>Point of Sale</Text>
          {location?.name ? <Text style={styles.headerLoc} numberOfLines={1}>📍 {location.name}</Text> : null}
        </View>
        <TouchableOpacity style={styles.headerPill} onPress={logout}>
          <Text style={styles.headerPillText}>Logout</Text>
        </TouchableOpacity>
      </View>

      {/* Search */}
      <View style={styles.searchContainer}>
        <View style={styles.searchWrap}>
          <Text style={styles.searchIcon}>🔍</Text>
          <TextInput
            style={styles.searchInput}
            value={search}
            onChangeText={setSearch}
            placeholder="Search products…"
            placeholderTextColor="#94a3b8"
            autoCorrect={false}
            returnKeyType="search"
          />
          {searching
            ? <ActivityIndicator size="small" color="#2563eb" />
            : search.length > 0
              ? <TouchableOpacity onPress={() => setSearch('')}><Text style={styles.searchClear}>✕</Text></TouchableOpacity>
              : null}
        </View>

        {search.trim().length > 0 && (
          <View style={styles.dropdown}>
            {searching ? (
              <View style={styles.dropdownLoading}><ActivityIndicator color="#2563eb" /></View>
            ) : searchResults.length === 0 ? (
              <Text style={styles.dropdownEmpty}>No products found</Text>
            ) : (
              <ScrollView keyboardShouldPersistTaps="handled" style={{ maxHeight: 300 }}>
                {searchResults.map(p => (
                  <TouchableOpacity
                    key={String(p.variation_id)}
                    style={styles.dropdownItem}
                    onPress={() => openProduct(p)}
                    activeOpacity={0.7}
                  >
                    <View style={{ flex: 1 }}>
                      <Text style={styles.dropdownName} numberOfLines={1}>
                        {p.name}{p.variation && p.variation !== 'DUMMY' ? ` · ${p.variation}` : ''}
                      </Text>
                      {p.enable_stock == 1
                        ? <Text style={styles.dropdownStock}>Stock: {parseFloat(p.qty_available || 0).toFixed(0)}</Text>
                        : null}
                    </View>
                    <Text style={styles.dropdownPrice}>Ksh {fmt(p.selling_price)}</Text>
                  </TouchableOpacity>
                ))}
              </ScrollView>
            )}
          </View>
        )}
      </View>

      {/* Product grid */}
      <FlatList
        data={products}
        keyExtractor={p => String(p.variation_id)}
        renderItem={renderProduct}
        numColumns={2}
        columnWrapperStyle={styles.row}
        contentContainerStyle={styles.grid}
        showsVerticalScrollIndicator={false}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={['#2563eb']} />}
        ListEmptyComponent={
          <View style={styles.emptyWrap}>
            <Text style={styles.emptyText}>No products available</Text>
            <Text style={styles.emptyHint}>Pull down to refresh</Text>
          </View>
        }
      />

      {/* Bottom bar */}
      <TouchableOpacity
        style={[styles.bottomBar, !count && styles.bottomBarEmpty]}
        onPress={openCart}
        activeOpacity={0.9}
      >
        <View style={styles.bottomLeft}>
          <Text style={styles.bottomIcon}>🛒</Text>
          {count > 0 && (
            <View style={styles.badge}><Text style={styles.badgeText}>{count}</Text></View>
          )}
          <Text style={styles.bottomLabel}>{count > 0 ? `${count} item${count !== 1 ? 's' : ''}` : 'Order empty'}</Text>
        </View>
        {count > 0 && <Text style={styles.bottomTotal}>Ksh {fmt(total)}</Text>}
        <View style={styles.expenseBtn}>
          <TouchableOpacity onPress={(e) => { e.stopPropagation?.(); setExpenseOpen(true) }} activeOpacity={0.85}>
            <Text style={styles.expenseText}>+ Expense</Text>
          </TouchableOpacity>
        </View>
      </TouchableOpacity>

      <SaleSheet
        open={saleOpen}
        onClose={() => setSaleOpen(false)}
        initialProduct={saleProduct}
        locationId={location?.id}
        onSuccess={onSaleSuccess}
      />
      <ExpenseModal
        open={expenseOpen}
        onClose={() => setExpenseOpen(false)}
        locationId={location?.id}
        onSuccess={() => setSuccessMsg('Expense added!')}
      />
      <TillSummaryModal
        open={tillOpen}
        onClose={() => setTillOpen(false)}
        locationId={location?.id}
        onLogout={logout}
      />
      <RecentSalesModal
        open={recentOpen}
        onClose={() => setRecentOpen(false)}
        locationId={location?.id}
      />

      {!!successMsg && (
        <View style={styles.toast} pointerEvents="none">
          <Text style={styles.toastText}>✓ {successMsg}</Text>
        </View>
      )}

    </SafeAreaView>
  )
}

const styles = StyleSheet.create({
  safe:        { flex: 1, backgroundColor: '#f1f5f9' },
  loadingFull: { flex: 1, alignItems: 'center', justifyContent: 'center', backgroundColor: '#f1f5f9' },

  header: {
    backgroundColor: '#2563eb', flexDirection: 'row', alignItems: 'center',
    justifyContent: 'space-between', paddingHorizontal: 14, paddingVertical: 10,
  },
  headerCenter:   { alignItems: 'center', flex: 1 },
  headerTitle:    { color: '#fff', fontWeight: '800', fontSize: 16 },
  headerLoc:      { color: 'rgba(255,255,255,0.75)', fontSize: 11, marginTop: 1 },
  headerPill:      { backgroundColor: 'rgba(255,255,255,0.15)', paddingHorizontal: 12, paddingVertical: 6, borderRadius: 10 },
  headerPillAdmin: { backgroundColor: 'rgba(234,179,8,0.3)' },
  headerPillText:  { color: '#fff', fontSize: 12, fontWeight: '600' },

  searchContainer: { zIndex: 100, marginHorizontal: 12, marginTop: 10, marginBottom: 4 },
  searchWrap: {
    flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', borderRadius: 16,
    paddingHorizontal: 14, paddingVertical: 2,
    shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.06, shadowRadius: 6, elevation: 3,
  },
  searchIcon:  { fontSize: 15, marginRight: 8 },
  searchInput: { flex: 1, fontSize: 14, color: '#1e293b', paddingVertical: 12 },
  searchClear: { color: '#94a3b8', fontSize: 16, paddingLeft: 8 },

  dropdown: {
    backgroundColor: '#fff', borderRadius: 16, marginTop: 4, overflow: 'hidden',
    shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.12, shadowRadius: 12, elevation: 8,
  },
  dropdownLoading: { padding: 20, alignItems: 'center' },
  dropdownEmpty:   { padding: 16, textAlign: 'center', color: '#94a3b8', fontSize: 14 },
  dropdownItem: {
    flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center',
    paddingHorizontal: 16, paddingVertical: 12, borderBottomWidth: 1, borderBottomColor: '#f1f5f9',
  },
  dropdownName:  { fontSize: 14, fontWeight: '600', color: '#1e293b' },
  dropdownStock: { fontSize: 11, color: '#64748b', marginTop: 2 },
  dropdownPrice: { fontSize: 14, fontWeight: '800', color: '#16a34a', marginLeft: 12 },

  grid: { paddingHorizontal: 8, paddingBottom: 100 },
  row:  { paddingHorizontal: 4, gap: 8 },
  card: {
    flex: 1, borderRadius: 16, padding: 14, marginBottom: 8,
    shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.06, shadowRadius: 4, elevation: 2,
  },
  cardName:      { fontSize: 13, fontWeight: '600', color: '#1e293b', lineHeight: 18, marginBottom: 8, minHeight: 36 },
  cardVariation: { color: '#94a3b8', fontWeight: '400' },
  cardPrice:     { fontSize: 15, fontWeight: '800', color: '#16a34a' },
  cardStock:     { fontSize: 11, fontWeight: '500', marginTop: 4 },

  emptyWrap: { alignItems: 'center', paddingTop: 60 },
  emptyText: { color: '#94a3b8', fontSize: 15 },
  emptyHint: { color: '#cbd5e1', fontSize: 13, marginTop: 6 },

  bottomBar: {
    position: 'absolute', bottom: 0, left: 0, right: 0,
    backgroundColor: '#1e293b', flexDirection: 'row', alignItems: 'center',
    paddingHorizontal: 16, paddingTop: 12, paddingBottom: 20,
    shadowColor: '#000', shadowOffset: { width: 0, height: -4 }, shadowOpacity: 0.18, shadowRadius: 14, elevation: 14,
  },
  bottomBarEmpty: { opacity: 0.85 },
  bottomLeft:  { flex: 1, flexDirection: 'row', alignItems: 'center' },
  bottomIcon:  { fontSize: 20 },
  badge: {
    backgroundColor: '#ef4444', borderRadius: 10, minWidth: 20, height: 20,
    alignItems: 'center', justifyContent: 'center', paddingHorizontal: 4, marginLeft: 4,
  },
  badgeText:   { color: '#fff', fontSize: 11, fontWeight: '700' },
  bottomLabel: { color: 'rgba(255,255,255,0.65)', fontSize: 13, marginLeft: 8 },
  bottomTotal: { color: '#fff', fontWeight: '800', fontSize: 16, marginRight: 12 },
  expenseBtn:  { backgroundColor: '#475569', borderRadius: 12, paddingHorizontal: 14, paddingVertical: 10 },
  expenseText: { color: '#fff', fontWeight: '600', fontSize: 13 },

  toast: {
    position: 'absolute', top: 80, alignSelf: 'center',
    backgroundColor: '#16a34a', paddingHorizontal: 22, paddingVertical: 12,
    borderRadius: 24, elevation: 10,
  },
  toastText: { color: '#fff', fontWeight: '700', fontSize: 14 },
})
