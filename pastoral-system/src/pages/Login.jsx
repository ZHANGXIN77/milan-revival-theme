import { useEffect, useRef } from 'react';
import { Navigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function Login() {
  const { user, loading, error } = useAuth();
  const btnRef = useRef(null);

  useEffect(() => {
    if (loading || user) return;

    const renderBtn = () => {
      if (!window.google?.accounts?.id || !btnRef.current) {
        setTimeout(renderBtn, 200);
        return;
      }
      btnRef.current.innerHTML = '';
      window.google.accounts.id.renderButton(btnRef.current, {
        type: 'standard',
        theme: 'outline',
        size: 'large',
        text: 'signin_with',
        shape: 'rectangular',
        width: 320,
        locale: 'zh_CN',
      });
    };

    renderBtn();
  }, [loading, user]);

  if (user) {
    return <Navigate to={user.role === 'youth' ? '/profile' : '/dashboard'} replace />;
  }

  return (
    <div style={{
      minHeight: '100vh', display: 'flex', alignItems: 'center', justifyContent: 'center',
      background: 'var(--color-bg-primary)', padding: 20,
    }}>
      <div style={{
        position: 'fixed', top: '30%', left: '50%', transform: 'translate(-50%,-50%)',
        width: 500, height: 500, borderRadius: '50%',
        background: 'radial-gradient(circle, rgba(201,168,76,0.06) 0%, transparent 70%)',
        pointerEvents: 'none',
      }} />

      <div style={{ width: '100%', maxWidth: 400, animation: 'fadeIn 0.4s ease' }}>
        <div style={{ textAlign: 'center', marginBottom: 40 }}>
          <div style={{
            width: 64, height: 64, borderRadius: '50%',
            background: 'var(--color-accent-dim)',
            border: '2px solid rgba(201,168,76,0.3)',
            margin: '0 auto 16px',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            fontSize: 28, color: 'var(--color-accent)',
          }}>
            ✦
          </div>
          <div style={{ fontSize: 13, color: 'var(--color-text-muted)', letterSpacing: '0.12em', marginBottom: 6 }}>
            基督教米兰华人复兴教会
          </div>
          <h1 style={{ fontSize: 24, fontWeight: 700 }}>青年牧区管理系统</h1>
          <p style={{ color: 'var(--color-text-secondary)', fontSize: 13, marginTop: 8 }}>
            Youth Ministry Pastoral Care System
          </p>
        </div>

        <div className="card" style={{ padding: 28 }}>
          {loading ? (
            <div style={{ display: 'flex', justifyContent: 'center', padding: 20 }}>
              <span className="spinner" />
            </div>
          ) : (
            <>
              <div style={{ fontSize: 14, color: 'var(--color-text-secondary)', textAlign: 'center', marginBottom: 20 }}>
                请使用已授权的 Google 账号登录
              </div>

              <div style={{ display: 'flex', justifyContent: 'center', marginBottom: 16 }}>
                <div ref={btnRef} />
              </div>

              {error && (
                <div style={{
                  background: 'rgba(201,168,76,0.1)', border: '1px solid rgba(201,168,76,0.3)',
                  borderRadius: 'var(--radius-sm)', padding: '10px 14px',
                  fontSize: 13, color: 'var(--color-accent)', textAlign: 'center',
                  marginTop: 8,
                }}>
                  {error}
                </div>
              )}

              <div className="divider" />

              <p style={{ fontSize: 11, color: 'var(--color-text-muted)', textAlign: 'center', lineHeight: 1.7 }}>
                本系统仅限教会授权人员使用<br />
                如需开通权限请联系牧区长
              </p>
            </>
          )}
        </div>

        <p style={{ textAlign: 'center', fontSize: 12, color: 'var(--color-text-muted)', marginTop: 24 }}>
          © 2026 基督教米兰华人复兴教会
        </p>
      </div>
    </div>
  );
}
