import { createContext, useContext, useState, useEffect, useCallback, useRef } from 'react';
import { useAuth } from './AuthContext';
import { apiFetch } from '../utils/api';

const AppContext = createContext(null);

export function AppProvider({ children }) {
  const { credential, user } = useAuth();

  const [members,    setMembers]    = useState([]);
  const [groups,     setGroups]     = useState([]);
  const [attendance, setAttendance] = useState([]);
  const [notes,      setNotes]      = useState([]);
  const [prayers,    setPrayers]    = useState([]);
  const [meetings,   setMeetings]   = useState([]);

  const [loading, setLoading] = useState(false);
  const [error,   setError]   = useState(null);

  // 用 ref 追踪是否已加载，避免重复请求
  const loaded = useRef(false);

  // ── 初始化：登录后从服务端加载所有数据 ──────────────────────────────────────
  useEffect(() => {
    if (!credential || !user) {
      // 登出时清空数据
      setMembers([]); setGroups([]); setAttendance([]);
      setNotes([]); setPrayers([]); setMeetings([]);
      loaded.current = false;
      return;
    }

    if (loaded.current) return;

    const load = async () => {
      setLoading(true);
      setError(null);
      try {
        const isPastorOrLeader = user.role === 'pastor' || user.role === 'leader';

        const requests = [
          isPastorOrLeader ? apiFetch('/groups',     'GET', null, credential) : Promise.resolve([]),
          isPastorOrLeader ? apiFetch('/attendance', 'GET', null, credential) : Promise.resolve([]),
          isPastorOrLeader ? apiFetch('/notes',      'GET', null, credential) : Promise.resolve([]),
          apiFetch('/prayers',  'GET', null, credential),
          apiFetch('/meetings', 'GET', null, credential),
        ];

        // 成员列表只有 pastor/leader 需要
        if (isPastorOrLeader) {
          requests.unshift(apiFetch('/members', 'GET', null, credential));
        } else {
          requests.unshift(Promise.resolve([]));
        }

        const [m, g, a, n, p, mt] = await Promise.all(requests);
        setMembers(m);
        setGroups(g);
        setAttendance(a);
        setNotes(n);
        setPrayers(p);
        setMeetings(mt);
        loaded.current = true;
      } catch (err) {
        setError('数据加载失败：' + err.message);
      } finally {
        setLoading(false);
      }
    };

    load();
  }, [credential, user]);

  // ── 工具函数 ──────────────────────────────────────────────────────────────
  const getMember         = useCallback((id) => members.find(m => m.id === id), [members]);
  const getGroup          = useCallback((id) => groups.find(g => g.id === id), [groups]);
  const getMemberNotes    = useCallback((memberId) =>
    notes.filter(n => n.memberId === memberId).sort((a, b) => new Date(b.date) - new Date(a.date)),
    [notes]
  );
  const getMembersByGroup = useCallback((groupId) =>
    members.filter(m => m.groupId === groupId),
    [members]
  );

  // ── 成员操作 ──────────────────────────────────────────────────────────────
  const addMember = useCallback(async (data) => {
    const result = await apiFetch('/members', 'POST', data, credential);
    setMembers(prev => [...prev, result]);
    return result;
  }, [credential]);

  const updateMember = useCallback(async (id, data) => {
    const result = await apiFetch(`/members/${id}`, 'PUT', data, credential);
    setMembers(prev => prev.map(m => m.id === id ? result : m));
    return result;
  }, [credential]);

  // ── 小组操作 ──────────────────────────────────────────────────────────────
  const addGroup = useCallback(async (data) => {
    const result = await apiFetch('/groups', 'POST', data, credential);
    setGroups(prev => [...prev, result]);
    return result;
  }, [credential]);

  const updateGroup = useCallback(async (id, data) => {
    const result = await apiFetch(`/groups/${id}`, 'PUT', data, credential);
    setGroups(prev => prev.map(g => g.id === id ? result : g));
    return result;
  }, [credential]);

  // ── 出席操作 ──────────────────────────────────────────────────────────────
  const recordAttendance = useCallback(async (date, records) => {
    await apiFetch('/attendance', 'POST', { date, records }, credential);
    // 重新拉取出席记录（保证顺序一致）
    const updated = await apiFetch('/attendance', 'GET', null, credential);
    setAttendance(updated);
  }, [credential]);

  // ── 笔记操作 ──────────────────────────────────────────────────────────────
  const addNote = useCallback(async (note) => {
    const result = await apiFetch('/notes', 'POST', note, credential);
    setNotes(prev => [result, ...prev]);
    return result;
  }, [credential]);

  // ── 代祷操作 ──────────────────────────────────────────────────────────────
  const addPrayer = useCallback(async (prayer) => {
    const result = await apiFetch('/prayers', 'POST', prayer, credential);
    setPrayers(prev => [result, ...prev]);
  }, [credential]);

  const updatePrayer = useCallback(async (id, data) => {
    const result = await apiFetch(`/prayers/${id}`, 'PUT', data, credential);
    setPrayers(prev => prev.map(p => p.id === id ? result : p));
  }, [credential]);

  const deletePrayer = useCallback(async (id) => {
    await apiFetch(`/prayers/${id}`, 'DELETE', null, credential);
    setPrayers(prev => prev.filter(p => p.id !== id));
  }, [credential]);

  // ── 会议操作 ──────────────────────────────────────────────────────────────
  const addMeeting = useCallback(async (data) => {
    const result = await apiFetch('/meetings', 'POST', data, credential);
    setMeetings(prev => [...prev, result]);
  }, [credential]);

  const updateMeeting = useCallback(async (id, data) => {
    const result = await apiFetch(`/meetings/${id}`, 'PUT', data, credential);
    setMeetings(prev => prev.map(m => m.id === id ? result : m));
  }, [credential]);

  const deleteMeeting = useCallback(async (id) => {
    await apiFetch(`/meetings/${id}`, 'DELETE', null, credential);
    setMeetings(prev => prev.filter(m => m.id !== id));
  }, [credential]);

  return (
    <AppContext.Provider value={{
      // 数据
      members, groups, attendance, notes, prayers, meetings,
      // 状态
      loading, error,
      // 查询
      getMember, getGroup, getMemberNotes, getMembersByGroup,
      // 变更
      addMember, updateMember,
      addGroup, updateGroup,
      addNote,
      recordAttendance,
      addPrayer, updatePrayer, deletePrayer,
      addMeeting, updateMeeting, deleteMeeting,
    }}>
      {children}
    </AppContext.Provider>
  );
}

export const useApp = () => useContext(AppContext);
