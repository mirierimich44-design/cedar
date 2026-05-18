import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import LoginScreen from './screens/LoginScreen'
import POSScreen from './screens/POSScreen'

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<LoginScreen />} />
        <Route path="/pos"   element={<POSScreen />} />
        <Route path="*"      element={<Navigate to="/login" replace />} />
      </Routes>
    </BrowserRouter>
  )
}
