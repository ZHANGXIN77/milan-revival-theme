import { useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { useApp } from '../context/AppContext';
import { useSuccessMessage } from '../hooks/useSuccessMessage';
import Avatar from '../components/Avatar';

export default function Attendance() {
  const { user } = useAuth();
  const { members, groups, attendance, getMembersByGroup, recordAttendance } = useApp();
  const isPastor = user?.role === 'pastor';

  const today = new Date().toISOString().split('T')[0];
  const [selectedDate, setSelectedDate] = useState(today);
  const [selectedGroup, setSelectedGroup] = useState(isPastor ? '' : String(user?.groupId || ''));

  const getDateMembers = () => {
    if (!selectedGroup) return members;
    return getMembersByGroup(Number(selectedGroup));
  };

  const displayMembers = getDateMembers().filter(m => m.status === '活跃');

  const getAttendanceForDate = (date) => attendance.find(a => a.date === date)?.records || [];

  const [editingDate, setEditingDate] = useState(null);
  const [editRecords, setEditRecords] = useState({});
  const [success, setSuccess] = useSuccessMessage();

  const startEditing = () => {
    const existing = getAttendanceForDate(selectedDate);
    const records = {};
    displayMembers.forEach(m => {
      const rec = existing.find(r => r.memberId === m.id);
      records[m.id] = rec ? rec.present : false;
    });
    setEditRecords(records);
    setEditingDate(selectedDate);
  };

  const toggleMember = (memberId) => {
    setEditRecords(prev => ({ ...prev, [memberId]: !prev[memberId] }));
  };

  const saveAttendance = async () => {
    const records = displayMembers.map(m => ({ memberId: m.id, present: editRecords[m.id] || false }));
    await recordAttendance(selectedDate, records);
    setEditingDate(null);
    const presentN = records.filter(r => r.present).length;
    setSuccess(`${new Date(selectedDate).toLocaleDateString('zh-CN', { month: 'long', day: 'numeric' })} 出席记录已保存（${presentN}/${records.length} 人出席）`);
  };

  const isEditing = editingDate === selectedDate;
  const currentRecords = isEditing ? editRecords : (() => {
    const recs = getAttendanceForDate(selectedDate);
    const map = {};
    recs.forEach(r => { map[r.memberId] = r.present; });
    return map;
  })();

  const presentCount = displayMembers.filter(m => currentRecords[m.id]).length;

  const recentDates = attendance.slice(0, 4).map(a => a.date);
  const memberStats = displayMembers.map(m => ({
    ...m,
    rate: recentDates.length ? Math.round(recentDates.filter(d => {
      const recs = getAttendanceForDate(d);
      const rec = recs.find(r => r.memberId === m.id);
      return rec && rec.present;
    }).length / recentDates.length * 100) : null,
  }));

  const groupsByLeader = isPastor ? groups : groups.filter(g => g.id === user?.groupId);

  return (
    <div className="fade-in">
      <div className="page-header">
        <div>
          <h1 className="page-title">出席记录</h1>
          <p className="page-subtitle">记录主日崇拜出席情况</p>
        </div>
      </div>

      {success && (
        <div style={{ marginBottom: 16, padding: '10px 16px', borderRadius: 'var(--radius-sm)', background: 'rgba(90,171,126,0.12)', border: '1px solid var(--color-success)', color: 'var(--color-success)', fontSize: 13 }}>
          {success}
        </div>
      )}

      {/* 今日出席提示 */}
      {selectedDate === today && !isEditing && getAttendanceForDate(today).length === 0 && displayMembers.length > 0 && (
        <div style={{ marginBottom: 20, padding: '14px 18px', borderRadius: 'var(--radius-sm)', background: 'var(--color-accent-dim)', border: '1px solid var(--color-accent)', display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: 12, flexWrap: 'wrap' }}>
          <div>
            <div style={{ fontSize: 14, fontWeight: 600, color: 'var(--color-accent)', marginBottom: 2 }}>今天还没有出席记录</div>
            <div style={{ fontSize: 12, color: 'var(--color-text-muted)' }}>
              {new Date().toLocaleDateString('zh-CN', { month: 'long', day: 'numeric', weekday: 'long' })} · {displayMembers.length} 人待记录
            </div>
          </div>
          <button className="btn btn-primary btn-sm" onClick={startEditing}>开始记录 →</button>
        </div>
      )}

      {/* Controls */}
      <div style={{ display: 'flex', gap: 10, marginBottom: 20, flexWrap: 'wrap', alignItems: 'center' }}>
        <input type="date" className="form-input" value={selectedDate}
          onChange={e => { setSelectedDate(e.target.value); setEditingDate(null); }}
          style={{ width: 160 }} />
        {isPastor && (
          <select className="form-select" value={selectedGroup} onChange={e => { setSelectedGroup(e.target.value); setEditingDate(null); }} style={{ width: 160 }}>
            <option value="">所有小组</option>
            {groups.map(g => <option key={g.id} value={g.id}>{g.name}</option>)}
          </select>
        )}
        <div style={{ marginLeft: 'auto', display: 'flex', gap: 8 }}>
          {isEditing ? (
            <>
              <button className="btn btn-ghost btn-sm" onClick={() => {
                const allPresent = displayMembers.every(m => editRecords[m.id]);
                const next = {};
                displayMembers.forEach(m => { next[m.id] = !allPresent; });
                setEditRecords(next);
              }}>
                {displayMembers.every(m => editRecords[m.id]) ? '取消全选' : '全选出席'}
              </button>
              <button className="btn btn-primary btn-sm" onClick={saveAttendance}>保存记录</button>
              <button className="btn btn-ghost btn-sm" onClick={() => setEditingDate(null)}>取消</button>
            </>
          ) : (
            <button className="btn btn-outline btn-sm" onClick={startEditing}>
              {getAttendanceForDate(selectedDate).length > 0 ? '编辑记录' : '+ 记录出席'}
            </button>
          )}
        </div>
      </div>

      {/* Stats */}
      {displayMembers.length > 0 && (
        <div style={{ display: 'flex', gap: 12, marginBottom: 20 }}>
          <div className="card" style={{ padding: '12px 20px', display: 'flex', alignItems: 'center', gap: 12 }}>
            <div style={{ fontSize: 24, fontWeight: 700, color: 'var(--color-success)' }}>{presentCount}</div>
            <div>
              <div style={{ fontSize: 13 }}>已出席</div>
              <div style={{ fontSize: 11, color: 'var(--color-text-muted)' }}>{Math.round(presentCount / displayMembers.length * 100)}%</div>
            </div>
          </div>
          <div className="card" style={{ padding: '12px 20px', display: 'flex', alignItems: 'center', gap: 12 }}>
            <div style={{ fontSize: 24, fontWeight: 700, color: 'var(--color-danger)' }}>{displayMembers.length - presentCount}</div>
            <div>
              <div style={{ fontSize: 13 }}>缺席</div>
              <div style={{ fontSize: 11, color: 'var(--color-text-muted)' }}>{displayMembers.length} 人在册</div>
            </div>
          </div>
        </div>
      )}

      {/* Attendance list grouped by group */}
      {(selectedGroup ? [groups.find(g => g.id === Number(selectedGroup))].filter(Boolean) : groupsByLeader).map(group => {
        const groupMembers = memberStats.filter(m => m.groupId === group.id && m.status === '活跃');
        if (groupMembers.length === 0) return null;
        return (
          <div key={group.id} style={{ marginBottom: 20 }}>
            <div style={{ fontSize: 14, fontWeight: 600, color: 'var(--color-text-secondary)', marginBottom: 10, paddingBottom: 8, borderBottom: '1px solid var(--color-border)' }}>
              {group.name} · {groupMembers.filter(m => currentRecords[m.id]).length}/{groupMembers.length} 人出席
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))', gap: 8 }}>
              {groupMembers.map(m => {
                const present = currentRecords[m.id];
                return (
                  <button key={m.id}
                    onClick={() => isEditing && toggleMember(m.id)}
                    style={{
                      display: 'flex', alignItems: 'center', gap: 10,
                      padding: '10px 14px', borderRadius: 'var(--radius-sm)',
                      background: present ? 'rgba(90,171,126,0.12)' : 'var(--color-bg-tertiary)',
                      border: present ? '1px solid rgba(90,171,126,0.3)' : '1px solid var(--color-border)',
                      cursor: isEditing ? 'pointer' : 'default',
                      transition: 'all 0.2s', textAlign: 'left', width: '100%',
                    }}>
                    <Avatar name={m.name} size={32} />
                    <div style={{ flex: 1 }}>
                      <div style={{ fontSize: 13, fontWeight: 500, color: present ? 'var(--color-success)' : 'var(--color-text-secondary)' }}>{m.name}</div>
                      {m.rate !== null && <div style={{ fontSize: 10, color: 'var(--color-text-muted)', marginTop: 2 }}>近期出席率 {m.rate}%</div>}
                    </div>
                    <div style={{ width: 20, height: 20, borderRadius: '50%', flexShrink: 0,
                      background: present ? 'var(--color-success)' : 'transparent',
                      border: present ? 'none' : '2px solid var(--color-border-hover)',
                      display: 'flex', alignItems: 'center', justifyContent: 'center',
                      fontSize: 11, color: '#fff' }}>
                    </div>
                  </button>
                );
              })}
            </div>
          </div>
        );
      })}
    </div>
  );
}
