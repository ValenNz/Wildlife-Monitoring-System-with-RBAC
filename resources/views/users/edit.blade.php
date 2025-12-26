@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('page-subtitle', 'Update user account information')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-6">
        <form method="POST" action="{{ route('users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text"
                       name="name"
                       id="name"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter user's full name"
                       value="{{ old('name', $user->name) }}">
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
                       value="{{ old('email', $user->email) }}">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password (opsional) -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password"
                       name="password"
                       id="password"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Leave blank to keep current password">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                <input type="password"
                       name="password_confirmation"
                       id="password_confirmation"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Confirm new password">
            </div>

            <!-- Role Assignment -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">User Role</label>
                <div class="space-y-2">
                    @php
                        $roles = [
                            1 => 'Administrator',
                            2 => 'Konservasionis Lapangan',
                            3 => 'Peneliti Ekologi',
                            4 => 'Pengambil Kebijakan'
                        ];
                    @endphp

                    @foreach($roles as $id => $name)
                    <div class="flex items-center">
                        <input type="radio"
                               name="role_id"
                               id="role_{{ $id }}"
                               value="{{ $id }}"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                               {{ old('role_id', $user->role_id) == $id ? 'checked' : '' }}>
                        <label for="role_{{ $id }}" class="ml-3 block text-sm text-gray-700">
                            {{ $name }}
                        </label>
                    </div>
                    @endforeach
                </div>
                @error('role_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex space-x-4">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Update User
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
