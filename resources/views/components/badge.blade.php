@props([
    'color' => 'primary',
    'disabled' => false,
    'tag' => 'span',
    'type' => 'button',
])


<{{ $tag }}
    {{ $attributes->merge(
            [
                'type' => $tag === 'button' ? $type : null,
            ],
            escape: false,
        )->class([
            'fi-badge flex items-center justify-center gap-x-1 rounded-md font-medium ring-1 ring-inset bg-gray-50 text-gray-600 ring-gray-600/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20',
            'opacity-70' => $disabled,
            match ($color) {
                'gray'
                    => 'bg-gray-50 text-gray-600 ring-gray-600/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20',
                default
                    => 'fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30',
            },
            is_string($color) ? "fi-color-{$color}" : null,
        ]) }}>

    <span class="grid">
        <span class="truncate">
            {{ $slot }}
        </span>
    </span>



    </{{ $tag }}>
