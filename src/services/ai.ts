import { toast } from 'react-hot-toast';

interface AIResponse {
  task_id: string;
  result?: string;
  running?: boolean;
}

export async function generateAIResponse(prompt: string, isSubtaskGeneration = true): Promise<string> {
  try {
    const systemPrompt = isSubtaskGeneration
      ? 'You are a task breakdown assistant. Given a task, extract only the direct subtasks needed to complete it. Format your response as a clear, numbered list with 3-5 specific, actionable subtasks. Each subtask should be a direct component of the main task, not general steps or analysis. Keep responses concise and focused.'
      : 'You are a task prioritization assistant. Please analyze and rank the given tasks by importance and urgency.';

    const response = await fetch('https://api.livepolls.app/api/write-with-role', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        messages: [
          {
            role: 'system',
            content: systemPrompt
          },
          {
            role: 'user',
            content: isSubtaskGeneration
              ? `Extract only the direct, specific subtasks needed to complete this task: ${prompt}`
              : prompt
          }
        ]
      }),
    });

    const initialData: AIResponse = await response.json();
    const taskId = initialData.task_id;

    // Poll for results
    while (true) {
      const statusResponse = await fetch(`https://api.livepolls.app/api/async-task-result/${taskId}`);
      const statusData: AIResponse = await statusResponse.json();

      if (!statusData.running) {
        const result = JSON.parse(statusData.result || '{}');
        return result.content || '';
      }

      await new Promise(resolve => setTimeout(resolve, 1000));
    }
  } catch (error) {
    toast.error('AI service error. Please try again.');
    throw new Error('Failed to generate response');
  }
}