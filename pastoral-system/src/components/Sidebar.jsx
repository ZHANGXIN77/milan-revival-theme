import { useState, useEffect } from 'react';
import { NavLink, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import Avatar from './Avatar';

const NAV_PASTOR = [
  { path: '/dashboard', label: '总览仪表板' },
  { path: '/members', label: '人员管理' },
  { path: '/attendance', label: '出席记录' },
  { path: '/prayers', label: '代祷事项' },
  { path: '/groups', label: '小组管理' },
  { path: '/meetings', label: '聚会安排' },
  { path: '/profile', label: '我的主页' },
  { path: '/user-management', label: '用户管理' },
];

const NAV_LEADER = [
  { path: '/dashboard', label: '小组概览' },
  { path: '/members', label: '我的组员' },
  { path: '/attendance', label: '出席记录' },
  { path: '/prayers', label: '代祷事项' },
  { path: '/meetings', label: '聚会安排' },
  { path: '/profile', label: '我的主页' },
];

const NAV_YOUTH = [
  { path: '/profile', label: '我的主页' },
  { path: '/prayers', label: '代祷事项' },
];

export default function Sidebar({ isOpen, onClose }) {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const [theme, setTheme] = useState(() => document.documentElement.getAttribute('data-theme') || 'dark');

  useEffect(() => {
    const saved = localStorage.getItem('theme');
    if (saved) {
      document.documentElement.setAttribute('data-theme', saved);
      setTheme(saved);
    }
  }, []);

  const toggleTheme = () => {
    const next = theme === 'light' ? 'dark' : 'light';
    if (next === 'dark') {
      document.documentElement.removeAttribute('data-theme');
      localStorage.removeItem('theme');
    } else {
      document.documentElement.setAttribute('data-theme', next);
      localStorage.setItem('theme', next);
    }
    setTheme(next);
  };

  const nav = user?.role === 'pastor' ? NAV_PASTOR : user?.role === 'leader' ? NAV_LEADER : NAV_YOUTH;

  const roleLabel = { pastor: '牧区长', leader: '小组长', youth: '青少年' }[user?.role] || '';

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  return (
    <>
      {/* Mobile overlay */}
      {isOpen && <div onClick={onClose} style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.6)', zIndex: 99 }} />}

      <aside className={`sidebar-panel${isOpen ? ' is-open' : ''}`} style={{
        position: 'fixed', top: 0, left: 0, bottom: 0,
        width: 240, background: 'var(--color-bg-sidebar)',
        borderRight: '1px solid var(--color-border)',
        display: 'flex', flexDirection: 'column',
        zIndex: 100, transition: 'transform 0.3s ease',
      }}>
        {/* Logo */}
        <div style={{ padding: '20px 20px 16px', borderBottom: '1px solid var(--color-border)' }}>
          <div style={{ fontSize: 11, color: 'var(--color-text-muted)', letterSpacing: '0.1em', marginBottom: 4 }}>
            米兰复兴教会
          </div>
          <div style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-accent)' }}>
            青年牧区管理
          </div>
        </div>

        {/* User info */}
        <div style={{ padding: '16px 20px', borderBottom: '1px solid var(--color-border)', display: 'flex', alignItems: 'center', gap: 12 }}>
          <Avatar name={user?.name} src={user?.avatar} size={38} />
          <div>
            <div style={{ fontSize: 14, fontWeight: 600 }}>{user?.name}</div>
            <div style={{ fontSize: 11, color: 'var(--color-accent)', marginTop: 2 }}>{roleLabel}</div>
          </div>
        </div>

        {/* Nav */}
        <nav style={{ flex: 1, padding: '12px 12px', overflowY: 'auto' }}>
          {nav.map(item => (
            <NavLink
              key={item.path}
              to={item.path}
              onClick={onClose}
              style={({ isActive }) => ({
                display: 'flex', alignItems: 'center', gap: 10,
                padding: '10px 12px', borderRadius: 'var(--radius-sm)',
                fontSize: 14, fontWeight: 500, marginBottom: 2,
                color: isActive ? 'var(--color-accent)' : 'var(--color-text-secondary)',
                background: isActive ? 'var(--color-accent-dim)' : 'transparent',
                textDecoration: 'none', transition: 'all 0.2s ease',
              })}
            >
              {item.label}
            </NavLink>
          ))}
        </nav>

        {/* Theme toggle + Logout */}
        <div style={{ padding: '12px', borderTop: '1px solid var(--color-border)', display: 'flex', flexDirection: 'column', gap: 4 }}>
          <button onClick={toggleTheme} className="btn btn-ghost" style={{ width: '100%', justifyContent: 'flex-start', gap: 10 }}>
            {theme === 'light' ? (
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
              </svg>
            ) : (
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/>
                <line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/>
                <line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
              </svg>
            )}
            {theme === 'light' ? '切换暗色模式' : '切换亮色模式'}
          </button>
          <button onClick={handleLogout} className="btn btn-ghost" style={{ width: '100%', justifyContent: 'flex-start', gap: 10 }}>
            退出登录
          </button>
        </div>
      </aside>
    </>
  );
}
