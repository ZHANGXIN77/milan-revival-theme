import { HashRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './context/AuthContext';
import { AppProvider } from './context/AppContext';
import Layout from './components/Layout';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import MemberList from './pages/MemberList';
import MemberProfile from './pages/MemberProfile';
import Attendance from './pages/Attendance';
import PrayerRequests from './pages/PrayerRequests';
import GroupManagement from './pages/GroupManagement';
import YouthProfile from './pages/YouthProfile';
import Meetings from './pages/Meetings';

function ProtectedRoute({ children, allowedRoles }) {
  const { user } = useAuth();
  if (!user) return <Navigate to="/login" replace />;
  if (allowedRoles && !allowedRoles.includes(user.role)) return <Navigate to="/dashboard" replace />;
  return children;
}

function AppRoutes() {
  const { user } = useAuth();
  return (
    <Routes>
      <Route path="/login" element={user ? <Navigate to={user.role === 'youth' ? '/profile' : '/dashboard'} /> : <Login />} />
      <Route path="/" element={<Navigate to={user ? (user.role === 'youth' ? '/profile' : '/dashboard') : '/login'} replace />} />

      <Route path="/dashboard" element={
        <ProtectedRoute allowedRoles={['pastor', 'leader']}>
          <Layout><Dashboard /></Layout>
        </ProtectedRoute>
      } />
      <Route path="/members" element={
        <ProtectedRoute allowedRoles={['pastor', 'leader']}>
          <Layout><MemberList /></Layout>
        </ProtectedRoute>
      } />
      <Route path="/members/:id" element={
        <ProtectedRoute allowedRoles={['pastor', 'leader']}>
          <Layout><MemberProfile /></Layout>
        </ProtectedRoute>
      } />
      <Route path="/attendance" element={
        <ProtectedRoute allowedRoles={['pastor', 'leader']}>
          <Layout><Attendance /></Layout>
        </ProtectedRoute>
      } />
      <Route path="/prayers" element={
        <ProtectedRoute>
          <Layout><PrayerRequests /></Layout>
        </ProtectedRoute>
      } />
      <Route path="/groups" element={
        <ProtectedRoute allowedRoles={['pastor']}>
          <Layout><GroupManagement /></Layout>
        </ProtectedRoute>
      } />
      <Route path="/meetings" element={
        <ProtectedRoute allowedRoles={['pastor', 'leader']}>
          <Layout><Meetings /></Layout>
        </ProtectedRoute>
      } />
      <Route path="/profile" element={
        <ProtectedRoute>
          <Layout><YouthProfile /></Layout>
        </ProtectedRoute>
      } />
    </Routes>
  );
}

export default function App() {
  return (
    <HashRouter>
      <AuthProvider>
        <AppProvider>
          <AppRoutes />
        </AppProvider>
      </AuthProvider>
    </HashRouter>
  );
}
