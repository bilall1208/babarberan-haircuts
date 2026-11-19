@props(['type' => 'success', 'title', 'text' => null])

<div {{ $attributes->merge(['class' => 'toast toast--' . $type]) }} role="{{ $type === 'error' ? 'alert' : 'status' }}">
    <x-icon :name="$type === 'success' ? 'circle-check' : 'circle-alert'" :size="20" />
    <div class="toast__body">
        <p class="toast__title">{{ $title }}</p>
        @if ($text) <p class="toast__text">{{ $text }}</p> @endif
    </div>
    <button type="button" class="toast__close" data-dismiss aria-label="Tutup">
        <x-icon name="x" :size="16" />
    </button>
</div>