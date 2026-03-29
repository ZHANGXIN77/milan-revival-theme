import { useState } from 'react';
import Sidebar from './Sidebar';

export default function Layout({ children }) {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  return (
    <div style={{ display: 'flex', minHeight: '100vh' }}>
      <Sidebar isOpen={sidebarOpen} onClose={() => setSidebarOpen(false)} />

      {/* Main content */}
      <div style={{
        flex: 1, marginLeft: 240, display: 'flex', flexDirection: 'column', minHeight: '100vh',
        transition: 'margin-left 0.3s',
      }}
        className="main-content"
      >
        {/* Top bar (mobile) */}
        <header style={{
          display: 'none', alignItems: 'center', gap: 16,
          padding: '0 20px', height: 56,
          background: '#111111', borderBottom: '1px solid var(--color-border)',
          position: 'sticky', top: 0, zIndex: 50,
        }} className="mobile-header">
          <button onClick={() => setSidebarOpen(true)} style={{ background: 'none', border: 'none', color: 'var(--color-text-primary)', fontSize: 13, cursor: 'pointer', lineHeight: 1, padding: '6px 10px', borderRadius: 'var(--radius-sm)', border: '1px solid var(--color-border)' }}>
            菜单
          </button>
          <span style={{ fontSize: 15, fontWeight: 600, color: 'var(--color-accent)' }}>青年牧区管理</span>
        </header>

        <main style={{ flex: 1, padding: '28px 28px', maxWidth: 1100 }}>
          {children}
        </main>
      </div>

      <style>{`
        @media (max-width: 768px) {
          .main-content { margin-left: 0 !important; }
          .mobile-header { display: flex !important; }
          main { padding: 20px 16px !important; }
        }
      `}</style>
    </div>
  );
}
