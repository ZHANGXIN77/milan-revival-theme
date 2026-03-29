import { useState, useEffect, useCallback } from 'react';
import { useAuth } from '../context/AuthContext';
import { API_BASE } from '../config';
import Avatar from '../components/Avatar';

const ROLE_LABELS = { pastor: '牧区长', leader: '小组长', youth: '青少年' };

export default function UserManagement() {
  const { credential } = useAuth();
  const [users, setUsers] = useState({});
  const [pending, setPending] = useState({});
  const [loading, setLoading] = useState(true);

  const headers = { 'Content-Type': 'application/json', 'X-Google-Credential': credential };

  const fetchData = useCallback(async () => {
    try {
      const [uRes, pRes] = await Promise.all([
        fetch(`${API_BASE}/users`),
        fetch(`${API_BASE}/pending`, { headers: { 'X-Google-Credential': credential } }),
      ]);
      setUsers(await uRes.json());
      if (pRes.ok) setPending(await pRes.json());
    } catch { /* ignore */ }
    setLoading(false);
  }, [credential]);

  useEffect(() => { fetchData(); }, [fetchData]);

  const approveUser = async (email, role) => {
    await fetch(`${API_BASE}/approve`, {
      method: 'POST', headers,
      body: JSON.stringify({ email, role, action: 'approve' }),
    });
    fetchData();
  };

  const rejectUser = async (email) => {
    await fetch(`${API_BASE}/approve`, {
      method: 'POST', headers,
      body: JSON.stringify({ email, action: 'reject' }),
    });
    fetchData();
  };

  const changeRole = async (email, role) => {
    const u = users[email];
    await fetch(`${API_BASE}/users`, {
      method: 'POST', headers,
      body: JSON.stringify({ email, role, name: u?.name || '', action: 'update' }),
    });
    fetchData();
  };

  const removeUser = async (email) => {
    if (!confirm(`确定要移除 ${email} 的权限吗？`)) return;
    await fetch(`${API_BASE}/users`, {
      method: 'POST', headers,
      body: JSON.stringify({ email, action: 'remove' }),
    });
    fetchData();
  };

  const pendingEntries = Object.entries(pending);
  const userEntries = Object.entries(users);

  if (loading) {
    return <div style={{ display: 'flex', justifyContent: 'center', padding: 60 }}><span className="spinner" /></div>;
  }

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <h1 className="page-title">用户管理</h1>
          <p className="page-subtitle">管理系统授权用户和审批新用户</p>
        </div>
      </div>

      {/* 待审批 */}
      {pendingEntries.length > 0 && (
        <div style={{ marginBottom: 28 }}>
          <h2 style={{ fontSize: 16, fontWeight: 600, marginBottom: 12, color: 'var(--color-accent)' }}>
            待审批（{pendingEntries.length}）
          </h2>
          <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
            {pendingEntries.map(([email, info]) => (
              <div key={email} className="card" style={{ display: 'flex', alignItems: 'center', gap: 14, padding: '14px 18px' }}>
                <Avatar name={info.name} src={info.avatar} size={36} />
                <div style={{ flex: 1, minWidth: 0 }}>
                  <div style={{ fontSize: 14, fontWeight: 600 }}>{info.name || '未知'}</div>
                  <div style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>{email}</div>
                </div>
                <div style={{ display: 'flex', gap: 6, flexShrink: 0 }}>
                  <button className="btn btn-sm btn-primary" onClick={() => approveUser(email, 'youth')}>
                    批准为青少年
                  </button>
                  <button className="btn btn-sm btn-outline" onClick={() => approveUser(email, 'leader')}>
                    批准为小组长
                  </button>
                  <button className="btn btn-sm btn-danger" onClick={() => rejectUser(email)}>
                    拒绝
                  </button>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* 已授权用户 */}
      <div>
        <h2 style={{ fontSize: 16, fontWeight: 600, marginBottom: 12 }}>
          已授权用户（{userEntries.length}）
        </h2>
        <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
          {userEntries.map(([email, info]) => (
            <div key={email} className="card" style={{ display: 'flex', alignItems: 'center', gap: 14, padding: '14px 18px' }}>
              <Avatar name={info.name || email} src={info.avatar} size={36} />
              <div style={{ flex: 1, minWidth: 0 }}>
                <div style={{ fontSize: 14, fontWeight: 600 }}>{info.name || email}</div>
                <div style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>{email}</div>
              </div>
              <div style={{ display: 'flex', alignItems: 'center', gap: 8, flexShrink: 0 }}>
                {info.role === 'pastor' ? (
                  <span style={{
                    fontSize: 12, padding: '4px 12px', borderRadius: 'var(--radius-sm)',
                    background: 'var(--color-accent-dim)', color: 'var(--color-accent)', fontWeight: 600,
                  }}>
                    {ROLE_LABELS.pastor}
                  </span>
                ) : (
                  <>
                    <select
                      className="form-select"
                      value={info.role}
                      onChange={(e) => changeRole(email, e.target.value)}
                      style={{ width: 110, padding: '5px 28px 5px 10px', fontSize: 13 }}
                    >
                      <option value="youth">青少年</option>
                      <option value="leader">小组长</option>
                    </select>
                    <button className="btn btn-sm btn-danger" onClick={() => removeUser(email)}>
                      移除
                    </button>
                  </>
                )}
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
