import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function Login() {
  const { login, loading } = useAuth();
  const navigate = useNavigate();
  const [selected, setSelected] = useState('pastor');

  const handleLogin = async () => {
    await login(selected);
    const paths = { pastor: '/dashboard', leader: '/dashboard', youth: '/profile' };
    navigate(paths[selected]);
  };

  return (
    <div style={{
      minHeight: '100vh', display: 'flex', alignItems: 'center', justifyContent: 'center',
      background: 'var(--color-bg-primary)', padding: 20,
    }}>
      {/* Background decoration */}
      <div style={{
        position: 'fixed', top: '30%', left: '50%', transform: 'translate(-50%,-50%)',
        width: 500, height: 500, borderRadius: '50%',
        background: 'radial-gradient(circle, rgba(201,168,76,0.06) 0%, transparent 70%)',
        pointerEvents: 'none',
      }} />

      <div style={{ width: '100%', maxWidth: 400, animation: 'fadeIn 0.4s ease' }}>
        {/* Church branding */}
        <div style={{ textAlign: 'center', marginBottom: 40 }}>
          <div style={{
            width: 64, height: 64, borderRadius: '50%',
            background: 'var(--color-accent-dim)',
            border: '2px solid rgba(201,168,76,0.3)',
            margin: '0 auto 16px',
          }} />
          <div style={{ fontSize: 13, color: 'var(--color-text-muted)', letterSpacing: '0.12em', marginBottom: 6 }}>
            基督教米兰华人复兴教会
          </div>
          <h1 style={{ fontSize: 24, fontWeight: 700 }}>青年牧区管理系统</h1>
          <p style={{ color: 'var(--color-text-secondary)', fontSize: 13, marginTop: 8 }}>
            Youth Ministry Pastoral Care System
          </p>
        </div>

        {/* Login card */}
        <div className="card" style={{ padding: 28 }}>
          {/* Demo role selector */}
          <div style={{ marginBottom: 24 }}>
            <div style={{ fontSize: 13, color: 'var(--color-text-muted)', marginBottom: 12, textAlign: 'center' }}>
              演示模式 — 选择角色登录
            </div>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
              {[
                { id: 'pastor', label: '牧区长', desc: '何恩典 · 全局管理权限' },
                { id: 'leader', label: '小组长', desc: '王建国 · A组晨星小组' },
                { id: 'youth', label: '青少年', desc: '李晓雨 · 个人主页' },
              ].map(role => (
                <button
                  key={role.id}
                  onClick={() => setSelected(role.id)}
                  style={{
                    display: 'flex', alignItems: 'center', gap: 12,
                    padding: '12px 16px', borderRadius: 'var(--radius-sm)',
                    background: selected === role.id ? 'var(--color-accent-dim)' : 'var(--color-bg-secondary)',
                    border: selected === role.id ? '1px solid rgba(201,168,76,0.4)' : '1px solid var(--color-border)',
                    cursor: 'pointer', transition: 'all 0.2s', textAlign: 'left', width: '100%',
                  }}
                >
                  <div>
                    <div style={{ fontSize: 14, fontWeight: 600, color: selected === role.id ? 'var(--color-accent)' : 'var(--color-text-primary)' }}>
                      {role.label}
                    </div>
                    <div style={{ fontSize: 12, color: 'var(--color-text-muted)', marginTop: 2 }}>{role.desc}</div>
                  </div>
                  {selected === role.id && (
                    <span style={{ marginLeft: 'auto', width: 8, height: 8, borderRadius: '50%', background: 'var(--color-accent)', flexShrink: 0 }} />
                  )}
                </button>
              ))}
            </div>
          </div>

          <div className="divider" />

          <button
            onClick={handleLogin}
            disabled={loading}
            className="btn btn-primary"
            style={{ width: '100%', justifyContent: 'center', padding: '12px', fontSize: 15, marginTop: 4 }}
          >
            {loading ? (
              <><span className="spinner" style={{ width: 18, height: 18, borderWidth: 2 }} />登录中…</>
            ) : (
              <><span style={{ fontSize: 18 }}>G</span> 使用 Google 账号登录</>
            )}
          </button>

          <p style={{ fontSize: 11, color: 'var(--color-text-muted)', textAlign: 'center', marginTop: 16, lineHeight: 1.7 }}>
            正式部署后将启用 Google OAuth 2.0 认证<br />
            本系统仅限教会授权人员使用
          </p>
        </div>

        <p style={{ textAlign: 'center', fontSize: 12, color: 'var(--color-text-muted)', marginTop: 24 }}>
          © 2026 基督教米兰华人复兴教会
        </p>
      </div>
    </div>
  );
}
