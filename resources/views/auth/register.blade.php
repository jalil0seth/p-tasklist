@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
    <div class="w-full max-w-sm">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex justify-center mb-6">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-brain text-blue-600 text-xl"></i>
                    <h1 class="text-sm font-semibold text-slate-700">TaskMaster AI</h1>
                </div>
            </div>

            <h2 class="text-base font-semibold text-center text-slate-700 mb-6">
                Create an account
            </h2>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-medium text-slate-700 mb-1">
                        Name
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full px-3 py-1.5 text-sm rounded-md border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                        placeholder="Enter your name"
                        required
                        autocomplete="name"
                        autofocus
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-medium text-slate-700 mb-1">
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full px-3 py-1.5 text-sm rounded-md border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                        placeholder="Enter your email"
                        required
                        autocomplete="email"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-slate-700 mb-1">
                        Password
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="w-full px-3 py-1.5 text-sm rounded-md border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                        placeholder="Enter your password"
                        required
                        autocomplete="new-password"
                    >
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-medium text-slate-700 mb-1">
                        Confirm Password
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="w-full px-3 py-1.5 text-sm rounded-md border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Confirm your password"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full py-2 flex items-center justify-center gap-1.5 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors"
                >
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    <span>Sign up</span>
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-xs text-blue-600 hover:text-blue-700">
                        Already have an account? Sign in
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection