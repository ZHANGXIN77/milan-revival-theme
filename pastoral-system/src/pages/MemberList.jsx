import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useApp } from '../context/AppContext';
import { STAGES } from '../data/mockData';
import StageTag from '../components/StageTag';
import Avatar from '../components/Avatar';
import { useSuccessMessage } from '../hooks/useSuccessMessage';

const NOTE_TYPES = ['关怀谈话', '出席情况', '代祷事项', '异常状况'];
const EMPTY_FORM = { name: '', englishName: '', gender: '男', dob: '', school: '', phone: '', email: '', parentContact: '', groupId: '', stage: 0, status: '活跃', gdprConsent: false, baptized: false, baptizeDate: '', joinDate: '' };

export default function MemberList() {
  const { user } = useAuth();
  const { members, groups, getMembersByGroup, addMember, addNote } = useApp();
  const navigate = useNavigate();
  const [search, setSearch] = useState('');
  const [filterStage, setFilterStage] = useState('');
  const [filterGroup, setFilterGroup] = useState('');
  const [filterStatus, setFilterStatus] = useState('活跃');
  const [sortBy, setSortBy] = useState('joinDate_desc');
  const [showAddModal, setShowAddModal] = useState(false);
  const [form, setForm] = useState(EMPTY_FORM);
  const [formError, setFormError] = useState('');
  const [quickNote, setQuickNote] = useState(null); // { id, name }
  const [quickNoteForm, setQuickNoteForm] = useState({ type: '关怀谈话', content: '' });
  const [revealedPhones, setRevealedPhones] = useState(new Set());
  const [successMsg, setSuccessMsg] = useSuccessMessage();

  const togglePhone = (e, memberId) => {
    e.stopPropagation();
    setRevealedPhones(prev => {
      const next = new Set(prev);
      if (next.has(memberId)) next.delete(memberId); else next.add(memberId);
      return next;
    });
  };

  const isPastor = user?.role === 'pastor';
  const canAddNote = isPastor || user?.role === 'leader';
  const baseList = isPastor ? members : getMembersByGroup(user?.groupId);

  const filtered = baseList.filter(m => {
    const matchSearch = !search || m.name.includes(search) || m.englishName?.toLowerCase().includes(search.toLowerCase()) || m.school?.includes(search);
    const matchStage = filterStage === '' || m.stage === Number(filterStage);
    const matchGroup = filterGroup === '' || m.groupId === Number(filterGroup);
    const matchStatus = !filterStatus || m.status === filterStatus;
    return matchSearch && matchStage && matchGroup && matchStatus;
  }).sort((a, b) => {
    switch (sortBy) {
      case 'name_asc': return a.name.localeCompare(b.name, 'zh');
      case 'name_desc': return b.name.localeCompare(a.name, 'zh');
      case 'stage_asc': return a.stage - b.stage;
      case 'stage_desc': return b.stage - a.stage;
      case 'joinDate_asc': return (a.joinDate || '').localeCompare(b.joinDate || '');
      case 'joinDate_desc': return (b.joinDate || '').localeCompare(a.joinDate || '');
      default: return 0;
    }
  });

  const getGroup = (id) => groups.find(g => g.id === id);

  const handleAddSubmit = async () => {
    if (!form.name.trim()) { setFormError('请填写姓名'); return; }
    if (!form.groupId) { setFormError('请选择所属小组'); return; }
    try {
      await addMember({
        ...form,
        name: form.name.trim(),
        englishName: form.englishName.trim(),
        school: form.school.trim(),
        phone: form.phone.trim(),
        email: form.email.trim(),
        parentContact: form.parentContact.trim(),
        groupId: Number(form.groupId),
        stage: Number(form.stage),
        baptizeDate: form.baptized ? form.baptizeDate || null : null,
      });
      setForm(EMPTY_FORM);
      setFormError('');
      setShowAddModal(false);
      setSuccessMsg(`成员「${form.name.trim()}」已成功添加`);
    } catch (err) {
      setFormError('添加失败：' + err.message);
    }
  };

  const handleQuickNoteSubmit = async () => {
    if (!quickNoteForm.content.trim()) return;
    await addNote({ memberId: quickNote.id, authorId: user?.id || 0, type: quickNoteForm.type, content: quickNoteForm.content.trim() });
    setQuickNote(null);
    setQuickNoteForm({ type: '关怀谈话', content: '' });
  };

  const set = (field) => (e) => {
    const val = e.target.type === 'checkbox' ? e.target.checked : e.target.value;
    setForm(prev => ({ ...prev, [field]: val }));
    setFormError('');
  };

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <h1 className="page-title">{isPastor ? '人员管理' : '我的组员'}</h1>
          <p className="page-subtitle">共 {filtered.length} 人 / 在册 {baseList.length} 人</p>
        </div>
        {isPastor && <button className="btn btn-primary" onClick={() => { setShowAddModal(true); setForm(EMPTY_FORM); setFormError(''); }}>+ 新增成员</button>}
      </div>

      {successMsg && (
        <div style={{ padding: '10px 16px', borderRadius: 8, background: 'var(--color-success)', color: '#fff', fontSize: 13, marginBottom: 16 }}>
          {successMsg}
        </div>
      )}

      {/* Filters */}
      <div style={{ display: 'flex', gap: 10, marginBottom: 20, flexWrap: 'wrap' }}>
        <input className="form-input" placeholder="搜索姓名或学校…" value={search} onChange={e => setSearch(e.target.value)}
          style={{ flex: 1, minWidth: 180, maxWidth: 280 }} />
        <select className="form-select" value={filterStage} onChange={e => setFilterStage(e.target.value)} style={{ width: 130 }}>
          <option value="">所有阶段</option>
          {STAGES.map((s, i) => <option key={i} value={i}>{s.name}</option>)}
        </select>
        {isPastor && (
          <select className="form-select" value={filterGroup} onChange={e => setFilterGroup(e.target.value)} style={{ width: 160 }}>
            <option value="">所有小组</option>
            {groups.map(g => <option key={g.id} value={g.id}>{g.name}</option>)}
          </select>
        )}
        <select className="form-select" value={filterStatus} onChange={e => setFilterStatus(e.target.value)} style={{ width: 110 }}>
          <option value="">所有状态</option>
          <option value="活跃">活跃</option>
          <option value="不活跃">不活跃</option>
          <option value="已离开">已离开</option>
        </select>
        <select className="form-select" value={sortBy} onChange={e => setSortBy(e.target.value)} style={{ width: 130 }}>
          <option value="joinDate_desc">最新加入</option>
          <option value="joinDate_asc">最早加入</option>
          <option value="name_asc">姓名 A→Z</option>
          <option value="name_desc">姓名 Z→A</option>
          <option value="stage_desc">阶段（高→低）</option>
          <option value="stage_asc">阶段（低→高）</option>
        </select>
      </div>

      {/* Member grid */}
      {filtered.length === 0 ? (
        <div className="empty-state"><p>没有符合条件的成员</p></div>
      ) : (
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(280px, 1fr))', gap: 12 }}>
          {filtered.map(m => {
            const group = getGroup(m.groupId);
            const age = m.dob ? Math.floor((new Date() - new Date(m.dob)) / (365.25 * 86400000)) : null;
            return (
              <div key={m.id} className="card" onClick={() => navigate(`/members/${m.id}`)}
                style={{ cursor: 'pointer', display: 'flex', flexDirection: 'column', gap: 12 }}>
                {/* Top row */}
                <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                  <Avatar name={m.name} size={44} src={m.avatar} />
                  <div style={{ flex: 1, minWidth: 0 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                      <span style={{ fontWeight: 600, fontSize: 15 }}>{m.name}</span>
                      {m.englishName && <span style={{ fontSize: 13, color: 'var(--color-text-muted)' }}>{m.englishName}</span>}
                      {m.isGroupLeader && <span style={{ fontSize: 10, padding: '1px 6px', borderRadius: 10, background: 'var(--color-accent-dim)', color: 'var(--color-accent)' }}>组长</span>}
                    </div>
                    <div style={{ fontSize: 12, color: 'var(--color-text-muted)', marginTop: 2 }}>
                      {m.gender} · {age ? `${age}岁` : '—'} · {m.school || '—'}
                    </div>
                  </div>
                  <StageTag stage={m.stage} />
                </div>

                {/* Info */}
                <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap', alignItems: 'center' }}>
                  {group && <span style={{ fontSize: 11, color: 'var(--color-text-muted)', padding: '2px 8px', background: 'var(--color-bg-secondary)', borderRadius: 10 }}>
                    {group.name}
                  </span>}
                  <span style={{ fontSize: 11, color: 'var(--color-text-muted)', padding: '2px 8px', background: 'var(--color-bg-secondary)', borderRadius: 10 }}>
                    {m.baptized ? '已受洗' : '未受洗'}
                  </span>
                  {!m.gdprConsent && <span style={{ fontSize: 11, color: 'var(--color-danger)', padding: '2px 8px', background: 'rgba(224,85,85,0.1)', borderRadius: 10 }}>
                    待签GDPR
                  </span>}
                  {m.phone && (
                    <span onClick={e => togglePhone(e, m.id)}
                      style={{ fontSize: 11, padding: '2px 8px', background: 'var(--color-bg-secondary)', borderRadius: 10, cursor: 'pointer',
                        color: revealedPhones.has(m.id) ? 'var(--color-text-secondary)' : 'var(--color-accent)' }}>
                      {revealedPhones.has(m.id) ? m.phone : '查看电话'}
                    </span>
                  )}
                </div>
                {canAddNote && (
                  <div style={{ display: 'flex', justifyContent: 'flex-end', marginTop: 4 }}>
                    <button
                      className="btn btn-ghost btn-sm"
                      onClick={e => { e.stopPropagation(); setQuickNote({ id: m.id, name: m.name }); setQuickNoteForm({ type: '关怀谈话', content: '' }); }}
                      style={{ fontSize: 11, padding: '3px 10px' }}>
                      + 快速记录
                    </button>
                  </div>
                )}
              </div>
            );
          })}
        </div>
      )}

      {/* Quick Note Modal */}
      {quickNote && (
        <div className="modal-overlay" onClick={e => e.target === e.currentTarget && setQuickNote(null)}>
          <div className="modal" style={{ maxWidth: 420 }}>
            <div className="modal-header">
              <h2 className="modal-title">快速记录 — {quickNote.name}</h2>
              <button className="btn btn-ghost btn-sm" onClick={() => setQuickNote(null)}>关闭</button>
            </div>
            <div style={{ display: 'grid', gap: 14 }}>
              <div className="form-group">
                <label className="form-label">记录类型</label>
                <select className="form-select" value={quickNoteForm.type}
                  onChange={e => setQuickNoteForm(f => ({ ...f, type: e.target.value }))}>
                  {NOTE_TYPES.map(t => <option key={t}>{t}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">内容</label>
                <textarea className="form-textarea" rows={4} placeholder="记录关怀内容、近况、祷告事项等…"
                  value={quickNoteForm.content}
                  onChange={e => setQuickNoteForm(f => ({ ...f, content: e.target.value }))} />
              </div>
              <div style={{ display: 'flex', gap: 8, justifyContent: 'flex-end' }}>
                <button className="btn btn-ghost" onClick={() => setQuickNote(null)}>取消</button>
                <button className="btn btn-primary" onClick={handleQuickNoteSubmit}>保存记录</button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Add Member Modal */}
      {showAddModal && (
        <div className="modal-overlay" onClick={e => e.target === e.currentTarget && setShowAddModal(false)}>
          <div className="modal" style={{ maxWidth: 540 }}>
            <div className="modal-header">
              <h2 className="modal-title">新增成员档案</h2>
              <button className="btn btn-ghost btn-sm" onClick={() => setShowAddModal(false)}>关闭</button>
            </div>
            <div style={{ display: 'grid', gap: 14 }}>
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                <div className="form-group">
                  <label className="form-label">姓名 *</label>
                  <input className="form-input" placeholder="请输入姓名" value={form.name} onChange={set('name')} />
                </div>
                <div className="form-group">
                  <label className="form-label">英文名</label>
                  <input className="form-input" placeholder="English name" value={form.englishName} onChange={set('englishName')} />
                </div>
              </div>
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                <div className="form-group">
                  <label className="form-label">性别</label>
                  <select className="form-select" value={form.gender} onChange={set('gender')}>
                    <option value="男">男</option>
                    <option value="女">女</option>
                  </select>
                </div>
                <div className="form-group">
                  <label className="form-label">出生日期</label>
                  <input type="date" className="form-input" value={form.dob} onChange={set('dob')} />
                </div>
              </div>
              <div className="form-group">
                <label className="form-label">所属小组 *</label>
                <select className="form-select" value={form.groupId} onChange={set('groupId')}>
                  <option value="">请选择</option>
                  {groups.map(g => <option key={g.id} value={g.id}>{g.name}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">学校 / 年级</label>
                <input className="form-input" placeholder="例：米兰大学 大一" value={form.school} onChange={set('school')} />
              </div>
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                <div className="form-group">
                  <label className="form-label">手机号码</label>
                  <input className="form-input" placeholder="+39 333 ..." value={form.phone} onChange={set('phone')} />
                </div>
                <div className="form-group">
                  <label className="form-label">邮箱地址</label>
                  <input className="form-input" placeholder="example@gmail.com" value={form.email} onChange={set('email')} />
                </div>
              </div>
              <div className="form-group">
                <label className="form-label">家长联系方式</label>
                <input className="form-input" placeholder="家长姓名及手机号" value={form.parentContact} onChange={set('parentContact')} />
              </div>
              <div className="form-group">
                <label className="form-label">来教会时间</label>
                <input type="date" className="form-input" value={form.joinDate} onChange={set('joinDate')} />
              </div>
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                <div className="form-group">
                  <label className="form-label">牧养阶段</label>
                  <select className="form-select" value={form.stage} onChange={set('stage')}>
                    {STAGES.map((s, i) => <option key={i} value={i}>{s.name}</option>)}
                  </select>
                </div>
                <div className="form-group">
                  <label className="form-label">状态</label>
                  <select className="form-select" value={form.status} onChange={set('status')}>
                    <option value="活跃">活跃</option>
                    <option value="不活跃">不活跃</option>
                    <option value="已离开">已离开</option>
                  </select>
                </div>
              </div>
              <div style={{ display: 'flex', gap: 20, flexWrap: 'wrap' }}>
                <label style={{ display: 'flex', alignItems: 'center', gap: 8, fontSize: 13, cursor: 'pointer' }}>
                  <input type="checkbox" checked={form.gdprConsent} onChange={set('gdprConsent')} />
                  已取得 GDPR 同意
                </label>
                <label style={{ display: 'flex', alignItems: 'center', gap: 8, fontSize: 13, cursor: 'pointer' }}>
                  <input type="checkbox" checked={form.baptized} onChange={set('baptized')} />
                  已受洗
                </label>
              </div>
              {form.baptized && (
                <div className="form-group">
                  <label className="form-label">受洗日期</label>
                  <input type="date" className="form-input" value={form.baptizeDate} onChange={set('baptizeDate')} />
                </div>
              )}
              {formError && <div style={{ fontSize: 13, color: 'var(--color-danger)' }}>{formError}</div>}
              <div style={{ display: 'flex', gap: 8, justifyContent: 'flex-end', marginTop: 4 }}>
                <button className="btn btn-ghost" onClick={() => setShowAddModal(false)}>取消</button>
                <button className="btn btn-primary" onClick={handleAddSubmit}>保存档案</button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
