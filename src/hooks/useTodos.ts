import { useState, useCallback } from 'react';
import { Todo, SubTask } from '../types';
import { generateAIResponse } from '../services/ai';
import { toast } from 'react-hot-toast';

export function useTodos() {
  const [todos, setTodos] = useState<Todo[]>([]);
  const [loading, setLoading] = useState(false);

  const addTodo = useCallback((text: string) => {
    const newTodo: Todo = {
      id: Date.now().toString(),
      text,
      completed: false,
      priority: 0,
      subtasks: []
    };
    setTodos(prev => [...prev, newTodo]);
  }, []);

  const updateTodo = useCallback((id: string, text: string) => {
    setTodos(prev => prev.map(todo =>
      todo.id === id ? { ...todo, text } : todo
    ));
  }, []);

  const addSubtask = useCallback((todoId: string, text: string) => {
    const newSubtask: SubTask = {
      id: Date.now().toString(),
      text,
      completed: false,
      expanded: ''
    };
    setTodos(prev => prev.map(todo =>
      todo.id === todoId
        ? { ...todo, subtasks: [...todo.subtasks, newSubtask] }
        : todo
    ));
  }, []);

  const expandSubtask = useCallback(async (todoId: string, subtaskId: string) => {
    setLoading(true);
    const todo = todos.find(t => t.id === todoId);
    if (!todo) return;

    const subtask = todo.subtasks.find(s => s.id === subtaskId);
    if (!subtask) return;

    try {
      const expandedText = await generateAIResponse(subtask.text);
      setTodos(prev => prev.map(t => 
        t.id === todoId ? {
          ...t,
          subtasks: t.subtasks.map(s =>
            s.id === subtaskId ? { ...s, expanded: expandedText } : s
          )
        } : t
      ));
      toast.success('Subtask expanded successfully!');
    } catch (error) {
      toast.error('Failed to expand subtask. Please try again.');
    } finally {
      setLoading(false);
    }
  }, [todos]);

  const toggleSubtask = useCallback((todoId: string, subtaskId: string) => {
    setTodos(prev => prev.map(todo =>
      todo.id === todoId
        ? {
            ...todo,
            subtasks: todo.subtasks.map(subtask =>
              subtask.id === subtaskId
                ? { ...subtask, completed: !subtask.completed }
                : subtask
            )
          }
        : todo
    ));
  }, []);

  const deleteSubtask = useCallback((todoId: string, subtaskId: string) => {
    setTodos(prev => prev.map(todo =>
      todo.id === todoId
        ? {
            ...todo,
            subtasks: todo.subtasks.filter(subtask => subtask.id !== subtaskId)
          }
        : todo
    ));
  }, []);

  const updateSubtask = useCallback((todoId: string, subtaskId: string, text: string) => {
    setTodos(prev => prev.map(todo =>
      todo.id === todoId
        ? {
            ...todo,
            subtasks: todo.subtasks.map(subtask =>
              subtask.id === subtaskId ? { ...subtask, text } : subtask
            )
          }
        : todo
    ));
  }, []);

  const expandTodo = useCallback(async (id: string) => {
    setLoading(true);
    const todo = todos.find(t => t.id === id);
    if (!todo) return;

    try {
      const expandedText = await generateAIResponse(todo.text);
      setTodos(prev => prev.map(t => 
        t.id === id ? { ...t, expanded: expandedText } : t
      ));
      toast.success('Task expanded successfully!');
    } catch (error) {
      toast.error('Failed to expand task. Please try again.');
    } finally {
      setLoading(false);
    }
  }, [todos]);

  const prioritizeTasks = useCallback(async () => {
    const activeTodos = todos.filter(t => !t.completed);
    if (activeTodos.length === 0) return;

    setLoading(true);
    try {
      const todoTexts = activeTodos.map(t => t.text).join('\n');
      const prompt = `Please prioritize these tasks from 1 (highest) to ${activeTodos.length} (lowest) based on typical urgency and importance. Return only numbers separated by commas:\n${todoTexts}`;
      
      const response = await generateAIResponse(prompt, false);
      const priorities = response.split(',').map(Number);
      
      if (priorities) {
        setTodos(prev => prev.map((todo, idx) => ({
          ...todo,
          priority: !todo.completed ? priorities[activeTodos.findIndex(t => t.id === todo.id)] : 0
        })));
        toast.success('Tasks prioritized successfully!');
      }
    } catch (error) {
      toast.error('Failed to prioritize tasks. Please try again.');
    } finally {
      setLoading(false);
    }
  }, [todos]);

  const toggleTodo = useCallback((id: string) => {
    setTodos(prev => prev.map(t => 
      t.id === id ? { ...t, completed: !t.completed } : t
    ));
  }, []);

  const deleteTodo = useCallback((id: string) => {
    setTodos(prev => prev.filter(t => t.id !== id));
  }, []);

  const increasePriority = useCallback((id: string) => {
    setTodos(prev => prev.map(t => 
      t.id === id ? { ...t, priority: t.priority + 1 } : t
    ));
  }, []);

  return {
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
  };
}