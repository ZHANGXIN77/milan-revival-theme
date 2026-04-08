import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useApp } from '../context/AppContext';
import { STAGES } from '../data/mockData';
import StageTag from '../components/StageTag';
import Avatar from '../components/Avatar';

function StatCard({ label, value, sub, accent }) {
  return (
    <div className="card" style={{ textAlign: 'center' }}>
      <div style={{ fontSize: 32, fontWeight: 700, color: accent || 'var(--color-accent)' }}>{value}</div>
      <div style={{ fontSize: 13, color: 'var(--color-text-primary)', fontWeight: 500, marginTop: 4 }}>{label}</div>
      {sub && <div style={{ fontSize: 11, color: 'var(--color-text-muted)', marginTop: 3 }}>{sub}</div>}
    </div>
  );
}

export default function Dashboard() {
  const { user } = useAuth();
  const { members, groups, notes, attendance, getMembersByGroup } = useApp();
  const navigate = useNavigate();

  const isPastor = user?.role === 'pastor';
  const myMembers = isPastor ? members : getMembersByGroup(user?.groupId);

  // Stage distribution
  const stageCounts = STAGES.map((s, i) => ({ ...s, count: myMembers.filter(m => m.stage === i).length }));

  // Recent notes (last 7 days)
  const recentNotes = notes
    .filter(n => isPastor || myMembers.find(m => m.id === n.memberId))
    .sort((a, b) => new Date(b.date) - new Date(a.date))
    .slice(0, 5);

  // Attendance alert: absent 2+ weeks
  const latestTwo = attendance.slice(0, 2);
  const alertMembers = myMembers.filter(m => {
    const absences = latestTwo.filter(w => {
      const rec = w.records.find(r => r.memberId === m.id);
      return rec && !rec.present;
    });
    return absences.length >= 2;
  });

  const activeMembers = myMembers.filter(m => m.status === '活跃');
  const activeCount = activeMembers.length;
  const latestAttRate = (attendance[0] && activeCount > 0) ? Math.round(
    attendance[0].records.filter(r => activeMembers.find(m => m.id === r.memberId) && r.present).length / activeCount * 100
  ) : 0;

  const typeColors = { '关怀谈话': 'var(--color-accent)', '出席情况': 'var(--color-success)', '代祷事项': 'var(--stage-3)', '异常状况': 'var(--color-danger)' };

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <h1 className="page-title">{isPastor ? '牧区总览' : '小组概览'}</h1>
          <p className="page-subtitle">欢迎回来，{user?.name}｜今天是 {new Date().toLocaleDateString('zh-CN', { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' })}</p>
        </div>
        <div style={{ display: 'flex', gap: 8 }}>
          <button className="btn btn-outline" onClick={() => navigate('/attendance')}>记录今日出席 →</button>
          <button className="btn btn-primary" onClick={() => navigate('/members')}>查看人员 →</button>
        </div>
      </div>

      {/* Stats row */}
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(150px, 1fr))', gap: 12, marginBottom: 24 }}>
        <StatCard label="在册人数" value={myMembers.length} sub={`${activeCount} 人活跃`} />
        <StatCard label="本周出席率" value={`${latestAttRate}%`}
          sub={attendance[0] ? new Date(attendance[0].date).toLocaleDateString('zh-CN', { month: 'numeric', day: 'numeric' }) + ' 主日' : '暂无记录'}
          accent="var(--color-success)" />
        <StatCard label="需要关注" value={alertMembers.length} sub="连续缺席2周" accent={alertMembers.length > 0 ? 'var(--color-danger)' : undefined} />
        {isPastor && <StatCard label="小组数量" value={groups.length} sub={`共 ${groups.length} 个小组`} accent="var(--stage-3)" />}
      </div>

      {/* Attendance trend */}
      {attendance.length > 1 && (
        <div className="card" style={{ marginBottom: 20 }}>
          <div style={{ fontSize: 15, fontWeight: 600, marginBottom: 14 }}>近期出席趋势</div>
          <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
            {attendance.slice(0, 4).map(a => {
              const relevant = a.records.filter(r => activeMembers.find(m => m.id === r.memberId));
              const present = relevant.filter(r => r.present).length;
              const total = activeCount;
              const rate = total ? Math.round(present / total * 100) : 0;
              const barColor = rate >= 80 ? 'var(--color-success)' : rate >= 60 ? 'var(--color-accent)' : 'var(--color-danger)';
              return (
                <div key={a.date} style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                  <div style={{ width: 72, fontSize: 12, color: 'var(--color-text-muted)', flexShrink: 0 }}>
                    {new Date(a.date).toLocaleDateString('zh-CN', { month: 'numeric', day: 'numeric' })}
                  </div>
                  <div style={{ flex: 1, height: 8, background: 'var(--color-bg-secondary)', borderRadius: 4, overflow: 'hidden' }}>
                    <div style={{ width: `${rate}%`, height: '100%', background: barColor, borderRadius: 4, transition: 'width 0.6s ease' }} />
                  </div>
                  <div style={{ width: 36, fontSize: 12, color: 'var(--color-text-secondary)', textAlign: 'right', flexShrink: 0 }}>{rate}%</div>
                  <div style={{ width: 52, fontSize: 11, color: 'var(--color-text-muted)', flexShrink: 0 }}>{present}/{total} 人</div>
                </div>
              );
            })}
          </div>
        </div>
      )}

      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20 }}>
        {/* Stage distribution */}
        <div className="card">
          <div style={{ fontSize: 15, fontWeight: 600, marginBottom: 16 }}>牧养阶段分布</div>
          {stageCounts.map(s => (
            <div key={s.id} style={{ marginBottom: 10 }}>
              <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 4, fontSize: 13 }}>
                <span style={{ color: s.color }}>{s.name}</span>
                <span style={{ color: 'var(--color-text-muted)' }}>{s.count} 人</span>
              </div>
              <div style={{ height: 6, background: 'var(--color-bg-secondary)', borderRadius: 3, overflow: 'hidden' }}>
                <div style={{
                  height: '100%', borderRadius: 3, transition: 'width 0.8s ease',
                  width: myMembers.length ? `${(s.count / myMembers.length) * 100}%` : '0%',
                  background: s.color,
                }} />
              </div>
            </div>
          ))}
        </div>

        {/* Alert members */}
        <div className="card">
          <div style={{ fontSize: 15, fontWeight: 600, marginBottom: 16, display: 'flex', alignItems: 'center', gap: 8 }}>
            需要关注
          </div>
          {alertMembers.length === 0 ? (
            <div className="empty-state" style={{ padding: '20px' }}>
              <p>所有人出席正常</p>
            </div>
          ) : alertMembers.map(m => (
            <div key={m.id} onClick={() => navigate(`/members/${m.id}`)}
              style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '8px 0', cursor: 'pointer', borderBottom: '1px solid var(--color-border)' }}>
              <Avatar name={m.name} size={32} />
              <div>
                <div style={{ fontSize: 13, fontWeight: 500 }}>{m.name}</div>
                <div style={{ fontSize: 11, color: 'var(--color-danger)' }}>连续缺席 2 周</div>
              </div>
              <StageTag stage={m.stage} />
            </div>
          ))}
        </div>

        {/* Recent notes */}
        <div className="card" style={{ gridColumn: '1 / -1' }}>
          <div style={{ fontSize: 15, fontWeight: 600, marginBottom: 16 }}>最近更新</div>
          {recentNotes.length === 0 ? (
            <div className="empty-state"><p>暂无近况记录</p></div>
          ) : recentNotes.map(note => {
            const member = myMembers.find(m => m.id === note.memberId);
            if (!member) return null;
            return (
              <div key={note.id} onClick={() => navigate(`/members/${note.memberId}`)}
                style={{ display: 'flex', gap: 12, padding: '12px 0', borderBottom: '1px solid var(--color-border)', cursor: 'pointer' }}>
                <Avatar name={member.name} size={36} />
                <div style={{ flex: 1, minWidth: 0 }}>
                  <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 4 }}>
                    <span style={{ fontSize: 13, fontWeight: 600 }}>{member.name}</span>
                    <span style={{ fontSize: 11, padding: '1px 8px', borderRadius: 10, background: `${typeColors[note.type]}20`, color: typeColors[note.type] }}>{note.type}</span>
                    <span style={{ fontSize: 11, color: 'var(--color-text-muted)', marginLeft: 'auto' }}>{new Date(note.date).toLocaleDateString('zh-CN')}</span>
                  </div>
                  <p style={{ fontSize: 13, color: 'var(--color-text-secondary)', overflow: 'hidden', display: '-webkit-box', WebkitLineClamp: 2, WebkitBoxOrient: 'vertical' }}>
                    {note.content}
                  </p>
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </div>
  );
}
