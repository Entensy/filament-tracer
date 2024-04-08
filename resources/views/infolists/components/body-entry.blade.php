<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $body = $getChangeState();
    @endphp
    <div class="fi-tracer__block">

        @forelse ($body as $key => $value)
            <div class="fi-tracer__field">

                <p>
                    <span class="cursor-pointer"
                        x-on:click="
                                window.navigator.clipboard.writeText(@js($key))
                                $tooltip(@js(__('filament-tracer::labels.tooltips.copy_key')), {
                                    theme: $store.theme,
                                })
                            ">{{ $key }}
                    </span>
                    <span class="flex cursor-pointer"
                        x-on:click="
                                window.navigator.clipboard.writeText(@js($value))
                                $tooltip(@js(__('filament-tracer::labels.tooltips.copy_value')), {
                                    theme: $store.theme,
                                })
                            ">
                        <x-filament::badge color='warning' class="block">
                            {{ $value }}
                        </x-filament::badge>
                    </span>
                </p>

            </div>
        @empty
            <x-filament-tracer::empty-state />
        @endforelse

    </div>
</x-dynamic-component>
