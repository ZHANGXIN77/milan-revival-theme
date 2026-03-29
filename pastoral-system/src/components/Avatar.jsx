import { memo } from 'react';

const colors = ['#c9a84c', '#5a8a6e', '#6b7fa3', '#9b6db5', '#c0614e'];

export default memo(function Avatar({ name, src, size = 40 }) {
  const color = colors[((name?.charCodeAt(0) ?? 0)) % colors.length];
  const initials = name ? name.slice(0, 1) : '?';

  return (
    <div style={{
      width: size, height: size, borderRadius: '50%', flexShrink: 0,
      background: src ? 'transparent' : `${color}25`,
      border: `2px solid ${color}50`,
      display: 'flex', alignItems: 'center', justifyContent: 'center',
      fontSize: size * 0.38, fontWeight: 600, color,
      overflow: 'hidden',
    }}>
      {src ? <img src={src} alt={name} style={{ width: '100%', height: '100%', objectFit: 'cover' }} /> : initials}
    </div>
  );
});
