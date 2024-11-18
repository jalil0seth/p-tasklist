<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class LivePollsService
{
    protected string $baseUrl = 'https://api.livepolls.app/api';

    public function generateResponse(string $prompt, bool $isSubtaskGeneration = true): string
    {
        try {
            $systemPrompt = $isSubtaskGeneration
                ? 'You are a task breakdown assistant. Given a task, extract only the direct subtasks needed to complete it. Format your response as a clear, numbered list with 3-5 specific, actionable subtasks. Each subtask should be a direct component of the main task, not general steps or analysis. Keep responses concise and focused.'
                : 'You are a task prioritization assistant. Please analyze and rank the given tasks by importance and urgency.';

            $userContent = $isSubtaskGeneration
                ? "Extract only the direct, specific subtasks needed to complete this task: {$prompt}"
                : $prompt;

            $response = Http::post("{$this->baseUrl}/write-with-role", [
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $userContent
                    ]
                ]
            ]);

            $initialData = $response->json();
            $taskId = $initialData['task_id'];

            // Poll for results
            while (true) {
                $statusResponse = Http::get("{$this->baseUrl}/async-task-result/{$taskId}");
                $statusData = $statusResponse->json();

                if (!($statusData['running'] ?? false)) {
                    $result = json_decode($statusData['result'] ?? '{}', true);
                    return $result['content'] ?? '';
                }

                sleep(1);
            }
        } catch (RequestException $e) {
            \Log::error('LivePolls API error: ' . $e->getMessage());
            throw new \Exception('Failed to generate response');
        }
    }
}