import React, { useState } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { 
  faTrash, 
  faWandMagicSparkles, 
  faArrowUp, 
  faPlus, 
  faPen,
  faCheck,
  faXmark
} from '@fortawesome/free-solid-svg-icons';
import { Todo, SubTask } from '../types';

interface TodoItemProps {
  todo: Todo;
  onToggle: (id: string) => void;
  onDelete: (id: string) => void;
  onExpand: (id: string) => void;
  onExpandSubtask: (todoId: string, subtaskId: string) => void;
  onPrioritize: (id: string) => void;
  onUpdateTodo: (id: string, text: string) => void;
  onAddSubtask: (todoId: string, text: string) => void;
  onToggleSubtask: (todoId: string, subtaskId: string) => void;
  onDeleteSubtask: (todoId: string, subtaskId: string) => void;
  onUpdateSubtask: (todoId: string, subtaskId: string, text: string) => void;
}

export function TodoItem({
  todo,
  onToggle,
  onDelete,
  onExpand,
  onExpandSubtask,
  onPrioritize,
  onUpdateTodo,
  onAddSubtask,
  onToggleSubtask,
  onDeleteSubtask,
  onUpdateSubtask,
}: TodoItemProps) {
  const [isEditing, setIsEditing] = useState(false);
  const [editText, setEditText] = useState(todo.text);
  const [newSubtask, setNewSubtask] = useState('');
  const [editingSubtaskId, setEditingSubtaskId] = useState<string | null>(null);
  const [editingSubtaskText, setEditingSubtaskText] = useState('');

  const handleUpdate = () => {
    if (editText.trim()) {
      onUpdateTodo(todo.id, editText.trim());
      setIsEditing(false);
    }
  };

  const handleAddSubtask = (e: React.FormEvent) => {
    e.preventDefault();
    if (newSubtask.trim()) {
      onAddSubtask(todo.id, newSubtask.trim());
      setNewSubtask('');
    }
  };

  const handleUpdateSubtask = (subtaskId: string) => {
    if (editingSubtaskText.trim()) {
      onUpdateSubtask(todo.id, subtaskId, editingSubtaskText.trim());
      setEditingSubtaskId(null);
    }
  };

  return (
    <div className={`bg-white rounded-md border ${
      todo.completed ? 'border-slate-200 bg-slate-50' : 'border-slate-200'
    } ${todo.priority > 0 ? 'border-l-4 border-l-blue-500' : ''}`}>
      <div className="p-3">
        <div className="flex items-center gap-3">
          <input
            type="checkbox"
            checked={todo.completed}
            onChange={() => onToggle(todo.id)}
            className="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
          />
          {isEditing ? (
            <div className="flex-1 flex gap-1.5">
              <input
                type="text"
                value={editText}
                onChange={(e) => setEditText(e.target.value)}
                className="flex-1 text-sm p-1.5 border rounded-md"
                autoFocus
              />
              <button
                onClick={handleUpdate}
                className="p-1.5 text-green-600 hover:bg-green-50 rounded-md"
              >
                <FontAwesomeIcon icon={faCheck} className="text-xs" />
              </button>
              <button
                onClick={() => setIsEditing(false)}
                className="p-1.5 text-red-600 hover:bg-red-50 rounded-md"
              >
                <FontAwesomeIcon icon={faXmark} className="text-xs" />
              </button>
            </div>
          ) : (
            <span className={`flex-1 text-sm ${
              todo.completed ? 'line-through text-slate-400' : 'text-slate-700'
            }`}>
              {todo.text}
            </span>
          )}
          {!isEditing && (
            <div className="flex gap-1">
              <button
                onClick={() => onPrioritize(todo.id)}
                className="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md"
                title="Increase Priority"
              >
                <FontAwesomeIcon icon={faArrowUp} className="text-xs" />
              </button>
              <button
                onClick={() => onExpand(todo.id)}
                className="p-1.5 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-md"
                title="Generate Subtasks"
              >
                <FontAwesomeIcon icon={faWandMagicSparkles} className="text-xs" />
              </button>
              <button
                onClick={() => setIsEditing(true)}
                className="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-md"
                title="Edit"
              >
                <FontAwesomeIcon icon={faPen} className="text-xs" />
              </button>
              <button
                onClick={() => onDelete(todo.id)}
                className="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-md"
                title="Delete"
              >
                <FontAwesomeIcon icon={faTrash} className="text-xs" />
              </button>
            </div>
          )}
        </div>

        {/* Subtasks */}
        <div className="mt-3 pl-7">
          {/* AI Suggestions */}
          {todo.expanded && (
            <div className="mb-3 pl-3 py-2 text-xs text-slate-600 border-l-2 border-purple-300 bg-purple-50/50 rounded-r-md">
              {todo.expanded.split('\n').map((line, index) => (
                <p key={index} className="mb-1 last:mb-0">{line}</p>
              ))}
            </div>
          )}

          {/* Manual Subtasks */}
          <div className="space-y-2">
            {todo.subtasks.map((subtask) => (
              <div key={subtask.id} className="flex flex-col gap-2">
                <div className="flex items-center gap-2 group">
                  <input
                    type="checkbox"
                    checked={subtask.completed}
                    onChange={() => onToggleSubtask(todo.id, subtask.id)}
                    className="h-3.5 w-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                  />
                  {editingSubtaskId === subtask.id ? (
                    <div className="flex-1 flex gap-1.5">
                      <input
                        type="text"
                        value={editingSubtaskText}
                        onChange={(e) => setEditingSubtaskText(e.target.value)}
                        className="flex-1 text-xs p-1 border rounded-md"
                        autoFocus
                      />
                      <button
                        onClick={() => handleUpdateSubtask(subtask.id)}
                        className="p-1 text-green-600 hover:bg-green-50 rounded-md"
                      >
                        <FontAwesomeIcon icon={faCheck} className="text-xs" />
                      </button>
                      <button
                        onClick={() => setEditingSubtaskId(null)}
                        className="p-1 text-red-600 hover:bg-red-50 rounded-md"
                      >
                        <FontAwesomeIcon icon={faXmark} className="text-xs" />
                      </button>
                    </div>
                  ) : (
                    <>
                      <span className={`flex-1 text-xs ${
                        subtask.completed ? 'line-through text-slate-400' : 'text-slate-600'
                      }`}>
                        {subtask.text}
                      </span>
                      <div className="opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                        <button
                          onClick={() => onExpandSubtask(todo.id, subtask.id)}
                          className="p-1 text-slate-400 hover:text-purple-600 rounded-md"
                          title="Expand Subtask"
                        >
                          <FontAwesomeIcon icon={faWandMagicSparkles} className="text-xs" />
                        </button>
                        <button
                          onClick={() => {
                            setEditingSubtaskId(subtask.id);
                            setEditingSubtaskText(subtask.text);
                          }}
                          className="p-1 text-slate-400 hover:text-amber-600 rounded-md"
                        >
                          <FontAwesomeIcon icon={faPen} className="text-xs" />
                        </button>
                        <button
                          onClick={() => onDeleteSubtask(todo.id, subtask.id)}
                          className="p-1 text-slate-400 hover:text-red-600 rounded-md"
                        >
                          <FontAwesomeIcon icon={faTrash} className="text-xs" />
                        </button>
                      </div>
                    </>
                  )}
                </div>
                {subtask.expanded && (
                  <div className="ml-5 pl-3 py-2 text-xs text-slate-600 border-l-2 border-purple-300 bg-purple-50/50 rounded-r-md">
                    {subtask.expanded.split('\n').map((line, index) => (
                      <p key={index} className="mb-1 last:mb-0">{line}</p>
                    ))}
                  </div>
                )}
              </div>
            ))}
          </div>

          {/* Add Subtask Form */}
          <form onSubmit={handleAddSubtask} className="mt-2 flex gap-1.5">
            <input
              type="text"
              value={newSubtask}
              onChange={(e) => setNewSubtask(e.target.value)}
              placeholder="Add a subtask..."
              className="flex-1 text-xs p-1.5 border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/50"
            />
            <button
              type="submit"
              className="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md"
            >
              <FontAwesomeIcon icon={faPlus} className="text-xs" />
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}