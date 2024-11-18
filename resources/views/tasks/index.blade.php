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
                        <button 
                            onclick="toggleTagModal()"
                            class="px-3 py-1.5 text-xs font-medium bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300 transition-colors flex items-center gap-1.5"
                        >
                            <i class="fa-solid fa-tags text-xs"></i>
                            <span>Manage Tags</span>
                        </button>
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

<!-- Tag Management Modal -->
<div id="tagModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-slate-700">Manage Tags</h2>
            <button onclick="toggleTagModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="addTagForm" class="mb-4">
            <div class="flex gap-2">
                <input
                    type="text"
                    name="name"
                    placeholder="Tag name"
                    class="flex-1 px-3 py-1.5 text-sm border rounded-md"
                    required
                >
                <input
                    type="color"
                    name="color"
                    value="#3B82F6"
                    class="w-10 h-10 rounded-md cursor-pointer"
                >
                <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                    Add
                </button>
            </div>
        </form>

        <div id="tagList" class="space-y-2">
            <!-- Tags will be rendered here -->
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let tags = [];
    let selectedTags = new Set();
    
    // Load initial data
    loadTags();
    loadTasks();

    // Tag modal toggle
    window.toggleTagModal = function() {
        const modal = document.getElementById('tagModal');
        modal.classList.toggle('hidden');
    };

    // Add tag form submission
    document.getElementById('addTagForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const name = form.querySelector('input[name="name"]').value;
        const color = form.querySelector('input[name="color"]').value;
        
        try {
            const response = await fetch('{{ route("tags.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ name, color })
            });
            
            if (response.ok) {
                form.reset();
                await loadTags();
                showToast('Tag created successfully');
            }
        } catch (error) {
            showToast('Failed to create tag', 'error');
        }
    });

    async function loadTags() {
        try {
            const response = await fetch('{{ route("tags.index") }}');
            const data = await response.json();
            tags = data.tags;
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

    window.deleteTag = async function(tagId) {
        try {
            const response = await fetch(`/tags/${tagId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            if (response.ok) {
                await loadTags();
                showToast('Tag deleted successfully');
            }
        } catch (error) {
            showToast('Failed to delete tag', 'error');
        }
    };

    window.attachTag = async function(taskId, tagId) {
        try {
            const response = await fetch(`/tasks/${taskId}/tags/${tagId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            if (response.ok) {
                await loadTasks();
                showToast('Tag attached successfully');
            }
        } catch (error) {
            showToast('Failed to attach tag', 'error');
        }
    };

    window.detachTag = async function(taskId, tagId) {
        try {
            const response = await fetch(`/tasks/${taskId}/tags/${tagId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            if (response.ok) {
                await loadTasks();
                showToast('Tag detached successfully');
            }
        } catch (error) {
            showToast('Failed to detach tag', 'error');
        }
    };

    // Rest of your existing JavaScript code...
    // (Keep all the existing task-related functions)
});
</script>
@endpush
@endsection