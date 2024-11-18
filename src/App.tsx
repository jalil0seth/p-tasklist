import React, { useState } from 'react';
import { Toaster } from 'react-hot-toast';
import { TodoItem } from './components/TodoItem';
import { TodoForm } from './components/TodoForm';
import { Header } from './components/Header';
import { Footer } from './components/Footer';
import { Auth } from './components/Auth';
import { FocusTimer } from './components/FocusTimer';
import { useTodos } from './hooks/useTodos';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faListCheck } from '@fortawesome/free-solid-svg-icons';

export default function App() {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const {
    todos,
    loading,
    addTodo,
    updateTodo,
    expandTodo,
    expandSubtask,
    prioritizeTasks,
    toggleTodo,
    deleteTodo,
    increasePriority,
    addSubtask,
    toggleSubtask,
    deleteSubtask,
    updateSubtask
  } = useTodos();

  if (!isAuthenticated) {
    return <Auth onLogin={() => setIsAuthenticated(true)} />;
  }

  return (
    <div className="min-h-screen flex flex-col bg-slate-50">
      <Header 
        isMobileMenuOpen={isMobileMenuOpen}
        setIsMobileMenuOpen={setIsMobileMenuOpen}
        onLogout={() => setIsAuthenticated(false)}
      />

      <main className="flex-1 container mx-auto px-4 py-6 mt-16">
        <div className="max-w-7xl mx-auto">
          <div className="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div className="p-4 border-b border-slate-200 bg-slate-50/50">
              <div className="flex items-center gap-2 mb-4">
                <FontAwesomeIcon icon={faListCheck} className="text-blue-600 text-lg" />
                <h1 className="text-sm font-semibold text-slate-700">My Tasks</h1>
              </div>
              <TodoForm onAdd={addTodo} />
            </div>

            <div className="p-4">
              <div className="flex justify-end mb-4">
                <button
                  onClick={prioritizeTasks}
                  disabled={loading}
                  className="px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {loading ? 'Processing...' : 'Prioritize Tasks'}
                </button>
              </div>

              <div className="space-y-2">
                {todos
                  .sort((a, b) => b.priority - a.priority)
                  .map(todo => (
                    <TodoItem
                      key={todo.id}
                      todo={todo}
                      onToggle={toggleTodo}
                      onDelete={deleteTodo}
                      onExpand={expandTodo}
                      onExpandSubtask={expandSubtask}
                      onPrioritize={increasePriority}
                      onUpdateTodo={updateTodo}
                      onAddSubtask={addSubtask}
                      onToggleSubtask={toggleSubtask}
                      onDeleteSubtask={deleteSubtask}
                      onUpdateSubtask={updateSubtask}
                    />
                  ))}
              </div>
            </div>
          </div>
        </div>
      </main>

      <Footer />
      <FocusTimer />
      <Toaster 
        position="bottom-right"
        toastOptions={{
          className: 'text-sm',
          duration: 3000,
          style: {
            background: 'white',
            color: '#334155',
            boxShadow: '0 1px 3px 0 rgb(0 0 0 / 0.1)',
            border: '1px solid #e2e8f0',
            borderRadius: '0.5rem',
            padding: '0.75rem 1rem',
          },
        }}
      />
    </div>
  );
}