<!DOCTYPE html>
<html lang="{{ $currentLocale ?? 'he' }}" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('confirm_password') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="min-h-screen flex items-center justify-center" style="background:var(--color-surface);">
    <div class="w-full max-w-md p-8">
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-accent rounded-btn flex items-center justify-center text-white font-bold text-lg mx-auto mb-4">F</div>
            <h1 class="text-xl font-bold" style="color:var(--color-ink);">{{ __('confirm_password') }}</h1>
            <p class="text-sm mt-2" style="color:var(--color-ink-secondary);">{{ __('confirm_password_desc') }}</p>
        </div>

        @if($errors->any())
            <div class="border px-5 py-3.5 rounded-card mb-6 text-sm font-medium" style="background:rgba(239,68,68,.08); border-color:rgba(239,68,68,.2); color:var(--color-danger);">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            <div class="mb-4">
                <label for="password" class="label">{{ __('password') }}</label>
                <input type="password" id="password" name="password" required autofocus
                    class="input" placeholder="{{ __('enter_password') }}">
            </div>
            <button type="submit" class="btn-primary w-full py-3">
                {{ __('confirm_password') }}
            </button>
        </form>
    </div>
</body>
</html>
