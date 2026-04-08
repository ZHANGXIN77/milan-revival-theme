import { useRef, useState, useMemo, useCallback } from 'react';
import { useAuth } from '../context/AuthContext';
import { useApp } from '../context/AppContext';
import { STAGES } from '../data/mockData';
import StageTag from '../components/StageTag';
import Avatar from '../components/Avatar';

// ── helpers ──────────────────────────────────────────────
function joinDuration(joinDate) {
  if (!joinDate) return null;
  const months = Math.floor((new Date() - new Date(joinDate)) / (30.44 * 86400000));
  if (months < 1) return '不足一个月';
  const y = Math.floor(months / 12), m = months % 12;
  if (y > 0) return m > 0 ? `${y} 年 ${m} 个月` : `${y} 年`;
  return `${months} 个月`;
}

function StatBadge({ label, value, accent }) {
  return (
    <div style={{ textAlign: 'center', flex: 1 }}>
      <div style={{ fontSize: 22, fontWeight: 700, color: accent ? 'var(--color-accent)' : 'var(--color-text-primary)' }}>{value}</div>
      <div style={{ fontSize: 11, color: 'var(--color-text-muted)', marginTop: 2 }}>{label}</div>
    </div>
  );
}

// ── component ─────────────────────────────────────────────
export default function YouthProfile() {
  const { user } = useAuth();
  const { getMember, getGroup, getMemberNotes, getMembersByGroup, attendance, prayers, meetings, updateMember, members } = useApp();
  const member = getMember(user?.id);
  const fileInputRef = useRef(null);
  const [showEdit, setShowEdit] = useState(false);
  const [form, setForm] = useState({});
  const [showAllCareNotes, setShowAllCareNotes] = useState(false);

  if (!member) return <div style={{ padding: 40, color: 'var(--color-text-muted)' }}>正在加载…</div>;

  const group = getGroup(member.groupId);
  const age = member.dob ? Math.floor((new Date() - new Date(member.dob)) / (365.25 * 86400000)) : null;
  const stage = STAGES[member.stage];
  const nextStage = STAGES[member.stage + 1];

  // 出席统计
  const attendanceCount = useMemo(() =>
    attendance.reduce((n, rec) => {
      const r = rec.records.find(r => r.memberId === member.id);
      return n + (r?.present ? 1 : 0);
    }, 0),
  [attendance, member.id]);

  // 加入时长
  const duration = useMemo(() => joinDuration(member.joinDate), [member.joinDate]);

  // 受洗周年
  const baptismYears = useMemo(() =>
    member.baptizeDate
      ? Math.floor((new Date() - new Date(member.baptizeDate)) / (365.25 * 86400000))
      : null,
  [member.baptizeDate]);

  // 关怀近况（只展示"关怀谈话"类型，全部）
  const careNotes = useMemo(() =>
    getMemberNotes(member.id).filter(n => n.type === '关怀谈话'),
  [getMemberNotes, member.id]);

  // 小组成员（不含自己）
  const groupMembers = useMemo(() =>
    member.groupId ? getMembersByGroup(member.groupId).filter(m => m.id !== member.id) : [],
  [getMembersByGroup, member.groupId, member.id]);

  const leader = group?.leaderId ? getMember(group.leaderId) : null;

  // 近期四周聚会
  const upcomingMeetings = useMemo(() => {
    if (!member.groupId) return [];
    const today = new Date().toISOString().split('T')[0];
    const fourWeeks = new Date(); fourWeeks.setDate(fourWeeks.getDate() + 28);
    const fourWeeksStr = fourWeeks.toISOString().split('T')[0];
    return meetings
      .filter(m => m.groupId === member.groupId && m.date >= today && m.date <= fourWeeksStr)
      .sort((a, b) => a.date.localeCompare(b.date));
  }, [meetings, member.groupId]);

  // 成长任务进度
  const completedTasks = useMemo(() => {
    const progress = member.taskProgress || {};
    return new Set(progress[member.stage] || []);
  }, [member.taskProgress, member.stage]);

  const toggleTask = useCallback(async (taskIndex) => {
    const progress = member.taskProgress || {};
    const stageTasks = new Set(progress[member.stage] || []);
    if (stageTasks.has(taskIndex)) stageTasks.delete(taskIndex); else stageTasks.add(taskIndex);
    await updateMember(member.id, { taskProgress: { ...progress, [member.stage]: Array.from(stageTasks) } });
  }, [member.id, member.stage, member.taskProgress, updateMember]);

  const allTasksDone = stage?.tasks && completedTasks.size >= stage.tasks.length;

  // 代祷事项（本组成员提交的公开代祷）
  const groupMemberIds = useMemo(() =>
    member.groupId ? members.filter(m => m.groupId === member.groupId).map(m => m.id) : [],
  [members, member.groupId]);

  const publicPrayers = useMemo(() =>
    prayers.filter(p => p.visibility === 'all' && groupMemberIds.includes(p.authorId)),
  [prayers, groupMemberIds]);

  // ── 头像上传 ──
  const handleAvatarClick = () => fileInputRef.current?.click();
  const handleFileChange = (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) {
      alert('请选择图片文件（JPG、PNG、WEBP 等）');
      e.target.value = '';
      return;
    }
    if (file.size > 5 * 1024 * 1024) {
      alert('图片不能超过 5 MB，请压缩后再上传');
      e.target.value = '';
      return;
    }
    const reader = new FileReader();
    reader.onload = (ev) => updateMember(member.id, { avatar: ev.target.result });
    reader.readAsDataURL(file);
    e.target.value = '';
  };

  // ── 编辑资料 ──
  const openEdit = () => {
    setForm({ name: member.name || '', englishName: member.englishName || '', phone: member.phone || '', email: member.email || '', school: member.school || '', mbti: member.mbti || '' });
    setShowEdit(true);
  };
  const handleSave = async (e) => {
    e.preventDefault();
    await updateMember(member.id, {
      name: form.name.trim() || member.name,
      englishName: form.englishName.trim(),
      phone: form.phone.trim(),
      email: form.email.trim(),
      school: form.school.trim(),
      mbti: form.mbti.trim().toUpperCase() || null,
    });
    setShowEdit(false);
  };

  return (
    <div className="fade-in" style={{ maxWidth: 620 }}>
      <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: 20 }}>
        <h1 className="page-title">我的主页</h1>
        <button className="btn btn-outline btn-sm" onClick={openEdit}>编辑资料</button>
      </div>

      {/* ── 个人卡片 ── */}
      <div className="card" style={{ marginBottom: 16 }}>
        <div style={{ display: 'flex', gap: 16, alignItems: 'center', marginBottom: 16 }}>
          <div style={{ position: 'relative', cursor: 'pointer', flexShrink: 0 }} onClick={handleAvatarClick} title="点击更换头像">
            <Avatar name={member.name} src={member.avatar} size={68} />
            <div style={{
              position: 'absolute', bottom: 0, right: 0, width: 20, height: 20, borderRadius: '50%',
              background: 'var(--color-accent)', display: 'flex', alignItems: 'center', justifyContent: 'center',
              fontSize: 11, color: '#fff', border: '2px solid var(--color-bg-tertiary)',
            }}>
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
              </svg>
            </div>
          </div>
          <input ref={fileInputRef} type="file" accept="image/*" style={{ display: 'none' }} onChange={handleFileChange} />
          <div style={{ flex: 1 }}>
            <div style={{ display: 'flex', alignItems: 'baseline', gap: 10, flexWrap: 'wrap' }}>
              <span style={{ fontSize: 20, fontWeight: 700 }}>{member.name}</span>
              {member.englishName && <span style={{ fontSize: 15, color: 'var(--color-text-muted)' }}>{member.englishName}</span>}
            </div>
            <div style={{ fontSize: 13, color: 'var(--color-text-muted)', marginTop: 3 }}>
              {member.gender}{age ? ` · ${age}岁` : ''}{member.school ? ` · ${member.school}` : ''}
            </div>
            <div style={{ marginTop: 8, display: 'flex', gap: 8, flexWrap: 'wrap', alignItems: 'center' }}>
              <StageTag stage={member.stage} size="md" />
              {group && <span style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>{group.name}</span>}
            </div>
          </div>
        </div>

        {/* 数据统计栏 */}
        <div style={{ display: 'flex', borderTop: '1px solid var(--color-border)', paddingTop: 14, gap: 8 }}>
          <StatBadge label="陪伴教会" value={duration || '—'} accent />
          <div style={{ width: 1, background: 'var(--color-border)' }} />
          <StatBadge label="已参加聚会" value={`${attendanceCount} 次`} />
          <div style={{ width: 1, background: 'var(--color-border)' }} />
          {member.baptized
            ? <StatBadge label="受洗至今" value={baptismYears !== null ? `${baptismYears} 年` : '已受洗'} />
            : <StatBadge label="受洗状况" value="未受洗" />}
        </div>
      </div>

      {/* ── 我的成长目标 ── */}
      <div className="card" style={{ marginBottom: 16 }}>
        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: 14 }}>
          <div style={{ fontSize: 14, fontWeight: 600 }}>我的成长目标</div>
          <StageTag stage={member.stage} size="sm" />
        </div>

        {/* 当前阶段任务 */}
        <div style={{ marginBottom: nextStage ? 14 : 0 }}>
          <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: 6 }}>
            <div style={{ fontSize: 12, color: stage?.color, fontWeight: 600 }}>
              {stage?.name} 阶段 — 当前目标
            </div>
            <div style={{ fontSize: 11, color: 'var(--color-text-muted)' }}>
              {completedTasks.size}/{stage?.tasks.length} 已完成
            </div>
          </div>
          {/* 进度条 */}
          <div style={{ height: 4, background: 'var(--color-bg-secondary)', borderRadius: 2, marginBottom: 10, overflow: 'hidden' }}>
            <div style={{ height: '100%', borderRadius: 2, background: stage?.color, width: `${(completedTasks.size / (stage?.tasks.length || 1)) * 100}%`, transition: 'width 0.4s ease' }} />
          </div>
          {stage?.tasks.map((task, i) => {
            const done = completedTasks.has(i);
            return (
              <div key={i} onClick={() => toggleTask(i)}
                style={{ display: 'flex', alignItems: 'flex-start', gap: 10, padding: '8px 0', borderBottom: i < stage.tasks.length - 1 ? '1px solid var(--color-border)' : 'none', cursor: 'pointer' }}>
                <div style={{
                  width: 18, height: 18, borderRadius: '50%', flexShrink: 0, marginTop: 1,
                  border: `2px solid ${stage.color}`, display: 'flex', alignItems: 'center', justifyContent: 'center',
                  background: done ? stage.color : stage.bgColor,
                  transition: 'all 0.2s',
                }}>
                  {done && (
                    <svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="#fff" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                      <polyline points="2,6 5,9 10,3" />
                    </svg>
                  )}
                  {!done && <div style={{ width: 6, height: 6, borderRadius: '50%', background: stage.color }} />}
                </div>
                <span style={{ fontSize: 13, lineHeight: 1.5, color: done ? 'var(--color-text-muted)' : 'var(--color-text-primary)', textDecoration: done ? 'line-through' : 'none', transition: 'all 0.2s' }}>{task}</span>
              </div>
            );
          })}
          {allTasksDone && (
            <div style={{ marginTop: 12, padding: '10px 14px', background: `${stage.color}18`, border: `1px solid ${stage.color}40`, borderRadius: 'var(--radius-sm)' }}>
              <div style={{ fontSize: 13, fontWeight: 600, color: stage.color, marginBottom: 2 }}>你已完成本阶段所有目标！</div>
              <div style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>请告诉你的小组长，请他为你做阶段晋升评估</div>
            </div>
          )}
        </div>

        {/* 下一阶段预览 */}
        {nextStage && (
          <div style={{ marginTop: 4, padding: '10px 12px', background: 'var(--color-bg-secondary)', borderRadius: 'var(--radius-sm)' }}>
            <div style={{ fontSize: 11, color: 'var(--color-text-muted)', marginBottom: 4 }}>下一阶段：{nextStage.name}</div>
            <div style={{ fontSize: 12, color: 'var(--color-text-secondary)' }}>
              {nextStage.tasks.join('　·　')}
            </div>
          </div>
        )}
      </div>

      {/* ── 我的小组 ── */}
      {group && (
        <div className="card" style={{ marginBottom: 16 }}>
          <div style={{ fontSize: 14, fontWeight: 600, marginBottom: 14 }}>我的小组 · {group.name}</div>

          {/* 小组长 */}
          {leader && leader.id !== member.id && (
            <div style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '10px 12px', background: 'var(--color-accent-dim)', borderRadius: 'var(--radius-sm)', marginBottom: 12 }}>
              <Avatar name={leader.name} src={leader.avatar} size={36} />
              <div style={{ flex: 1 }}>
                <div style={{ fontSize: 13, fontWeight: 600, color: 'var(--color-accent)' }}>{leader.name}</div>
                <div style={{ fontSize: 11, color: 'var(--color-text-muted)' }}>小组长</div>
              </div>
              {leader.phone && (
                <a href={`tel:${leader.phone}`} style={{ fontSize: 12, color: 'var(--color-accent)', textDecoration: 'none' }}>
                  联系 →
                </a>
              )}
            </div>
          )}

          {/* 组员头像墙 */}
          {groupMembers.length > 0 && (
            <div>
              <div style={{ fontSize: 12, color: 'var(--color-text-muted)', marginBottom: 10 }}>
                小组共 {groupMembers.length + 1} 位成员
              </div>
              <div style={{ display: 'flex', flexWrap: 'wrap', gap: 12 }}>
                {groupMembers.map(m => (
                  <div key={m.id} style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 4, width: 52 }}>
                    <Avatar name={m.name} src={m.avatar} size={40} />
                    <span style={{ fontSize: 11, color: 'var(--color-text-secondary)', textAlign: 'center', lineHeight: 1.3 }}>{m.name}</span>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>
      )}

      {/* ── 有人在关心你 ── */}
      {careNotes.length > 0 && (
        <div className="card" style={{ marginBottom: 16 }}>
          <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: 14 }}>
            <div style={{ fontSize: 14, fontWeight: 600 }}>有人在关心你</div>
            {careNotes.length > 2 && (
              <button className="btn btn-ghost btn-sm" onClick={() => setShowAllCareNotes(v => !v)} style={{ fontSize: 12 }}>
                {showAllCareNotes ? '收起' : `查看全部 ${careNotes.length} 条`}
              </button>
            )}
          </div>
          <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
            {(showAllCareNotes ? careNotes : careNotes.slice(0, 2)).map(note => {
              const author = getMember(note.authorId);
              return (
                <div key={note.id} style={{ display: 'flex', gap: 12 }}>
                  <Avatar name={author?.name || '？'} src={author?.avatar} size={34} />
                  <div style={{ flex: 1 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 4 }}>
                      <span style={{ fontSize: 13, fontWeight: 600 }}>{author?.name || '—'}</span>
                      <span style={{ fontSize: 11, color: 'var(--color-text-muted)' }}>
                        {new Date(note.date).toLocaleDateString('zh-CN', { month: 'long', day: 'numeric' })}
                      </span>
                    </div>
                    <p style={{ fontSize: 13, color: 'var(--color-text-secondary)', lineHeight: 1.65 }}>{note.content}</p>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      )}

      {/* ── MBTI ── */}
      <div className="card" style={{ marginBottom: 16 }}>
        <div style={{ fontSize: 14, fontWeight: 600, marginBottom: 12 }}>人格类型 MBTI</div>
        {member.mbti ? (
          <div style={{ display: 'flex', alignItems: 'center', gap: 16 }}>
            <div style={{ fontSize: 30, fontWeight: 700, color: 'var(--color-accent)' }}>{member.mbti}</div>
            <span style={{ fontSize: 13, color: 'var(--color-text-muted)' }}>性格类型已记录</span>
          </div>
        ) : (
          <div>
            <p style={{ fontSize: 13, color: 'var(--color-text-muted)', marginBottom: 12 }}>
              你还没有填写 MBTI 人格类型。完成测试后在"编辑资料"里填写吧！
            </p>
            <a href="https://www.16personalities.com/ch" target="_blank" rel="noreferrer" className="btn btn-outline btn-sm">
              前往 MBTI 测试 →
            </a>
          </div>
        )}
      </div>

      {/* ── 近期聚会 ── */}
      {member.groupId && (
        <div className="card" style={{ marginBottom: 16 }}>
          <div style={{ fontSize: 14, fontWeight: 600, marginBottom: 14 }}>近期四周聚会</div>
          {upcomingMeetings.length === 0 ? (
            <p style={{ fontSize: 13, color: 'var(--color-text-muted)' }}>近四周暂无聚会安排</p>
          ) : upcomingMeetings.map((m, idx) => (
            <div key={m.id} style={{ display: 'flex', gap: 14, padding: '10px 0', borderBottom: idx < upcomingMeetings.length - 1 ? '1px solid var(--color-border)' : 'none' }}>
              {/* 日期块 */}
              <div style={{ textAlign: 'center', minWidth: 40, flexShrink: 0 }}>
                <div style={{ fontSize: 20, fontWeight: 700, color: idx === 0 ? 'var(--color-accent)' : 'var(--color-text-primary)', lineHeight: 1 }}>
                  {m.date.slice(8)}
                </div>
                <div style={{ fontSize: 11, color: 'var(--color-text-muted)', marginTop: 2 }}>
                  {new Date(m.date + 'T00:00:00').toLocaleDateString('zh-CN', { month: 'numeric', day: 'numeric', weekday: 'short' }).replace(/\d+\/\d+/, '')}
                </div>
              </div>
              <div style={{ width: 1, background: 'var(--color-border)', margin: '2px 0' }} />
              {/* 内容 */}
              <div style={{ flex: 1 }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 4 }}>
                  <span style={{ fontSize: 14, fontWeight: 600 }}>{m.theme}</span>
                  {idx === 0 && <span style={{ fontSize: 11, padding: '1px 8px', borderRadius: 10, background: 'var(--color-accent-dim)', color: 'var(--color-accent)' }}>最近一次</span>}
                </div>
                <div style={{ display: 'flex', gap: 14, flexWrap: 'wrap' }}>
                  {m.time && <span style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>{m.time}</span>}
                  {m.location && <span style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>{m.location}</span>}
                  {m.address && (
                    <a
                      href={`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(m.address)}`}
                      target="_blank"
                      rel="noopener noreferrer"
                      style={{ fontSize: 12, color: 'var(--color-accent)', textDecoration: 'underline', textUnderlineOffset: 3 }}
                    >{m.address}</a>
                  )}
                </div>
                {m.notes && <p style={{ fontSize: 12, color: 'var(--color-text-secondary)', marginTop: 4, fontStyle: 'italic' }}>{m.notes}</p>}
              </div>
            </div>
          ))}
        </div>
      )}

      {/* ── 小组代祷事项 ── */}
      <div className="card">
        <div style={{ fontSize: 14, fontWeight: 600, marginBottom: 12 }}>小组代祷事项</div>
        {publicPrayers.length === 0 ? (
          <p style={{ fontSize: 13, color: 'var(--color-text-muted)' }}>暂无代祷事项</p>
        ) : publicPrayers.map(p => (
          <div key={p.id} style={{ padding: '10px 0', borderBottom: '1px solid var(--color-border)' }}>
            <div style={{ fontSize: 14, fontWeight: 500, marginBottom: 4 }}>{p.title}</div>
            {p.content && <p style={{ fontSize: 12, color: 'var(--color-text-muted)', lineHeight: 1.6 }}>{p.content}</p>}
          </div>
        ))}
      </div>

      {/* ── 编辑资料弹窗 ── */}
      {showEdit && (
        <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 1000 }}
          onClick={(e) => { if (e.target === e.currentTarget) setShowEdit(false); }}>
          <div className="card" style={{ width: 420, maxWidth: '90vw', padding: 28 }}>
            <h2 style={{ fontSize: 16, fontWeight: 700, marginBottom: 20 }}>编辑个人资料</h2>
            <form onSubmit={handleSave}>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
                {[
                  { key: 'name', label: '姓名', placeholder: '' },
                  { key: 'englishName', label: '英文名', placeholder: 'English name' },
                  { key: 'phone', label: '手机号码', placeholder: '+39 333 ...' },
                  { key: 'email', label: '邮箱地址', placeholder: 'example@gmail.com' },
                  { key: 'school', label: '学校 / 职位', placeholder: '' },
                  { key: 'mbti', label: 'MBTI', placeholder: '例：ENFJ' },
                ].map(({ key, label, placeholder }) => (
                  <div key={key} className="form-group">
                    <label className="form-label">{label}</label>
                    <input className="form-input" placeholder={placeholder} value={form[key]}
                      onChange={e => setForm(f => ({ ...f, [key]: e.target.value }))} />
                  </div>
                ))}
              </div>
              <div style={{ display: 'flex', gap: 10, justifyContent: 'flex-end', marginTop: 20 }}>
                <button type="button" className="btn btn-outline" onClick={() => setShowEdit(false)}>取消</button>
                <button type="submit" className="btn btn-primary">保存</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
