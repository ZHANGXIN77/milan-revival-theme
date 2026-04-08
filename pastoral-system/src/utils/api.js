import { API_BASE } from '../config';

/**
 * 统一 API 请求函数
 * 自动附加 Google JWT 凭证头，处理非 2xx 响应
 */
export async function apiFetch(path, method = 'GET', body = null, credential = null) {
  const headers = { 'Content-Type': 'application/json' };
  if (credential) headers['X-Google-Credential'] = credential;

  const opts = { method, headers };
  if (body !== null) opts.body = JSON.stringify(body);

  const res = await fetch(`${API_BASE}${path}`, opts);

  if (!res.ok) {
    const err = await res.json().catch(() => ({}));
    throw new Error(err.message || `请求失败 (${res.status})`);
  }

  return res.json();
}
