{{-- パンくず。$items は [['label' => '日誌一覧', 'url' => '/'], ['label' => '編集']] の形で、最後の要素は現在地 (リンクなし) --}}
@props(['items'])
<nav aria-label="パンくず" class="text-[13px] text-on-surface-variant">
    <ol class="flex flex-wrap items-center gap-1.5">
        @foreach ($items as $item)
            <li class="flex items-center gap-1.5 {{ $loop->last ? 'min-w-0' : '' }}">
                @if ($loop->last)
                    <span class="truncate text-on-surface" aria-current="page">{{ $item['label'] }}</span>
                @else
                    @if (! empty($item['url']))
                        <a href="{{ $item['url'] }}" class="hover:text-primary">{{ $item['label'] }}</a>
                    @else
                        <span>{{ $item['label'] }}</span>
                    @endif
                    <x-icon name="chevron-right" class="h-3.5 w-3.5 text-outline-variant"/>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
