@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }} style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; list-style: none; padding: 0;">
        @foreach ((array) $messages as $message)
            <li style="color: #dc2626; background-color: #fee2e2; padding: 0.5rem; border-radius: 0.25rem; margin-bottom: 0.25rem;">{{ $message }}</li>
        @endforeach
    </ul>
@endif
