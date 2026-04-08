import { HashRouter, Routes, Route, Navigate } from 'react-router-dom';
import { lazy, Suspense } from 'react';
import { AuthProvider, useAuth } from './context/AuthContext';
import { AppProvider } from './context/AppContext';
import Layout from './components/Layout';
import Login from './pages/Login';

// 按路由懒加载，减少首屏体积，不同角色只加载自己需要的页面
const Dashboard = lazy(() => import('./pages/Dashboard'));
const MemberList = lazy(() => import('./pages/MemberList'));
const MemberProfile = lazy(() => import('./pages/MemberProfile'));
const Attendance = lazy(() => import('./pages/Attendance'));
const PrayerRequests = lazy(() => import('./pages/PrayerRequests'));
const GroupManagement = lazy(() => import('./pages/GroupManagement'));
const YouthProfile = lazy(() => import('./pages/YouthProfile'));
const Meetings = lazy(() => import('./pages/Meetings'));
const UserManagement = lazy(() => import('./pages/UserManagement'));

function PageLoader() {
  return <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '60vh' }}><span className="spinner" /></div>;
}

function ProtectedRoute({ children, allowedRoles }) {
  const { user } = useAuth();
  if (!user) return <Navigate to="/login" replace />;
  if (allowedRoles && !allowedRoles.includes(user.role)) return <Navigate to={user.role === 'youth' ? '/profile' : '/dashboard'} replace />;
  return children;
}

function AppRoutes() {
  const { user } = useAuth();
  return (
    <Suspense fallback={<PageLoader />}>
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
        <ProtectedRoute allowedRoles={['pastor', 'leader', 'youth']}>
          <Layout><Meetings /></Layout>
        </ProtectedRoute>
      } />
      <Route path="/profile" element={
        <ProtectedRoute>
          <Layout><YouthProfile /></Layout>
        </ProtectedRoute>
      } />
      <Route path="/user-management" element={
        <ProtectedRoute allowedRoles={['pastor']}>
          <Layout><UserManagement /></Layout>
        </ProtectedRoute>
      } />
    </Routes>
    </Suspense>
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
