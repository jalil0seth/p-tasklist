<div class="fixed bottom-4 right-4 bg-white rounded-lg shadow-lg border border-slate-200 p-4 z-50">
    <div class="flex items-center gap-2 mb-2">
        <i class="fa-solid fa-clock text-blue-600"></i>
        <span class="text-sm font-medium text-slate-700">Focus Timer</span>
    </div>
    
    <div id="timer" class="text-2xl font-mono font-bold text-slate-700 mb-3 text-center">
        25:00
    </div>

    <div class="flex items-center gap-2 mb-2">
        <button
            onclick="toggleTimer()"
            id="toggleButton"
            class="flex-1 px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center gap-1"
        >
            <i class="fa-solid fa-play"></i>
            <span>Start</span>
        </button>
        <button
            onclick="resetTimer()"
            class="px-3 py-1.5 text-xs font-medium bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300 transition-colors flex items-center justify-center gap-1"
        >
            <i class="fa-solid fa-stop"></i>
            Reset
        </button>
    </div>

    <button
        onclick="toggleCustomTime()"
        class="w-full text-xs text-blue-600 hover:text-blue-700"
    >
        Set custom time
    </button>

    <form id="customTimeForm" class="hidden mt-2" onsubmit="setCustomTime(event)">
        <div class="flex gap-2">
            <input
                type="number"
                id="customMinutes"
                value="25"
                class="flex-1 px-2 py-1 text-xs border rounded-md"
                min="1"
                max="60"
            >
            <button
                type="submit"
                class="px-2 py-1 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
                Set
            </button>
        </div>
    </form>
</div>

<script>
let timeLeft = 25 * 60;
let timerId = null;
let isRunning = false;

function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

function updateDisplay() {
    document.getElementById('timer').textContent = formatTime(timeLeft);
    const button = document.getElementById('toggleButton');
    button.innerHTML = isRunning 
        ? '<i class="fa-solid fa-pause"></i><span>Pause</span>'
        : '<i class="fa-solid fa-play"></i><span>Start</span>';
}

function toggleTimer() {
    isRunning = !isRunning;
    if (isRunning) {
        timerId = setInterval(() => {
            if (timeLeft > 0) {
                timeLeft--;
                updateDisplay();
            } else {
                isRunning = false;
                clearInterval(timerId);
                showToast('Time is up!', 'success');
            }
        }, 1000);
    } else {
        clearInterval(timerId);
    }
    updateDisplay();
}

function resetTimer() {
    isRunning = false;
    clearInterval(timerId);
    timeLeft = parseInt(document.getElementById('customMinutes').value) * 60;
    updateDisplay();
}

function toggleCustomTime() {
    const form = document.getElementById('customTimeForm');
    form.classList.toggle('hidden');
}

function setCustomTime(e) {
    e.preventDefault();
    const minutes = parseInt(document.getElementById('customMinutes').value);
    timeLeft = minutes * 60;
    updateDisplay();
    toggleCustomTime();
}

updateDisplay();
</script>