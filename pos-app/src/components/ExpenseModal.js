import { useState, useEffect } from 'react'
import {
  View, Text, Modal, TouchableOpacity, TextInput, StyleSheet,
  TouchableWithoutFeedback, ActivityIndicator, ScrollView,
} from 'react-native'
import api from '../api/client'

export default function ExpenseModal({ open, onClose, locationId, onSuccess }) {
  const [categories, setCategories] = useState([])
  const [categoryId, setCategoryId] = useState(null)
  const [amount, setAmount]         = useState('')
  const [note, setNote]             = useState('')
  const [loading, setLoading]       = useState(false)
  const [error, setError]           = useState('')

  useEffect(() => {
    if (open && categories.length === 0) {
      api.get('/api/mobile/expense-categories')
        .then(r => setCategories(r.data?.categories || []))
        .catch(() => {})
    }
  }, [open])

  async function submit() {
    if (!categoryId) { setError('Please select a category.'); return }
    if (!amount || parseFloat(amount) <= 0) { setError('Please enter a valid amount.'); return }
    setLoading(true); setError('')
    try {
      await api.post('/api/mobile/expense', {
        expense_category_id: categoryId,
        final_total: parseFloat(amount).toFixed(2),
        additional_notes: note,
        location_id: locationId,
        transaction_date: new Date().toISOString().slice(0, 10),
        status: 'final',
        'payment[0][method]': 'cash',
        'payment[0][amount]': parseFloat(amount).toFixed(2),
      })
      onSuccess?.()
      onClose()
      setAmount(''); setNote(''); setCategoryId(null)
    } catch (err) {
      setError(err.response?.data?.msg || err.response?.data?.message || 'Failed to add expense.')
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
        <Text style={styles.title}>Add Expense</Text>

        {!!error && <View style={styles.errorBox}><Text style={styles.errorText}>{error}</Text></View>}

        {/* Category */}
        <Text style={styles.label}>CATEGORY</Text>
        {categories.length === 0
          ? <ActivityIndicator color="#2563eb" style={{ marginBottom: 16 }} />
          : (
            <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.catScroll}>
              {categories.map(c => (
                <TouchableOpacity
                  key={c.id}
                  style={[styles.catChip, categoryId === c.id && styles.catChipActive]}
                  onPress={() => setCategoryId(c.id)}
                >
                  <Text style={[styles.catChipText, categoryId === c.id && styles.catChipTextActive]}>
                    {c.name}
                  </Text>
                </TouchableOpacity>
              ))}
            </ScrollView>
          )
        }

        {/* Amount */}
        <Text style={styles.label}>AMOUNT (Ksh)</Text>
        <TextInput
          style={styles.input}
          value={amount}
          onChangeText={setAmount}
          placeholder="0.00"
          placeholderTextColor="#9ca3af"
          keyboardType="decimal-pad"
        />

        {/* Note */}
        <Text style={styles.label}>NOTE (optional)</Text>
        <TextInput
          style={[styles.input, styles.inputMulti]}
          value={note}
          onChangeText={setNote}
          placeholder="What was this expense for?"
          placeholderTextColor="#9ca3af"
          multiline
          numberOfLines={2}
        />

        <TouchableOpacity
          style={[styles.submitBtn, loading && styles.submitOff]}
          onPress={submit}
          disabled={loading}
          activeOpacity={0.85}
        >
          {loading
            ? <ActivityIndicator color="#fff" />
            : <Text style={styles.submitText}>Add Expense</Text>
          }
        </TouchableOpacity>
      </View>
    </Modal>
  )
}

const styles = StyleSheet.create({
  backdrop: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)' },
  sheet: {
    backgroundColor: '#fff', borderTopLeftRadius: 28, borderTopRightRadius: 28,
    padding: 22, paddingBottom: 40,
  },
  handle:    { width: 40, height: 4, backgroundColor: '#e2e8f0', borderRadius: 2, alignSelf: 'center', marginBottom: 18 },
  title:     { fontSize: 19, fontWeight: '800', color: '#0f172a', marginBottom: 16 },
  errorBox:  { backgroundColor: '#fef2f2', borderRadius: 12, padding: 12, marginBottom: 14 },
  errorText: { color: '#dc2626', fontSize: 13 },

  label: { fontSize: 11, fontWeight: '700', color: '#64748b', letterSpacing: 1, marginBottom: 8 },

  catScroll: { marginBottom: 18 },
  catChip: {
    borderWidth: 1.5, borderColor: '#e2e8f0', borderRadius: 20,
    paddingHorizontal: 14, paddingVertical: 8, marginRight: 8,
  },
  catChipActive:     { borderColor: '#2563eb', backgroundColor: '#eff6ff' },
  catChipText:       { fontSize: 13, fontWeight: '600', color: '#64748b' },
  catChipTextActive: { color: '#2563eb' },

  input: {
    borderWidth: 1.5, borderColor: '#e2e8f0', borderRadius: 14,
    paddingHorizontal: 16, paddingVertical: 12, fontSize: 15, color: '#1e293b', marginBottom: 16,
  },
  inputMulti: { height: 72, textAlignVertical: 'top' },

  submitBtn:  { backgroundColor: '#475569', borderRadius: 16, paddingVertical: 15, alignItems: 'center' },
  submitOff:  { opacity: 0.6 },
  submitText: { color: '#fff', fontWeight: '700', fontSize: 15 },
})
