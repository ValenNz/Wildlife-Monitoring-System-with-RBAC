@extends('layouts.app')

@section('title', 'Login')
@section('page-title', 'Sign in to your account')
@section('page-subtitle', 'Access wildlife monitoring system')

@section('content')
<div class="max-w-md w-full mx-auto">
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-8">
            <div class="mx-auto bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-2.548 6.4-5.5 7.185C4.548 18.4 3 16.76 3 15c0-3.333 2.667-6 6-6h1c3.333 0 6 2.667 6 6 0 3.333-2.667 6-6 6z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10c0-2.21-1.79-4-4-4s-4 1.79-4 4 1.79 4 4 4 4-1.79 4-4z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
            <p class="text-gray-600 mt-2">Sign in to continue to Wildlife Monitoring</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email"
                       name="email"
                       id="email"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="you@example.com"
                       value="{{ old('email') }}">
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                </div>
                <input type="password"
                       name="password"
                       id="password"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center mb-6">
                <input type="checkbox"
                       name="remember"
                       id="remember"
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-700">
                    Remember me
                </label>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                Sign in
            </button>
        </form>
    </div>
</div>
@endsection
