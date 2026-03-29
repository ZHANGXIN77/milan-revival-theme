import { createContext, useContext, useState, useEffect, useCallback } from 'react';
import { GOOGLE_CLIENT_ID, API_BASE, PASTOR_EMAIL } from '../config';

const AuthContext = createContext(null);

function decodeJwt(token) {
  const base64 = token.split('.')[1].replace(/-/g, '+').replace(/_/g, '/');
  return JSON.parse(atob(base64));
}

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [credential, setCredential] = useState(null); // 保存 Google credential 用于 API 调用

  // 向 WordPress 注册/验证用户
  const registerWithApi = useCallback(async (payload, cred) => {
    try {
      const res = await fetch(`${API_BASE}/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          email: payload.email,
          name: payload.name || '',
          avatar: payload.picture || '',
        }),
      });
      const data = await res.json();

      if (data.authorized) {
        const u = data.user;
        setUser({
          id: u.memberId || 0,
          name: u.name || payload.name || payload.email,
          email: payload.email.toLowerCase(),
          avatar: u.avatar || payload.picture || null,
          role: u.role,
          groupId: u.groupId || null,
        });
        setCredential(cred);
        localStorage.setItem('pastoral_credential', cred);
        return true;
      }
      return false;
    } catch {
      // API 不可用时，牧区长仍可本地登录
      if (payload.email.toLowerCase() === PASTOR_EMAIL) {
        setUser({
          id: 0,
          name: payload.name || PASTOR_EMAIL,
          email: PASTOR_EMAIL,
          avatar: payload.picture || null,
          role: 'pastor',
          groupId: null,
        });
        setCredential(cred);
        localStorage.setItem('pastoral_credential', cred);
        return true;
      }
      return false;
    }
  }, []);

  // Google 登录回调
  const handleCredentialResponse = useCallback(async (response) => {
    setError(null);
    const payload = decodeJwt(response.credential);
    const authorized = await registerWithApi(payload, response.credential);

    if (!authorized) {
      setError('您的账号正在等待牧区长审批，请稍后再试。');
      setUser(null);
      localStorage.removeItem('pastoral_credential');
    }
  }, [registerWithApi]);

  // 初始化 Google Identity Services + 恢复会话
  useEffect(() => {
    const initGoogle = () => {
      if (!window.google?.accounts?.id) {
        setTimeout(initGoogle, 200);
        return;
      }

      window.google.accounts.id.initialize({
        client_id: GOOGLE_CLIENT_ID,
        callback: handleCredentialResponse,
        auto_select: true,
      });

      // 恢复会话
      const saved = localStorage.getItem('pastoral_credential');
      if (saved) {
        try {
          const payload = decodeJwt(saved);
          if (payload.exp * 1000 > Date.now()) {
            registerWithApi(payload, saved).then((ok) => {
              if (!ok) localStorage.removeItem('pastoral_credential');
              setLoading(false);
            });
            return;
          }
        } catch { /* token 无效 */ }
        localStorage.removeItem('pastoral_credential');
      }

      setLoading(false);
    };

    initGoogle();
  }, [handleCredentialResponse, registerWithApi]);

  const logout = useCallback(() => {
    setUser(null);
    setError(null);
    setCredential(null);
    localStorage.removeItem('pastoral_credential');
    if (window.google?.accounts?.id) {
      window.google.accounts.id.disableAutoSelect();
    }
  }, []);

  return (
    <AuthContext.Provider value={{ user, loading, error, credential, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export const useAuth = () => useContext(AuthContext);
