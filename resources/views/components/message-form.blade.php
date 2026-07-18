@props(['action', 'receiverId' => null])
<form method="POST" action="{{ $action }}" class="mb-6">
    @csrf
    @if($receiverId)
        <input type="hidden" name="receiver_id" value="{{ $receiverId }}">
    @endif
    <div class="flex gap-3">
        <input type="text" name="body" required placeholder="{{ __('type_message') }}"
            class="input flex-1" maxlength="5000" aria-label="{{ __('message_aria') }}">
        <button type="submit" class="btn-primary px-6 py-3">
            {{ __('send') }}
        </button>
    </div>
    @error('body') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
</form>
