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

                    <p>
                        <span class="fi-tracer__red">Trace: </span>
                        <span
                            class="cursor-pointer"
                            x-on:click="
                                window.navigator.clipboard.writeText(@js($fields[0] ?? ''))
                                $tooltip(@js(__('filament-tracer::labels.tooltips.copy_trace')), {
                                    theme: $store.theme,
                                })
                            "
                        >{{ $fields[0] ?? '' }}</span>
                    </p>
                    <p>
                        <span class="fi-tracer__yellow">Path: </span>
                        <span
                            class="cursor-pointer"
                            x-on:click="
                                window.navigator.clipboard.writeText(@js($fields[1] ?? ''))
                                $tooltip(@js(__('filament-tracer::labels.tooltips.copy_path')), {
                                    theme: $store.theme,
                                })
                            "
                        >{{ $fields[1] ?? '' }}</span>
                    </p>
                    <p>
                        <span class="fi-tracer__blue">Caller: </span>
                        <span
                            class="cursor-pointer language-php"
                            x-on:click="
                                window.navigator.clipboard.writeText(@js($fields[2] ?? ''))
                                $tooltip(@js(__('filament-tracer::labels.tooltips.copy_caller')), {
                                    theme: $store.theme,
                                })
                            "
                        >{{ $fields[2] ?? '' }}</span>
                    </p>
                @endif

            </div>
        @empty
            <x-filament-tracer::empty-state />
        @endforelse

    </div>
</x-dynamic-component>
