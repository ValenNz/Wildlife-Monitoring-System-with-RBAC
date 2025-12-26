@extends('layouts.app')

@section('title', 'Add User')
@section('page-title', 'Add New User')
@section('page-subtitle', 'Create a new system user account')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-6">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text"
                       name="name"
                       id="name"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter user's full name"
                       value="{{ old('name') }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email"
                       name="email"
                       id="email"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter email address"
                       value="{{ old('email') }}">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password"
                       name="password"
                       id="password"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter password (min 8 characters)">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password"
                       name="password_confirmation"
                       id="password_confirmation"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Confirm password">
            </div>

            <!-- Role Assignment -->
            <div class="mb-6">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">User Role</label>
                <select name="role"
                        id="role"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a role</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="editor" {{ old('role') == 'editor' ? 'selected' : '' }}>Konservasionis Lapangan</option>
                    <option value="peneliti" {{ old('role') == 'peneliti' ? 'selected' : '' }}>Peneliti Ekologi</option>
                    <option value="viewer" {{ old('role') == 'viewer' ? 'selected' : '' }}>Pengambil Kebijakan</option>
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex space-x-4">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Create User
                </button>
                <a href="{{ route('users.index') }}"
                   class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
