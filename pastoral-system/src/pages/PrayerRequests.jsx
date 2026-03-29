import { useState, useEffect } from 'react';
import { useAuth } from '../context/AuthContext';
import { useApp } from '../context/AppContext';

const STATUS_COLORS = { '进行中': 'var(--color-accent)', '已蒙应允': 'var(--color-success)', '已结束': 'var(--color-text-muted)' };
const VISIBILITY_LABELS = { all: '所有人可见', leaders: '同工可见', pastor: '牧区长专属' };

export default function PrayerRequests() {
  const { user } = useAuth();
  const { prayers, members, groups, addPrayer, updatePrayer } = useApp();
  const [showForm, setShowForm] = useState(false);
  const [newPrayer, setNewPrayer] = useState({ title: '', content: '', status: '进行中', visibility: 'all' });
  const [filter, setFilter] = useState('');
  const [filterGroup, setFilterGroup] = useState('');
  const [filterMine, setFilterMine] = useState(false);
  const [success, setSuccess] = useState(false);

  useEffect(() => {
    if (!success) return;
    const t = setTimeout(() => setSuccess(false), 3000);
    return () => clearTimeout(t);
  }, [success]);

  const isPastor = user?.role === 'pastor';
  const isLeader = user?.role === 'leader';
  const isYouth = user?.role === 'youth';

  const visiblePrayers = prayers.filter(p => {
    if (isPastor) return true;
    if (isLeader) return p.visibility !== 'pastor';
    return p.visibility === 'all';
  }).filter(p => !filter || p.status === filter)
    .filter(p => {
      if (!isPastor || !filterGroup) return true;
      const author = members.find(m => m.id === p.authorId);
      return author?.groupId === Number(filterGroup);
    })
    .filter(p => !filterMine || p.authorId === (user?.id ?? 0));

  const getMemberName = (id) => members.find(m => m.id === id)?.name || '未知';

  const handleSubmit = () => {
    if (!newPrayer.title.trim()) return;
    addPrayer({ ...newPrayer, title: newPrayer.title.trim(), content: newPrayer.content.trim(), authorId: user?.id ?? 0 });
    setNewPrayer({ title: '', content: '', status: '进行中', visibility: 'all' });
    setShowForm(false);
    setSuccess(true);
  };

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <h1 className="page-title">代祷事项</h1>
          <p className="page-subtitle">共 {visiblePrayers.length} 项代祷</p>
        </div>
        <button className="btn btn-primary" onClick={() => setShowForm(!showForm)}>
          {showForm ? '取消' : '+ 新增代祷'}
        </button>
      </div>

      {success && (
        <div style={{ marginBottom: 16, padding: '10px 16px', borderRadius: 'var(--radius-sm)', background: 'rgba(90,171,126,0.12)', border: '1px solid var(--color-success)', color: 'var(--color-success)', fontSize: 13 }}>
          代祷事项已提交
        </div>
      )}

      {/* Form */}
      {showForm && (
        <div className="card" style={{ marginBottom: 20, border: '1px solid var(--color-accent-dim)' }}>
          <div style={{ fontSize: 14, fontWeight: 600, marginBottom: 14 }}>新增代祷事项</div>
          <div style={{ display: 'grid', gap: 12 }}>
            <div className="form-group">
              <label className="form-label">标题</label>
              <input className="form-input" placeholder="代祷事项标题…" value={newPrayer.title}
                onChange={e => setNewPrayer(p => ({...p, title: e.target.value}))} />
            </div>
            <div className="form-group">
              <label className="form-label">内容详情</label>
              <textarea className="form-textarea" placeholder="详细描述代祷内容…" value={newPrayer.content}
                onChange={e => setNewPrayer(p => ({...p, content: e.target.value}))} />
            </div>
            {!isYouth && (
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                <div className="form-group">
                  <label className="form-label">可见范围</label>
                  <select className="form-select" value={newPrayer.visibility}
                    onChange={e => setNewPrayer(p => ({...p, visibility: e.target.value}))}>
                    <option value="all">所有人（含青少年）</option>
                    <option value="leaders">仅同工</option>
                    {isPastor && <option value="pastor">仅牧区长</option>}
                  </select>
                </div>
              </div>
            )}
            <div style={{ display: 'flex', gap: 8 }}>
              <button className="btn btn-primary btn-sm" onClick={handleSubmit}>提交</button>
              <button className="btn btn-ghost btn-sm" onClick={() => setShowForm(false)}>取消</button>
            </div>
          </div>
        </div>
      )}

      {/* Filter */}
      <div style={{ display: 'flex', gap: 8, marginBottom: 16, flexWrap: 'wrap', alignItems: 'center' }}>
        {['', '进行中', '已蒙应允', '已结束'].map(f => (
          <button key={f} onClick={() => setFilter(f)}
            className={`btn btn-sm ${filter === f ? 'btn-primary' : 'btn-outline'}`}>
            {f || '全部'}
          </button>
        ))}
        <button onClick={() => setFilterMine(v => !v)}
          className={`btn btn-sm ${filterMine ? 'btn-primary' : 'btn-outline'}`}>
          我提交的
        </button>
        {isPastor && (
          <select className="form-select" value={filterGroup} onChange={e => setFilterGroup(e.target.value)}
            style={{ width: 150, marginLeft: 8 }}>
            <option value="">所有小组</option>
            {groups.map(g => <option key={g.id} value={g.id}>{g.name}</option>)}
          </select>
        )}
      </div>

      {/* Prayer list */}
      {visiblePrayers.length === 0 ? (
        <div className="empty-state"><p>暂无代祷事项</p></div>
      ) : (
        <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
          {visiblePrayers.map(prayer => (
            <div key={prayer.id} className="card">
              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: 12, flexWrap: 'wrap', marginBottom: 8 }}>
                <div style={{ flex: 1 }}>
                  <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 4, flexWrap: 'wrap' }}>
                    <h3 style={{ fontSize: 15, fontWeight: 600 }}>{prayer.title}</h3>
                    <span style={{ fontSize: 11, padding: '1px 8px', borderRadius: 10,
                      background: `${STATUS_COLORS[prayer.status]}20`,
                      color: STATUS_COLORS[prayer.status] }}>
                      {prayer.status}
                    </span>
                    {isPastor && (
                      <span style={{ fontSize: 10, padding: '1px 8px', borderRadius: 10, background: 'var(--color-bg-secondary)', color: 'var(--color-text-muted)' }}>
                        {VISIBILITY_LABELS[prayer.visibility]}
                      </span>
                    )}
                  </div>
                  <div style={{ fontSize: 11, color: 'var(--color-text-muted)' }}>
                    {getMemberName(prayer.authorId)} · {new Date(prayer.date).toLocaleDateString('zh-CN')}
                  </div>
                </div>
                {(isPastor || isLeader) && (
                  <select className="form-select" value={prayer.status}
                    onChange={e => updatePrayer(prayer.id, { status: e.target.value })}
                    style={{ width: 120 }}>
                    <option>进行中</option>
                    <option>已蒙应允</option>
                    <option>已结束</option>
                  </select>
                )}
              </div>
              {prayer.content && (
                <p style={{ fontSize: 13, color: 'var(--color-text-secondary)', lineHeight: 1.7, marginTop: 8, paddingTop: 8, borderTop: '1px solid var(--color-border)' }}>
                  {prayer.content}
                </p>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
