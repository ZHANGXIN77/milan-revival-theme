import { useState, useEffect } from 'react';

export function useSuccessMessage(duration = 3000) {
  const [message, setMessage] = useState('');
  useEffect(() => {
    if (!message) return;
    const t = setTimeout(() => setMessage(''), duration);
    return () => clearTimeout(t);
  }, [message, duration]);
  return [message, setMessage];
}
