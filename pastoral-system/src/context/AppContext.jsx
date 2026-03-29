import { createContext, useContext, useState } from 'react';
import { MEMBERS, GROUPS, ATTENDANCE, NOTES, PRAYER_REQUESTS, MEETINGS } from '../data/mockData';

const AppContext = createContext(null);

export function AppProvider({ children }) {
  const [members, setMembers] = useState(MEMBERS);
  const [groups, setGroups] = useState(GROUPS);
  const [attendance, setAttendance] = useState(ATTENDANCE);
  const [notes, setNotes] = useState(NOTES);
  const [prayers, setPrayers] = useState(PRAYER_REQUESTS);
  const [meetings, setMeetings] = useState(MEETINGS);

  const getMember = (id) => members.find(m => m.id === id);
  const getGroup = (id) => groups.find(g => g.id === id);
  const getMemberNotes = (memberId) => notes.filter(n => n.memberId === memberId).sort((a,b) => new Date(b.date) - new Date(a.date));
  const getMembersByGroup = (groupId) => members.filter(m => m.groupId === groupId);

  const addMember = (data) => {
    const newId = members.reduce((max, m) => Math.max(max, m.id), 0) + 1;
    const newMember = {
      id: newId, avatar: null, englishName: '', isGroupLeader: false,
      baptized: false, baptizeDate: null, mbti: null, gdprConsent: false,
      status: '活跃', stage: 0,
      ...data,
    };
    setMembers(prev => [...prev, newMember]);
    return newMember;
  };

  const updateMember = (id, data) => {
    setMembers(prev => prev.map(m => m.id === id ? { ...m, ...data } : m));
  };

  const updateGroup = (id, data) => {
    setGroups(prev => prev.map(g => g.id === id ? { ...g, ...data } : g));
  };

  const addGroup = (data) => {
    const newId = groups.reduce((max, g) => Math.max(max, g.id), 0) + 1;
    const newGroup = { id: newId, leaderId: null, description: '', ...data };
    setGroups(prev => [...prev, newGroup]);
    return newGroup;
  };

  const addNote = (note) => {
    const newNote = { ...note, id: Date.now(), date: new Date().toISOString() };
    setNotes(prev => [newNote, ...prev]);
    return newNote;
  };

  const recordAttendance = (date, records) => {
    setAttendance(prev => {
      const idx = prev.findIndex(a => a.date === date);
      if (idx >= 0) {
        const updated = [...prev];
        updated[idx] = { ...updated[idx], records };
        return updated;
      }
      return [{ id: Date.now(), date, records }, ...prev];
    });
  };

  const addPrayer = (prayer) => {
    const newPrayer = { authorId: 0, ...prayer, id: Date.now(), date: new Date().toISOString().split('T')[0] };
    setPrayers(prev => [newPrayer, ...prev]);
  };

  const updatePrayer = (id, data) => {
    setPrayers(prev => prev.map(p => p.id === id ? { ...p, ...data } : p));
  };

  const addMeeting = (data) => {
    const newId = meetings.reduce((max, m) => Math.max(max, m.id), 0) + 1;
    setMeetings(prev => [...prev, { id: newId, notes: '', ...data }]);
  };

  const updateMeeting = (id, data) => {
    setMeetings(prev => prev.map(m => m.id === id ? { ...m, ...data } : m));
  };

  const deleteMeeting = (id) => {
    setMeetings(prev => prev.filter(m => m.id !== id));
  };

  return (
    <AppContext.Provider value={{
      members, groups, attendance, notes, prayers, meetings,
      getMember, getGroup, getMemberNotes, getMembersByGroup,
      addMember, updateMember, addGroup, updateGroup, addNote, recordAttendance, addPrayer, updatePrayer,
      addMeeting, updateMeeting, deleteMeeting,
    }}>
      {children}
    </AppContext.Provider>
  );
}

export const useApp = () => useContext(AppContext);
