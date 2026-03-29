import { createContext, useContext, useState } from 'react';
import { CURRENT_USER } from '../data/mockData';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(false);

  const login = async (role = 'pastor') => {
    setLoading(true);
    await new Promise(r => setTimeout(r, 800));
    const roles = {
      pastor: { ...CURRENT_USER, role: 'pastor', name: '何恩典' },
      leader: { ...CURRENT_USER, id: 3, role: 'leader', name: '王建国', groupId: 1 },
      youth: { ...CURRENT_USER, id: 2, role: 'youth', name: '李晓雨', groupId: 1 },
    };
    setUser(roles[role]);
    setLoading(false);
  };

  const logout = () => setUser(null);

  return (
    <AuthContext.Provider value={{ user, loading, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export const useAuth = () => useContext(AuthContext);
