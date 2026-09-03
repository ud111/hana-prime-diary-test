{{-- Laravel 標準のページネーションを、表示件数の案内付きで描画する --}}
@if ($paginator->hasPages())
    <nav class="flex flex-col items-center gap-3 pt-3 sm:flex-row sm:justify-between" aria-label="ページ送り">
        <p class="order-2 text-[13px] text-on-surface-variant sm:order-1">
            全 <span class="num font-semibold text-on-surface">{{ $paginator->total() }}</span> 件中
            <span class="num font-semibold text-on-surface">{{ $paginator->firstItem() }}〜{{ $paginator->lastItem() }}</span> 件を表示
        </p>
        <div class="order-1 flex items-center gap-1 sm:order-2">
            {{-- 前へ --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-9 items-center gap-1 rounded-full px-3 text-[13px] font-medium text-outline-variant" aria-disabled="true"><x-icon name="chevron-left" class="h-4 w-4"/>前へ</span>
            @else
                <a class="inline-flex h-9 items-center gap-1 rounded-full px-3 text-[13px] font-medium text-on-surface hover:bg-surface-low" href="{{ $paginator->previousPageUrl() }}" rel="prev"><x-icon name="chevron-left" class="h-4 w-4"/>前へ</a>
            @endif

            {{-- ページ番号。elements は「数値=>URL の配列」か省略記号 "..." の並び --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="num px-1 text-[13px] text-outline" aria-disabled="true">{{ $element }}</span>
                @else
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="num flex h-9 w-9 items-center justify-center rounded-full bg-primary text-[13px] font-semibold text-on-primary" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="num flex h-9 w-9 items-center justify-center rounded-full text-[13px] font-medium text-on-surface hover:bg-surface-low" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- 次へ --}}
            @if ($paginator->hasMorePages())
                <a class="inline-flex h-9 items-center gap-1 rounded-full px-3 text-[13px] font-medium text-on-surface hover:bg-surface-low" href="{{ $paginator->nextPageUrl() }}" rel="next">次へ<x-icon name="chevron-right" class="h-4 w-4"/></a>
            @else
                <span class="inline-flex h-9 items-center gap-1 rounded-full px-3 text-[13px] font-medium text-outline-variant" aria-disabled="true">次へ<x-icon name="chevron-right" class="h-4 w-4"/></span>
            @endif
        </div>
    </nav>
@endif
