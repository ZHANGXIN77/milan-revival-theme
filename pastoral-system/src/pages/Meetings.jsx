import { useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { useApp } from '../context/AppContext';

const EMPTY_FORM = { groupId: '', date: '', time: '', location: '', address: '', theme: '', notes: '' };

function fmt(dateStr) {
  if (!dateStr) return '';
  return new Date(dateStr + 'T00:00:00').toLocaleDateString('zh-CN', { month: 'long', day: 'numeric', weekday: 'short' });
}

export default function Meetings() {
  const { user } = useAuth();
  const { groups, meetings, getGroup, addMeeting, updateMeeting, deleteMeeting } = useApp();
  const isPastor = user?.role === 'pastor';

  const [filterGroup, setFilterGroup] = useState(isPastor ? '' : String(user?.groupId));
  const [showModal, setShowModal] = useState(false);
  const [editId, setEditId] = useState(null);
  const [form, setForm] = useState(EMPTY_FORM);
  const [error, setError] = useState('');

  const today = new Date().toISOString().split('T')[0];
  const sixMonthsLater = new Date();
  sixMonthsLater.setMonth(sixMonthsLater.getMonth() + 6);
  const maxDate = sixMonthsLater.toISOString().split('T')[0];

  // 只显示未来6个月，按日期排序
  const visible = meetings
    .filter(m => {
      const inRange = m.date >= today && m.date <= maxDate;
      const matchGroup = !filterGroup || m.groupId === Number(filterGroup);
      const canSee = isPastor || m.groupId === user?.groupId;
      return inRange && matchGroup && canSee;
    })
    .sort((a, b) => a.date.localeCompare(b.date) || a.time.localeCompare(b.time));

  // 按月份分组展示
  const byMonth = visible.reduce((acc, m) => {
    const key = m.date.slice(0, 7); // "2026-04"
    if (!acc[key]) acc[key] = [];
    acc[key].push(m);
    return acc;
  }, {});

  const openAdd = () => {
    setEditId(null);
    setForm({ ...EMPTY_FORM, groupId: isPastor ? '' : String(user?.groupId) });
    setError('');
    setShowModal(true);
  };

  const openEdit = (m) => {
    setEditId(m.id);
    setForm({ groupId: String(m.groupId), date: m.date, time: m.time, location: m.location, address: m.address || '', theme: m.theme, notes: m.notes || '' });
    setError('');
    setShowModal(true);
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!form.groupId) { setError('请选择小组'); return; }
    if (!form.date) { setError('请选择日期'); return; }
    if (!form.theme.trim()) { setError('请填写聚会主题'); return; }
    const data = { ...form, groupId: Number(form.groupId) };
    if (editId) updateMeeting(editId, data);
    else addMeeting(data);
    setShowModal(false);
  };

  const handleDelete = (id) => {
    if (window.confirm('确定要删除这次聚会安排吗？')) deleteMeeting(id);
  };

  const set = (k) => (e) => { setForm(f => ({ ...f, [k]: e.target.value })); setError(''); };

  const monthLabel = (key) => {
    const [y, m] = key.split('-');
    return `${y} 年 ${Number(m)} 月`;
  };

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <h1 className="page-title">聚会安排</h1>
          <p className="page-subtitle">未来 6 个月共 {visible.length} 次聚会</p>
        </div>
        <button className="btn btn-primary" onClick={openAdd}>+ 新增聚会</button>
      </div>

      {/* 小组筛选（牧区长可见） */}
      {isPastor && (
        <div style={{ marginBottom: 20 }}>
          <select className="form-select" value={filterGroup} onChange={e => setFilterGroup(e.target.value)} style={{ width: 200 }}>
            <option value="">所有小组</option>
            {groups.map(g => <option key={g.id} value={g.id}>{g.name}</option>)}
          </select>
        </div>
      )}

      {visible.length === 0 ? (
        <div className="empty-state"><p>未来 6 个月暂无聚会安排</p></div>
      ) : (
        Object.entries(byMonth).map(([month, items]) => (
          <div key={month} style={{ marginBottom: 28 }}>
            <div style={{ fontSize: 13, fontWeight: 600, color: 'var(--color-text-muted)', marginBottom: 12, letterSpacing: '0.05em' }}>
              {monthLabel(month)}
            </div>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
              {items.map(m => {
                const group = getGroup(m.groupId);
                const isNext = m.date === visible[0]?.date && m.id === visible[0]?.id;
                return (
                  <div key={m.id} className="card" style={{
                    padding: '16px 20px',
                    borderLeft: isNext ? '3px solid var(--color-accent)' : '3px solid transparent',
                  }}>
                    <div style={{ display: 'flex', gap: 16, alignItems: 'flex-start' }}>
                      {/* 日期块 */}
                      <div style={{ textAlign: 'center', minWidth: 44, flexShrink: 0 }}>
                        <div style={{ fontSize: 22, fontWeight: 700, color: isNext ? 'var(--color-accent)' : 'var(--color-text-primary)', lineHeight: 1 }}>
                          {m.date.slice(8)}
                        </div>
                        <div style={{ fontSize: 11, color: 'var(--color-text-muted)', marginTop: 2 }}>
                          {new Date(m.date + 'T00:00:00').toLocaleDateString('zh-CN', { weekday: 'short' })}
                        </div>
                      </div>

                      <div style={{ width: 1, background: 'var(--color-border)', alignSelf: 'stretch', margin: '2px 4px' }} />

                      {/* 内容 */}
                      <div style={{ flex: 1, minWidth: 0 }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: 8, flexWrap: 'wrap', marginBottom: 6 }}>
                          <span style={{ fontSize: 15, fontWeight: 600 }}>{m.theme}</span>
                          {isNext && <span style={{ fontSize: 11, padding: '1px 8px', borderRadius: 10, background: 'var(--color-accent-dim)', color: 'var(--color-accent)' }}>最近一次</span>}
                        </div>
                        <div style={{ display: 'flex', gap: 16, flexWrap: 'wrap' }}>
                          <span style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>{m.time}</span>
                          <span style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>{m.location || '待定'}</span>
                          {m.address && (
                            <a
                              href={`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(m.address)}`}
                              target="_blank"
                              rel="noopener noreferrer"
                              style={{ fontSize: 12, color: 'var(--color-accent)', textDecoration: 'underline', textUnderlineOffset: 3 }}
                            >{m.address}</a>
                          )}
                          {isPastor && group && <span style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>{group.name}</span>}
                        </div>
                        {m.notes && <p style={{ fontSize: 12, color: 'var(--color-text-secondary)', marginTop: 6, fontStyle: 'italic' }}>{m.notes}</p>}
                      </div>

                      {/* 操作按钮 */}
                      <div style={{ display: 'flex', gap: 6, flexShrink: 0 }}>
                        <button className="btn btn-ghost btn-sm" onClick={() => openEdit(m)} style={{ padding: '4px 10px', fontSize: 12 }}>编辑</button>
                        <button className="btn btn-ghost btn-sm" onClick={() => handleDelete(m.id)} style={{ padding: '4px 10px', fontSize: 12, color: 'var(--color-danger)' }}>删除</button>
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        ))
      )}

      {/* 新增/编辑弹窗 */}
      {showModal && (
        <div className="modal-overlay" onClick={e => e.target === e.currentTarget && setShowModal(false)}>
          <div className="modal">
            <div className="modal-header">
              <h2 className="modal-title">{editId ? '编辑聚会' : '新增聚会'}</h2>
              <button className="btn btn-ghost btn-sm" onClick={() => setShowModal(false)}>关闭</button>
            </div>
            <form onSubmit={handleSubmit}>
              <div style={{ display: 'grid', gap: 14 }}>
                {isPastor && (
                  <div className="form-group">
                    <label className="form-label">所属小组 *</label>
                    <select className="form-select" value={form.groupId} onChange={set('groupId')}>
                      <option value="">请选择</option>
                      {groups.map(g => <option key={g.id} value={g.id}>{g.name}</option>)}
                    </select>
                  </div>
                )}
                <div className="form-group">
                  <label className="form-label">聚会主题 *</label>
                  <input className="form-input" placeholder="例：马可福音第1章研读" value={form.theme} onChange={set('theme')} />
                </div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                  <div className="form-group">
                    <label className="form-label">日期 *</label>
                    <input type="date" className="form-input" value={form.date} min={today} max={maxDate} onChange={set('date')} />
                  </div>
                  <div className="form-group">
                    <label className="form-label">时间</label>
                    <input type="time" className="form-input" value={form.time} onChange={set('time')} />
                  </div>
                </div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                  <div className="form-group">
                    <label className="form-label">地点名称</label>
                    <input className="form-input" placeholder="例：王建国家" value={form.location} onChange={set('location')} />
                  </div>
                  <div className="form-group">
                    <label className="form-label">地址（可跳转地图）</label>
                    <input className="form-input" placeholder="例：Via Torino 12, Milano" value={form.address} onChange={set('address')} />
                  </div>
                </div>
                <div className="form-group">
                  <label className="form-label">备注</label>
                  <textarea className="form-textarea" placeholder="请带圣经、会后一起吃饭…" value={form.notes} onChange={set('notes')} rows={2} />
                </div>
                {error && <p style={{ fontSize: 13, color: 'var(--color-danger)' }}>{error}</p>}
                <div style={{ display: 'flex', gap: 8, justifyContent: 'flex-end' }}>
                  <button type="button" className="btn btn-outline" onClick={() => setShowModal(false)}>取消</button>
                  <button type="submit" className="btn btn-primary">{editId ? '保存修改' : '新增'}</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
