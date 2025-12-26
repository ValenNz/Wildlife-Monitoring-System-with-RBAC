@extends('layouts.app')

@section('title', 'Create Report')
@section('page-title', 'Create New Report')
@section('page-subtitle', 'Configure and generate your report')

@section('content')

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-6">
        <form method="POST" action="{{ route('reports.store') }}">
            @csrf

            <!-- Report Type -->
            <input type="hidden" name="report_type" value="{{ $reportType }}">

            <!-- Title -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Title</label>
                <input type="text"
                       name="title"
                       required
                       value="{{ $reportTypes[$reportType] ?? 'Custom Report' }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date"
                           name="date_from"
                           required
                           value="{{ date('Y-m-d', strtotime('-30 days')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date"
                           name="date_to"
                           required
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Animal Filter (opsional) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Animal (Optional)</label>
                <select name="animal_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Animals</option>
                    @foreach($animals as $animal)
                    <option value="{{ $animal->id }}">{{ $animal->name }} ({{ $animal->species }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="flex space-x-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    Generate Report
                </button>
                <a href="{{ route('reports.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
