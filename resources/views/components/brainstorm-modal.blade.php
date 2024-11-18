<div id="brainstormModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl transform transition-all duration-300 scale-95 opacity-0">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-slate-700">Task Brainstorming</h2>
            <button onclick="toggleBrainstormModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="brainstormForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Task Description</label>
                <textarea
                    name="taskDescription"
                    rows="3"
                    class="w-full px-3 py-2 text-sm border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Describe your task in detail..."
                    required
                ></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Estimated Time (minutes)</label>
                    <input
                        type="number"
                        name="estimatedTime"
                        min="1"
                        class="w-full px-3 py-1.5 text-sm border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g., 30"
                        required
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Priority Level</label>
                    <select
                        name="priority"
                        class="w-full px-3 py-1.5 text-sm border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                data-loading="false"
            >
                <span class="button-text">Analyze Task</span>
                <span class="loading-spinner hidden">
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                </span>
            </button>
        </form>

        <div id="brainstormResult" class="mt-6 hidden">
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <h3 class="text-sm font-semibold text-blue-800 mb-2">Purpose & Goals</h3>
                    <div id="purposeContent" class="text-sm text-blue-700"></div>
                </div>

                <div class="p-4 bg-purple-50 rounded-lg border border-purple-100">
                    <h3 class="text-sm font-semibold text-purple-800 mb-2">Action Steps</h3>
                    <div id="stepsContent" class="text-sm text-purple-700"></div>
                </div>

                <div class="p-4 bg-green-50 rounded-lg border border-green-100">
                    <h3 class="text-sm font-semibold text-green-800 mb-2">Success Criteria</h3>
                    <div id="successContent" class="text-sm text-green-700"></div>
                </div>

                <div class="flex justify-end gap-2">
                    <button
                        onclick="createTaskFromBrainstorm()"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors"
                    >
                        Create Task
                    </button>
                    <button
                        onclick="resetBrainstorm()"
                        class="px-4 py-2 bg-slate-200 text-slate-700 text-sm rounded-md hover:bg-slate-300 transition-colors"
                    >
                        Start Over
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>