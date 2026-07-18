@extends('layouts.app')

@section('title', __('new_project_title'))
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">{{ __('home') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('admin.projects.index') }}" class="text-ink-muted hover:text-accent transition">{{ __('projects') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">{{ __('new_project') }}</span>
</nav>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <form method="POST" action="{{ route('admin.projects.store') }}">
            @csrf

            <div class="mb-4">
                <label for="title" class="label">{{ __('project_name') }} *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                    class="input" placeholder="{{ __('enter_project_name') }}">
                @error('title') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="user_id" class="label">{{ __('client') }} *</label>
                <select id="user_id" name="user_id" required
                    class="input">
                    <option value="">{{ __('select_client') }}</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('user_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }} - {{ $client->email }}
                        </option>
                    @endforeach
                </select>
                @error('user_id') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="label">{{ __('description') }}</label>
                <textarea id="description" name="description" rows="4"
                    class="input" placeholder="{{ __('project_description') }}">{{ old('description') }}</textarea>
                @error('description') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="status" class="label">{{ __('status') }} *</label>
                    <select id="status" name="status" required
                        class="input">
                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>{{ __('pending') }}</option>
                        <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>{{ __('in_progress') }}</option>
                        <option value="review" {{ old('status') === 'review' ? 'selected' : '' }}>{{ __('review') }}</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>{{ __('completed') }}</option>
                        <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>{{ __('cancelled') }}</option>
                    </select>
                    @error('status') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="priority" class="label">{{ __('priority') }} *</label>
                    <select id="priority" name="priority" required
                        class="input">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>{{ __('priority_low') }}</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>{{ __('priority_medium') }}</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>{{ __('priority_high') }}</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>{{ __('priority_urgent') }}</option>
                    </select>
                    @error('priority') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="budget" class="label">{{ __('budget') }} (₪)</label>
                <input type="number" id="budget" name="budget" value="{{ old('budget') }}" min="0" step="0.01"
                    class="input" placeholder="0">
                @error('budget') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="hourly_rate" class="label">{{ __('hourly_rate') }} (₪)</label>
                    <input type="number" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate') }}" min="0" step="0.01"
                        class="input" placeholder="0">
                    @error('hourly_rate') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="estimated_hours" class="label">{{ __('estimated_hours') }}</label>
                    <input type="number" id="estimated_hours" name="estimated_hours" value="{{ old('estimated_hours') }}" min="0" step="0.5"
                        class="input" placeholder="0">
                    @error('estimated_hours') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="label">{{ __('estimated_total') }} (₪)</label>
                    <div class="input bg-gray-50 flex items-center font-semibold" id="total-price-display">-</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="start_date" class="label">{{ __('start_date') }}</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}"
                        class="input">
                    @error('start_date') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="due_date" class="label">{{ __('due_date') }}</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}"
                        class="input">
                    @error('due_date') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="label">{{ __('notes') }}</label>
                <textarea id="notes" name="notes" rows="3"
                    class="input">{{ old('notes') }}</textarea>
                @error('notes') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="btn-primary">
                    {{ __('create') }}
                </button>
                <a href="{{ route('admin.projects.index') }}" class="btn-ghost">
                    {{ __('cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const rateInput = document.getElementById('hourly_rate');
    const hoursInput = document.getElementById('estimated_hours');
    const totalDisplay = document.getElementById('total-price-display');

    function updateTotal() {
        const rate = parseFloat(rateInput.value) || 0;
        const hours = parseFloat(hoursInput.value) || 0;
        totalDisplay.textContent = rate && hours ? '₪' + (rate * hours).toLocaleString('he-IL', {minimumFractionDigits: 2}) : '-';
    }

    rateInput.addEventListener('input', updateTotal);
    hoursInput.addEventListener('input', updateTotal);
</script>
@endpush
@endsection