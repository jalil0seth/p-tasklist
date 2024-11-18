<header class="fixed top-0 left-0 right-0 bg-white border-b border-slate-200 z-50">
    <nav class="container mx-auto px-4">
        <div class="flex items-center justify-between h-14">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-brain text-blue-600 text-lg"></i>
                <span class="text-sm font-semibold text-slate-700">TaskMaster AI</span>
            </div>

            <!-- Desktop menu -->
            <div class="hidden md:flex items-center gap-4">
                <span class="text-sm text-slate-600">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-50 transition-colors"
                    >
                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button
                    type="button"
                    class="p-2 text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-50"
                    onclick="toggleMobileMenu()"
                >
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200">
            <div class="px-2 py-2 space-y-1">
                <span class="block px-3 py-1.5 text-sm text-slate-600">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex items-center gap-1.5 w-full px-3 py-1.5 text-sm text-slate-600 hover:text-slate-900 rounded-md hover:bg-slate-50 transition-colors"
                    >
                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>
</header>

<script>
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenu.classList.toggle('hidden');
}
</script>