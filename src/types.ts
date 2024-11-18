export interface SubTask {
  id: string;
  text: string;
  completed: boolean;
  expanded?: string;
}

export interface Todo {
  id: string;
  text: string;
  completed: boolean;
  priority: number;
  subtasks: SubTask[];
  expanded?: string;
}