<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $headers = $getChangeState();
    @endphp
    <div class="fi-tracer__block">

        @forelse ($headers as $header => $values)
            <div class="fi-tracer__field">

                <p>
                    <span
                        class="cursor-pointer"
                        x-on:click="
                                window.navigator.clipboard.writeText(@js($header))
                                $tooltip(@js(__('filament-tracer::labels.tooltips.copy_header')), {
                                    theme: $store.theme,
                                })
                            "
                    >{{ $header ?? '-' }}</span>

                    @foreach ($values as $value)
                        <p
                            class="flex cursor-pointer"
                            x-on:click="
                                window.navigator.clipboard.writeText(@js($value))
                                $tooltip(@js(__('filament-tracer::labels.tooltips.copy_value')), {
                                    theme: $store.theme,
                                })
                            "
                        >
                            <x-filament::badge
                                color='success'
                                class="block"
                            >
                                {{ $value ?? '-' }}
                            </x-filament::badge>
                        </p>
                    @endforeach
                </p>

            </div>
        @empty
            <x-filament-tracer::empty-state />
        @endforelse

    </div>
</x-dynamic-component>
