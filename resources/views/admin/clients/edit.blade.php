@extends('layouts.app')

@section('title', "עריכת {$client->name} - מנהל")
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">בית</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('admin.clients.index') }}" class="text-ink-muted hover:text-accent transition">לקוחות</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('admin.clients.show', $client) }}" class="text-ink-muted hover:text-accent transition">{{ $client->name }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">עריכה</span>
</nav>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <form method="POST" action="{{ route('admin.clients.update', $client) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="label">שם *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $client->name) }}" required
                    class="input" placeholder="הזן שם לקוח">
                @error('name') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="label">דוא"ל *</label>
                <input type="email" id="email" name="email" value="{{ old('email', $client->email) }}" required
                    class="input" placeholder="הזן דואל">
                @error('email') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="label">טלפון</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $client->phone) }}"
                    class="input" placeholder="הזן טלפון">
                @error('phone') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="company" class="label">חברה</label>
                <input type="text" id="company" name="company" value="{{ old('company', $client->company) }}"
                    class="input" placeholder="הזן שם חברה">
                @error('company') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="address" class="label">כתובת</label>
                <input type="text" id="address" name="address" value="{{ old('address', $client->address) }}"
                    class="input" placeholder="הזן כתובת">
                @error('address') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $client->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-accent border-border rounded focus:ring-accent/30">
                    <span class="text-sm font-medium text-ink">לקוח פעיל</span>
                </label>
                    @error('is_active') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="btn-primary">
                    עדכן לקוח
                </button>
                <a href="{{ route('admin.clients.show', $client) }}" class="btn-ghost">
                    ביטול
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
