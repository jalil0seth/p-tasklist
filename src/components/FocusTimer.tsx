import React, { useState, useEffect } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faPlay, faPause, faStop, faClock } from '@fortawesome/free-solid-svg-icons';

export function FocusTimer() {
  const [time, setTime] = useState(25 * 60); // 25 minutes in seconds
  const [isActive, setIsActive] = useState(false);
  const [showCustom, setShowCustom] = useState(false);
  const [customMinutes, setCustomMinutes] = useState('25');

  useEffect(() => {
    let interval: NodeJS.Timeout;

    if (isActive && time > 0) {
      interval = setInterval(() => {
        setTime(time => time - 1);
      }, 1000);
    } else if (time === 0) {
      setIsActive(false);
    }

    return () => clearInterval(interval);
  }, [isActive, time]);

  const toggleTimer = () => {
    setIsActive(!isActive);
  };

  const resetTimer = () => {
    setIsActive(false);
    setTime(parseInt(customMinutes) * 60);
  };

  const formatTime = (seconds: number) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  };

  const handleCustomTime = (e: React.FormEvent) => {
    e.preventDefault();
    setTime(parseInt(customMinutes) * 60);
    setShowCustom(false);
  };

  return (
    <div className="fixed bottom-4 right-4 bg-white rounded-lg shadow-lg border border-slate-200 p-4 z-50">
      <div className="flex items-center gap-2 mb-2">
        <FontAwesomeIcon icon={faClock} className="text-blue-600" />
        <span className="text-sm font-medium text-slate-700">Focus Timer</span>
      </div>
      
      <div className="text-2xl font-mono font-bold text-slate-700 mb-3 text-center">
        {formatTime(time)}
      </div>

      <div className="flex items-center gap-2 mb-2">
        <button
          onClick={toggleTimer}
          className="flex-1 px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center gap-1"
        >
          <FontAwesomeIcon icon={isActive ? faPause : faPlay} />
          {isActive ? 'Pause' : 'Start'}
        </button>
        <button
          onClick={resetTimer}
          className="px-3 py-1.5 text-xs font-medium bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300 transition-colors flex items-center justify-center gap-1"
        >
          <FontAwesomeIcon icon={faStop} />
          Reset
        </button>
      </div>

      <button
        onClick={() => setShowCustom(!showCustom)}
        className="w-full text-xs text-blue-600 hover:text-blue-700"
      >
        Set custom time
      </button>

      {showCustom && (
        <form onSubmit={handleCustomTime} className="mt-2">
          <div className="flex gap-2">
            <input
              type="number"
              value={customMinutes}
              onChange={(e) => setCustomMinutes(e.target.value)}
              className="flex-1 px-2 py-1 text-xs border rounded-md"
              min="1"
              max="60"
            />
            <button
              type="submit"
              className="px-2 py-1 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Set
            </button>
          </div>
        </form>
      )}
    </div>
  );
}