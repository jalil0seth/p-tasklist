@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col bg-slate-50">
    <div class="container mx-auto px-4 py-6 mt-16">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-slate-200 bg-slate-50/50">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-blue-600 text-lg"></i>
                            <h1 class="text-sm font-semibold text-slate-700">My Tasks</h1>
                        </div>
                        <div class="flex items-center gap-2">
                            <button 
                                onclick="toggleBrainstormModal()"
                                class="px-3 py-1.5 text-xs font-medium bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors flex items-center gap-1.5"
                            >
                                <i class="fa-solid fa-brain text-xs"></i>
                                <span>Brainstorm</span>
                            </button>
                            <button 
                                onclick="toggleTagModal()"
                                class="px-3 py-1.5 text-xs font-medium bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300 transition-colors flex items-center gap-1.5"
                            >
                                <i class="fa-solid fa-tags text-xs"></i>
                                <span>Manage Tags</span>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4" id="tagFilters">
                        <!-- Tag filters will be rendered here -->
                    </div>

                    <form id="addTaskForm" class="flex gap-2">
                        @csrf
                        <input
                            type="text"
                            name="text"
                            placeholder="Add a new task..."
                            class="flex-1 px-3 py-1.5 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder:text-slate-400"
                            required
                        >
                        <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors flex items-center gap-1.5">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>Add</span>
                        </button>
                    </form>
                </div>

                <div class="p-4">
                    <div class="flex justify-end mb-4">
                        <button 
                            id="prioritizeButton"
                            class="px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                        >
                            Prioritize Tasks
                        </button>
                    </div>

                    <div id="tasksList" class="space-y-2">
                        <!-- Tasks will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.brainstorm-modal')
