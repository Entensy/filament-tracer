<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $cookies = $getChangeState();
        $isNullValuesHidden = $hideNullValues();
    @endphp
    <div class="fi-tracer__block">

        @forelse ($cookies as $key => $value)
            @if ($isNullValuesHidden && ($value === null || $value === ''))
                @continue
            @endif
            <div class="fi-tracer__field">

                <p>
                    <span
                        class="cursor-pointer fi-tracer__yellow"
                        x-on:click="
                                window.navigator.clipboard.writeText(@js($key))
                                $tooltip(@js(__('filament-tracer::labels.tooltips.copy_header')), {
                                    theme: $store.theme,
                                })
                            "
                    >{{ $key }}</span>

                <p
                    class="cursor-pointer fi-tracer__black"
                    x-on:click="
                                window.navigator.clipboard.writeText(@js($value))
                                $tooltip(@js(__('filament-tracer::labels.tooltips.copy_value')), {
                                    theme: $store.theme,
                                })
                            "
                >{{ $value ?? '' }}</p>
                </p>

            </div>
        @empty
            <x-filament-tracer::empty-state />
        @endforelse

    </div>
</x-dynamic-component>
