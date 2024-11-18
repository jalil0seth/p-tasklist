import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faBrain, faRightFromBracket, faBars, faXmark } from '@fortawesome/free-solid-svg-icons';

interface HeaderProps {
  isMobileMenuOpen: boolean;
  setIsMobileMenuOpen: (value: boolean) => void;
  onLogout: () => void;
}

export function Header({ isMobileMenuOpen, setIsMobileMenuOpen, onLogout }: HeaderProps) {
  return (
    <header className="fixed top-0 left-0 right-0 bg-white border-b border-slate-200 z-50">
      <nav className="container mx-auto px-4">
        <div className="flex items-center justify-between h-14">
          <div className="flex items-center gap-2">
            <FontAwesomeIcon icon={faBrain} className="text-blue-600 text-lg" />
            <span className="text-sm font-semibold text-slate-700">TaskMaster AI</span>
          </div>

          {/* Desktop menu */}
          <div className="hidden md:flex items-center">
            <button
              onClick={onLogout}
              className="flex items-center gap-1.5 px-3 py-1.5 text-sm text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-50 transition-colors"
            >
              <FontAwesomeIcon icon={faRightFromBracket} className="text-xs" />
              <span>Logout</span>
            </button>
          </div>

          {/* Mobile menu button */}
          <div className="md:hidden">
            <button
              onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
              className="p-2 text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-50"
            >
              <FontAwesomeIcon 
                icon={isMobileMenuOpen ? faXmark : faBars} 
                className="text-lg" 
              />
            </button>
          </div>
        </div>

        {/* Mobile menu */}
        {isMobileMenuOpen && (
          <div className="md:hidden border-t border-slate-200">
            <div className="px-2 py-2">
              <button
                onClick={onLogout}
                className="flex items-center gap-1.5 w-full px-3 py-1.5 text-sm text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-50 transition-colors"
              >
                <FontAwesomeIcon icon={faRightFromBracket} className="text-xs" />
                <span>Logout</span>
              </button>
            </div>
          </div>
        )}
      </nav>
    </header>
  );
}