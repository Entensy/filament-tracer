<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $queries = $getChangeState();
    @endphp
    <div class="fi-tracer__block">

        @forelse ($queries as $query)
            <div class="fi-tracer__field">

                @if (!$doesSourceContain('php'))
                    <p>{{ is_array($query) ? print_r($query) : $query }}</p>
                @else
                    @php
                        $sql = $joinQueryWithBindings($query['sql'], $query['bindings']);
                    @endphp
                    <p>
                        <span class="fi-tracer__upper">{{ $query['connection_name'] }}</span>
                        <span class="fi-tracer__danger">{{ $query['time'] }}ms</span>
                    </p>

                    <p class="cursor-pointer language-sql"
                        x-on:click="
                                window.navigator.clipboard.writeText(@js($sql))
                                $tooltip(@js(__('filament-tracer::labels.tooltips.copy_sql')), {
                                    theme: $store.theme,
                                })
                            ">
                        {{ $sql }}
                    </p>
                @endif

            </div>
        @empty
            <x-filament-tracer::empty-state />
        @endforelse

    </div>
</x-dynamic-component>
