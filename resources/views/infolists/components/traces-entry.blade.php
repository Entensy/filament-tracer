<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $traces = $getChangeState();
    @endphp
    <div class="fi-tracer__block">

        @forelse ($traces as $trace)
            <div class="fi-tracer__field fi-tracer__field--strip">

                @if (!$doesSourceContain('php'))
                    <p>{{ is_array($trace) ? print_r($trace) : $trace }}</p>
                @else
                    @php
                        $fields = $splitTrace($trace);
                    @endphp

                    <p class="flex gap-2">
                        <span class="fi-tracer__danger">{{ __('filament-tracer::labels.trace') }}</span>
                        <span
                            class="cursor-pointer"
                            x-on:click="
                            window.navigator.clipboard.writeText(@js($fields[0] ?? ''))
                            $tooltip(@js(__('filament-tracer::labels.tooltips.copy_trace')), {
                                theme: $store.theme,
                            })
                        "
                        >
                            <x-filament::badge
                                color='danger'
                                class="block"
                            >
                                {{ $fields[0] ?? '-' }}
                            </x-filament::badge>
                        </span>
                    </p>

                    <p class="flex gap-2">
                        <span class="fi-tracer__warning">{{ __('filament-tracer::labels.path') }}</span>
                        <span
                            class="cursor-pointer"
                            x-on:click="
                            window.navigator.clipboard.writeText(@js($fields[1] ?? ''))
                            $tooltip(@js(__('filament-tracer::labels.tooltips.copy_path')), {
                                theme: $store.theme,
                            })
                        "
                        >
                            <x-filament::badge
                                color='warning'
                                class="block"
                            >
                                {{ $fields[1] ?? '-' }}
                            </x-filament::badge>
                        </span>
                    </p>

                    <p class="flex gap-2">
                        <span class="fi-tracer__info">{{ __('filament-tracer::labels.caller') }} </span>
                        <span
                            class="cursor-pointer language-php"
                            x-on:click="
                                window.navigator.clipboard.writeText(@js($fields[2] ?? ''))
                                $tooltip(@js(__('filament-tracer::labels.tooltips.copy_caller')), {
                                    theme: $store.theme,
                                })
                            "
                        >
                            <x-filament::badge
                                color='info'
                                class="block"
                            >
                                {{ $fields[2] ?? '-' }}
                            </x-filament::badge>

                        </span>
                    </p>
                @endif

            </div>
        @empty
            <x-filament-tracer::empty-state />
        @endforelse

    </div>
</x-dynamic-component>
