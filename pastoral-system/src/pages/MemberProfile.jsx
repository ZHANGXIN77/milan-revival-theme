import { useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useApp } from '../context/AppContext';
import { STAGES } from '../data/mockData';
import StageTag from '../components/StageTag';
import Avatar from '../components/Avatar';

const NOTE_TYPES = ['关怀谈话', '出席情况', '代祷事项', '异常状况'];

export default function MemberProfile() {
  const { id } = useParams();
  const { user } = useAuth();
  const { getMember, getGroup, getMemberNotes, updateMember, addNote, groups } = useApp();
  const navigate = useNavigate();
  const member = getMember(Number(id));
  const [tab, setTab] = useState('profile');
  const [showNoteForm, setShowNoteForm] = useState(false);
  const [newNote, setNewNote] = useState({ type: '关怀谈话', content: '' });
  const [pendingStage, setPendingStage] = useState(null);

  if (!member) return (
    <div style={{ padding: 40, textAlign: 'center' }}>
      <p style={{ fontSize: 15, color: 'var(--color-text-muted)', marginBottom: 20 }}>找不到该成员档案</p>
      <button className="btn btn-outline" onClick={() => navigate('/members')}>返回人员列表</button>
    </div>
  );

  const group = getGroup(member.groupId);
  const memberNotes = getMemberNotes(member.id);
  const age = member.dob ? Math.floor((new Date() - new Date(member.dob)) / (365.25 * 86400000)) : null;
  const stage = STAGES[member.stage];
  const canEdit = user?.role === 'pastor' || (user?.role === 'leader' && user?.groupId === member.groupId);

  const handleStageChange = (e) => {
    if (canEdit) setPendingStage(Number(e.target.value));
  };

  const confirmStageChange = () => {
    updateMember(member.id, { stage: pendingStage });
    setPendingStage(null);
  };

  const handleSubmitNote = () => {
    if (!newNote.content.trim()) return;
    addNote({ memberId: member.id, authorId: user?.id || 0, ...newNote });
    setNewNote({ type: '关怀谈话', content: '' });
    setShowNoteForm(false);
    setTab('notes');
  };

  const typeColors = { '关怀谈话': 'var(--color-accent)', '出席情况': 'var(--color-success)', '代祷事项': 'var(--stage-3)', '异常状况': 'var(--color-danger)' };

  return (
    <div className="fade-in">
      {/* Back */}
      <button onClick={() => navigate(-1)} className="btn btn-ghost btn-sm" style={{ marginBottom: 16 }}>
        ← 返回
      </button>

      {/* Profile hero */}
      <div className="card" style={{ marginBottom: 20, display: 'flex', gap: 20, flexWrap: 'wrap' }}>
        <Avatar name={member.name} src={member.avatar} size={72} />
        <div style={{ flex: 1 }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: 10, flexWrap: 'wrap', marginBottom: 6 }}>
            <h2 style={{ fontSize: 22, fontWeight: 700 }}>{member.name}</h2>
            {member.englishName && <span style={{ fontSize: 16, color: 'var(--color-text-muted)', fontWeight: 400 }}>{member.englishName}</span>}
            {member.isGroupLeader && <span style={{ fontSize: 11, padding: '2px 8px', borderRadius: 10, background: 'var(--color-accent-dim)', color: 'var(--color-accent)' }}>小组长</span>}
            <span style={{ fontSize: 12, padding: '2px 10px', borderRadius: 10,
              background: member.status === '活跃' ? 'rgba(90,171,126,0.15)' : 'var(--color-bg-secondary)',
              color: member.status === '活跃' ? 'var(--color-success)' : 'var(--color-text-muted)' }}>
              {member.status}
            </span>
          </div>
          <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap', alignItems: 'center' }}>
            <StageTag stage={member.stage} size="md" />
            {group && <span style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>{group.name}</span>}
            <span style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>{member.gender} · {age ? `${age}岁` : ''}</span>
          </div>
        </div>
        {canEdit && (
          <button onClick={() => { setShowNoteForm(true); setTab('notes'); }} className="btn btn-outline btn-sm">
            + 写近况
          </button>
        )}
      </div>

      {/* Tabs */}
      <div style={{ display: 'flex', gap: 4, marginBottom: 20, borderBottom: '1px solid var(--color-border)', paddingBottom: 0 }}>
        {['profile', 'pastoral', 'notes'].map(t => {
          const labels = { profile: '基本资料', pastoral: '牧养阶段', notes: `近况记录 (${memberNotes.length})` };
          return (
            <button key={t} onClick={() => setTab(t)} style={{
              background: 'none', border: 'none', cursor: 'pointer',
              padding: '10px 16px', fontSize: 14, fontWeight: tab === t ? 600 : 400,
              color: tab === t ? 'var(--color-accent)' : 'var(--color-text-secondary)',
              borderBottom: tab === t ? '2px solid var(--color-accent)' : '2px solid transparent',
              transition: 'all 0.2s', marginBottom: -1,
            }}>{labels[t]}</button>
          );
        })}
      </div>

      {/* Tab content */}
      {tab === 'profile' && (
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))', gap: 12 }}>
          {[
            { label: '英文名', value: member.englishName },
            { label: '学校 / 年级', value: member.school },
            { label: '手机号码', value: member.phone },
            { label: '邮箱地址', value: member.email },
            { label: '家长联系方式', value: member.parentContact },
            { label: '来教会时间', value: member.joinDate ? new Date(member.joinDate).toLocaleDateString('zh-CN') : '—' },
            { label: '受洗状况', value: member.baptized ? `已受洗（${member.baptizeDate ? new Date(member.baptizeDate).toLocaleDateString('zh-CN') : '日期未知'}）` : '未受洗' },
            { label: 'MBTI', value: member.mbti || '未填写' },
            { label: 'GDPR 同意', value: member.gdprConsent ? '已取得' : '待取得' },
          ].map(item => (
            <div key={item.label} className="card" style={{ padding: '14px 18px' }}>
              <div style={{ fontSize: 12, color: 'var(--color-text-muted)', marginBottom: 6 }}>{item.label}</div>
              <div style={{ fontSize: 14, color: item.label === 'GDPR 同意' && !member.gdprConsent ? 'var(--color-danger)' : 'var(--color-text-primary)' }}>
                {item.value || '—'}
              </div>
            </div>
          ))}
          {user?.role === 'pastor' && (
            <div className="card" style={{ padding: '14px 18px' }}>
              <div style={{ fontSize: 12, color: 'var(--color-text-muted)', marginBottom: 6 }}>所属小组</div>
              <select
                className="form-select"
                value={member.groupId ?? ''}
                onChange={e => {
                  const newGroupId = e.target.value ? Number(e.target.value) : null;
                  updateMember(member.id, { groupId: newGroupId });
                }}
                style={{ fontSize: 13, padding: '4px 8px', height: 30 }}
              >
                <option value="">（未分组）</option>
                {groups.map(g => <option key={g.id} value={g.id}>{g.name}</option>)}
              </select>
            </div>
          )}
        </div>
      )}

      {tab === 'pastoral' && (
        <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
          {/* Stage selector */}
          <div className="card">
            <div style={{ fontSize: 14, fontWeight: 600, marginBottom: 12 }}>当前牧养阶段</div>
            {canEdit ? (
              <div style={{ display: 'flex', alignItems: 'center', gap: 12, flexWrap: 'wrap' }}>
                <select className="form-select" value={member.stage} onChange={handleStageChange} style={{ maxWidth: 200 }}>
                  {STAGES.map((s, i) => <option key={i} value={i}>{s.name}</option>)}
                </select>
                <span style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>调整后需二次确认</span>
              </div>
            ) : (
              <StageTag stage={member.stage} size="md" />
            )}
          </div>

          {/* Tasks for current stage */}
          <div className="card">
            <div style={{ fontSize: 14, fontWeight: 600, marginBottom: 12, color: stage?.color }}>{stage?.name} — 追踪事项</div>
            {stage?.tasks.map((task, i) => (
              <div key={i} style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '10px 0', borderBottom: '1px solid var(--color-border)' }}>
                <div style={{ width: 20, height: 20, borderRadius: '50%', border: `2px solid ${stage.color}`, display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0, fontSize: 11, color: stage.color }}>
                  {i + 1}
                </div>
                <span style={{ fontSize: 14 }}>{task}</span>
              </div>
            ))}
          </div>

          {/* All stages overview */}
          <div className="card">
            <div style={{ fontSize: 14, fontWeight: 600, marginBottom: 12 }}>阶段路径</div>
            <div style={{ display: 'flex', gap: 6, flexWrap: 'wrap' }}>
              {STAGES.map((s, i) => (
                <div key={i} style={{ display: 'flex', alignItems: 'center', gap: 4 }}>
                  <div style={{
                    padding: '4px 12px', borderRadius: 20, fontSize: 12, fontWeight: 500,
                    background: i === member.stage ? s.bgColor : 'var(--color-bg-secondary)',
                    color: i === member.stage ? s.color : 'var(--color-text-muted)',
                    border: i === member.stage ? `1px solid ${s.color}60` : '1px solid transparent',
                  }}>{s.name}</div>
                  {i < 4 && <span style={{ color: 'var(--color-text-muted)', fontSize: 12 }}>→</span>}
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* Stage change confirmation modal */}
      {pendingStage !== null && (
        <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 1000 }}>
          <div className="card" style={{ width: 380, maxWidth: '90vw', padding: 28 }}>
            <h2 style={{ fontSize: 16, fontWeight: 700, marginBottom: 8 }}>确认调整牧养阶段</h2>
            <p style={{ fontSize: 14, color: 'var(--color-text-secondary)', lineHeight: 1.7, marginBottom: 20 }}>
              将 <strong>{member.name}</strong> 的牧养阶段从
              <span style={{ color: STAGES[member.stage]?.color, fontWeight: 600 }}>「{STAGES[member.stage]?.name}」</span>
              调整为
              <span style={{ color: STAGES[pendingStage]?.color, fontWeight: 600 }}>「{STAGES[pendingStage]?.name}」</span>，确认继续？
            </p>
            <div style={{ display: 'flex', gap: 10, justifyContent: 'flex-end' }}>
              <button className="btn btn-outline" onClick={() => setPendingStage(null)}>取消</button>
              <button className="btn btn-primary" onClick={confirmStageChange}>确认调整</button>
            </div>
          </div>
        </div>
      )}

      {tab === 'notes' && (
        <div>
          {/* Note form */}
          {showNoteForm && (
            <div className="card" style={{ marginBottom: 16, border: '1px solid var(--color-accent-dim)' }}>
              <div style={{ fontSize: 14, fontWeight: 600, marginBottom: 12 }}>写入近况记录</div>
              <div style={{ display: 'grid', gap: 12 }}>
                <div className="form-group">
                  <label className="form-label">记录类型</label>
                  <select className="form-select" value={newNote.type} onChange={e => setNewNote(p => ({...p, type: e.target.value}))}>
                    {NOTE_TYPES.map(t => <option key={t}>{t}</option>)}
                  </select>
                </div>
                <div className="form-group">
                  <label className="form-label">内容</label>
                  <textarea className="form-textarea" rows={4} placeholder="记录关怀内容、近况、祷告事项等…"
                    value={newNote.content} onChange={e => setNewNote(p => ({...p, content: e.target.value}))} />
                </div>
                <div style={{ display: 'flex', gap: 8 }}>
                  <button className="btn btn-primary btn-sm" onClick={handleSubmitNote}>保存记录</button>
                  <button className="btn btn-ghost btn-sm" onClick={() => setShowNoteForm(false)}>取消</button>
                </div>
              </div>
            </div>
          )}

          {!showNoteForm && canEdit && (
            <button className="btn btn-outline btn-sm" onClick={() => setShowNoteForm(true)} style={{ marginBottom: 16 }}>
              + 写入近况记录
            </button>
          )}

          {/* Timeline */}
          {memberNotes.length === 0 ? (
            <div className="empty-state"><p>暂无近况记录</p></div>
          ) : (
            <div style={{ position: 'relative', paddingLeft: 28 }}>
              <div style={{ position: 'absolute', left: 10, top: 0, bottom: 0, width: 1, background: 'var(--color-border)' }} />
              {memberNotes.map(note => (
                <div key={note.id} style={{ position: 'relative', marginBottom: 16 }}>
                  <div style={{
                    position: 'absolute', left: -22, top: 16, width: 10, height: 10,
                    borderRadius: '50%', background: typeColors[note.type] || 'var(--color-accent)',
                    border: '2px solid var(--color-bg-primary)',
                  }} />
                  <div className="card" style={{ padding: '14px 16px' }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 8, flexWrap: 'wrap', gap: 6 }}>
                      <span style={{ fontSize: 12, padding: '2px 10px', borderRadius: 10,
                        background: `${typeColors[note.type] || 'var(--color-accent)'}20`,
                        color: typeColors[note.type] || 'var(--color-accent)' }}>
                        {note.type}
                      </span>
                      <span style={{ fontSize: 11, color: 'var(--color-text-muted)' }}>
                        {new Date(note.date).toLocaleString('zh-CN', { month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}
                      </span>
                    </div>
                    <p style={{ fontSize: 14, lineHeight: 1.7, color: 'var(--color-text-secondary)' }}>{note.content}</p>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      )}
    </div>
  );
}
