import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useApp } from '../context/AppContext';
import { useSuccessMessage } from '../hooks/useSuccessMessage';
import { STAGES } from '../data/mockData';
import Avatar from '../components/Avatar';
import StageTag from '../components/StageTag';

export default function GroupManagement() {
  const { user } = useAuth();
  const { groups, members, getMembersByGroup, addGroup, updateGroup, updateMember } = useApp();
  const isPastor = user?.role === 'pastor';
  const navigate = useNavigate();
  const [showModal, setShowModal] = useState(false);
  const [form, setForm] = useState({ name: '', description: '' });
  const [error, setError] = useState('');
  const [success, setSuccess] = useSuccessMessage();

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.name.trim()) { setError('请填写小组名称'); return; }
    const groupName = form.name.trim();
    setForm({ name: '', description: '' });
    setError('');
    setShowModal(false);
    await addGroup({ name: groupName, description: form.description.trim() });
    setSuccess(`小组「${groupName}」已创建`);
  };

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <h1 className="page-title">小组管理</h1>
          <p className="page-subtitle">共 {groups.length} 个小组，{members.length} 位成员</p>
        </div>
        <button className="btn btn-primary" onClick={() => setShowModal(true)}>+ 新增小组</button>
      </div>

      {success && (
        <div style={{ marginBottom: 16, padding: '10px 16px', borderRadius: 'var(--radius-sm)', background: 'rgba(90,171,126,0.12)', border: '1px solid var(--color-success)', color: 'var(--color-success)', fontSize: 13 }}>
          {success}
        </div>
      )}

      {/* Stage legend */}
      <div style={{ display: 'flex', gap: 8, marginBottom: 24, flexWrap: 'wrap' }}>
        {STAGES.map((s, i) => (
          <span key={i} style={{ fontSize: 11, padding: '2px 10px', borderRadius: 20, background: s.bgColor, color: s.color, border: `1px solid ${s.color}40` }}>
            {s.name}
          </span>
        ))}
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: 16 }}>
        {groups.map(group => {
          const groupMembers = getMembersByGroup(group.id);
          const leader = members.find(m => m.id === group.leaderId);
          const activeCount = groupMembers.filter(m => m.status === '活跃').length;
          return (
            <div key={group.id} className="card">
              {/* Group header */}
              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 16 }}>
                <div>
                  <div style={{ fontSize: 16, fontWeight: 700, marginBottom: 4 }}>{group.name}</div>
                  <div style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>{group.description}</div>
                </div>
                <span style={{ fontSize: 20, fontWeight: 700, color: 'var(--color-accent)' }}>{activeCount}</span>
              </div>

              {/* Leader */}
              <div style={{ marginBottom: 12, padding: '8px 10px', background: 'var(--color-accent-dim)', borderRadius: 'var(--radius-sm)' }}>
                {isPastor ? (
                  <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                    <div style={{ fontSize: 11, color: 'var(--color-text-muted)', flexShrink: 0 }}>小组长</div>
                    <select
                      className="form-select"
                      value={group.leaderId ?? ''}
                      onChange={async e => {
                        const newId = e.target.value ? Number(e.target.value) : null;
                        if (group.leaderId) await updateMember(group.leaderId, { isGroupLeader: false });
                        if (newId) await updateMember(newId, { isGroupLeader: true });
                        await updateGroup(group.id, { leaderId: newId });
                      }}
                      style={{ fontSize: 12, padding: '3px 8px', height: 28 }}>
                      <option value="">（未指定）</option>
                      {groupMembers.map(m => <option key={m.id} value={m.id}>{m.name}</option>)}
                    </select>
                  </div>
                ) : (
                  leader && (
                    <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                      <Avatar name={leader.name} size={28} />
                      <div>
                        <div style={{ fontSize: 12, color: 'var(--color-accent)', fontWeight: 600 }}>{leader.name}</div>
                        <div style={{ fontSize: 10, color: 'var(--color-text-muted)' }}>小组长</div>
                      </div>
                    </div>
                  )
                )}
              </div>

              {/* Members */}
              <div style={{ display: 'flex', flexDirection: 'column', gap: 4 }}>
                {groupMembers.map(m => (
                  <div key={m.id} onClick={() => navigate(`/members/${m.id}`)}
                    style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '6px 8px', borderRadius: 'var(--radius-sm)', cursor: 'pointer', transition: 'background 0.2s' }}
                    onMouseEnter={e => e.currentTarget.style.background = 'var(--color-bg-secondary)'}
                    onMouseLeave={e => e.currentTarget.style.background = 'transparent'}>
                    <Avatar name={m.name} size={28} />
                    <span style={{ flex: 1, fontSize: 13 }}>{m.name}</span>
                    <StageTag stage={m.stage} />
                  </div>
                ))}
              </div>
            </div>
          );
        })}
      </div>
      {/* Add group modal */}
      {showModal && (
        <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 1000 }}
          onClick={(e) => { if (e.target === e.currentTarget) setShowModal(false); }}>
          <div className="card" style={{ width: 400, maxWidth: '90vw', padding: 28 }}>
            <h2 style={{ fontSize: 16, fontWeight: 700, marginBottom: 20 }}>新增小组</h2>
            <form onSubmit={handleSubmit}>
              <div style={{ marginBottom: 14 }}>
                <label style={{ fontSize: 13, color: 'var(--color-text-secondary)', display: 'block', marginBottom: 6 }}>小组名称 *</label>
                <input
                  className="form-input"
                  placeholder="例：F组（橄榄枝）"
                  value={form.name}
                  onChange={e => setForm(f => ({ ...f, name: e.target.value }))}
                  autoFocus
                />
              </div>
              <div style={{ marginBottom: 20 }}>
                <label style={{ fontSize: 13, color: 'var(--color-text-secondary)', display: 'block', marginBottom: 6 }}>描述</label>
                <input
                  className="form-input"
                  placeholder="例：大学生小组"
                  value={form.description}
                  onChange={e => setForm(f => ({ ...f, description: e.target.value }))}
                />
              </div>
              {error && <p style={{ fontSize: 12, color: 'var(--color-danger)', marginBottom: 12 }}>{error}</p>}
              <div style={{ display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
                <button type="button" className="btn btn-outline" onClick={() => { setShowModal(false); setError(''); }}>取消</button>
                <button type="submit" className="btn btn-primary">创建</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
