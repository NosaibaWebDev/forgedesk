<!DOCTYPE html>
<html lang="{{ $currentLocale ?? 'he' }}" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - {{ __('too_many_requests') }}</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body style="background:var(--color-bg); color:var(--color-ink); font-family:var(--font-primary), sans-serif; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0;">
    <div class="text-center p-8">
        <h1 class="text-6xl font-bold text-ink mb-4">429</h1>
        <p class="text-lg text-ink-secondary mb-6">{{ __('too_many_requests') }}</p>
        <p class="text-sm text-ink-muted mb-6">{{ __('try_again_later') }}</p>
        <a href="{{ url()->previous(route('login')) }}" class="btn-primary">{{ __('back') }}</a>
    </div>
</body>
</html>