@include('components.tag-modal')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let tags = [];
    let selectedTags = new Set();
    
    // Load initial data
    loadTags();
    loadTasks();

    // API Helper
    async function handleApiCall(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        };

        try {
            const response = await fetch(url, { ...defaultOptions, ...options });
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'API call failed');
            }
            
            return data;
        } catch (error) {
            showToast(error.message, 'error');
            throw error;
        }
    }

    // Loading State Helper
    function setLoading(element, isLoading) {
        const buttonText = element.querySelector('.button-text');
        const loadingSpinner = element.querySelector('.loading-spinner');
        
        element.disabled = isLoading;
        buttonText.classList.toggle('hidden', isLoading);
        loadingSpinner.classList.toggle('hidden', !isLoading);
    }

    // Task Management
    async function loadTasks() {
        try {
            const response = await handleApiCall('{{ route("tasks.index") }}');
            renderTasks(response.tasks);
        } catch (error) {
            showToast('Failed to load tasks', 'error');
        }
    }

    function renderTasks(tasks) {
        const tasksList = document.getElementById('tasksList');
        tasksList.innerHTML = tasks
            .filter(task => selectedTags.size === 0 || task.tags.some(tag => selectedTags.has(tag.id)))
            .map(task => `
                <div class="task-item" data-task-id="${task.id}">
                    <!-- Task rendering logic -->
                </div>
            `).join('');
    }

    document.getElementById('addTaskForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const text = form.querySelector('[name="text"]').value;
        
        try {
            await handleApiCall('{{ route("tasks.store") }}', {
                method: 'POST',
                body: JSON.stringify({ text })
            });
            
            form.reset();
            await loadTasks();
            showToast('Task added successfully');
        } catch (error) {
            showToast('Failed to add task', 'error');
        }
    });

    // Brainstorm Modal Functions
    window.toggleBrainstormModal = function() {
        const modal = document.getElementById('brainstormModal');
        const modalContent = modal.querySelector('.bg-white');
        
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        } else {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    };

    let currentBrainstormResult = null;

    document.getElementById('brainstormForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const submitButton = form.querySelector('button[type="submit"]');
        
        setLoading(submitButton, true);
        
        try {
            const response = await handleApiCall('{{ route("tasks.brainstorm") }}', {
                method: 'POST',
                body: JSON.stringify({
                    taskDescription: form.querySelector('[name="taskDescription"]').value,
                    estimatedTime: form.querySelector('[name="estimatedTime"]').value,
                    priority: form.querySelector('[name="priority"]').value
                })
            });
            
            if (response.success) {
                currentBrainstormResult = response.analysis;
                displayBrainstormResult(response.analysis);
            }
        } finally {
            setLoading(submitButton, false);
        }
    });

    function displayBrainstormResult(analysis) {
        const resultDiv = document.getElementById('brainstormResult');
        const sections = analysis.split(/\d\.\s+(?:Purpose & Goals|Action Steps|Success Criteria):/);
        
        if (sections.length >= 4) {
            document.getElementById('purposeContent').innerHTML = formatSection(sections[1]);
            document.getElementById('stepsContent').innerHTML = formatSection(sections[2]);
            document.getElementById('successContent').innerHTML = formatSection(sections[3]);
            
            resultDiv.classList.remove('hidden');
            document.getElementById('brainstormForm').classList.add('hidden');
        }
    }

    function formatSection(content) {
        return content
            .split('\n')
            .filter(line => line.trim())
            .map(line => `<p class="mb-1">${line.trim().replace(/^-\s*/, '• ')}</p>`)
            .join('');
    }

    window.createTaskFromBrainstorm = function() {
        if (!currentBrainstormResult) return;
        
        const form = document.getElementById('brainstormForm');
        const taskDescription = form.querySelector('[name="taskDescription"]').value;
        
        handleApiCall('{{ route("tasks.store") }}', {
            method: 'POST',
            body: JSON.stringify({ text: taskDescription })
        }).then(() => {
            toggleBrainstormModal();
            loadTasks();
            showToast('Task created successfully');
            resetBrainstorm();
        });
    };

    window.resetBrainstorm = function() {
        document.getElementById('brainstormForm').reset();
        document.getElementById('brainstormForm').classList.remove('hidden');
        document.getElementById('brainstormResult').classList.add('hidden');
        currentBrainstormResult = null;
    };

    // Tag Management
    window.toggleTagModal = function() {
        const modal = document.getElementById('tagModal');
        modal.classList.toggle('hidden');
    };

    async function loadTags() {
        try {
            const response = await handleApiCall('{{ route("tags.index") }}');
            tags = response.tags;
            renderTags();
            renderTagFilters();
        } catch (error) {
            showToast('Failed to load tags', 'error');
        }
    }

    function renderTags() {
        const tagList = document.getElementById('tagList');
        tagList.innerHTML = tags.map(tag => `
            <div class="flex items-center justify-between p-2 bg-slate-50 rounded-md">
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full" style="background-color: ${tag.color}"></span>
                    <span class="text-sm text-slate-700">${tag.name}</span>
                </div>
                <button
                    onclick="deleteTag(${tag.id})"
                    class="text-slate-400 hover:text-red-600"
                >
                    <i class="fa-solid fa-trash text-xs"></i>
                </button>
            </div>
        `).join('');
    }

    function renderTagFilters() {
        const tagFilters = document.getElementById('tagFilters');
        tagFilters.innerHTML = `
            <div class="flex flex-wrap gap-2">
                ${tags.map(tag => `
                    <button
                        onclick="toggleTagFilter(${tag.id})"
                        class="px-2 py-1 text-xs rounded-md flex items-center gap-1 ${
                            selectedTags.has(tag.id)
                                ? 'text-white'
                                : 'text-slate-700 bg-slate-100 hover:bg-slate-200'
                        }"
                        style="${selectedTags.has(tag.id) ? `background-color: ${tag.color}` : ''}"
                    >
                        <span>${tag.name}</span>
                        ${selectedTags.has(tag.id) ? '<i class="fa-solid fa-xmark"></i>' : ''}
                    </button>
                `).join('')}
            </div>
        `;
    }

    window.toggleTagFilter = function(tagId) {
        if (selectedTags.has(tagId)) {
            selectedTags.delete(tagId);
        } else {
            selectedTags.add(tagId);
        }
        renderTagFilters();
        loadTasks();
    };

    // Initialize event listeners
    document.getElementById('prioritizeButton').addEventListener('click', async function() {
        try {
            await handleApiCall('{{ route("tasks.prioritize") }}', { method: 'POST' });
            await loadTasks();
            showToast('Tasks prioritized successfully');
        } catch (error) {
            showToast('Failed to prioritize tasks', 'error');
        }
    });
});
</script>
@endpush
@endsection