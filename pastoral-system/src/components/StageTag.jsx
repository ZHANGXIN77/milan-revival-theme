import { memo } from 'react';
import { STAGES } from '../data/mockData';

export default memo(function StageTag({ stage, size = 'sm' }) {
  const s = STAGES[stage];
  if (!s) return null;
  return (
    <span style={{
      display: 'inline-flex', alignItems: 'center',
      padding: size === 'sm' ? '2px 10px' : '4px 14px',
      borderRadius: '20px', fontSize: size === 'sm' ? '11px' : '13px',
      fontWeight: 500, whiteSpace: 'nowrap',
      background: s.bgColor, color: s.color,
      border: `1px solid ${s.color}40`,
    }}>{s.name}</span>
  );
});
