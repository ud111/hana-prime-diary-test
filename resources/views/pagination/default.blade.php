{{-- Laravel 標準のページネーションを Stitch のデザイン (白いバー、丸いページ番号) で描画する --}}
@if ($paginator->hasPages())
    <nav class="card flex flex-col items-center justify-between gap-4 px-6 py-4 shadow-sm sm:flex-row" aria-label="ページ送り">
        <p class="order-2 text-[13px] text-on-surface-variant sm:order-1">
            全 <strong class="num font-semibold text-on-surface">{{ $paginator->total() }}</strong> 件中
            <strong class="num font-semibold text-on-surface">{{ $paginator->firstItem() }}〜{{ $paginator->lastItem() }}</strong> 件を表示
        </p>
        <div class="order-1 flex items-center gap-1 sm:order-2">
            {{-- 前へ --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex cursor-not-allowed items-center gap-1 rounded-full px-3 py-1.5 text-[13px] font-medium text-outline-variant" aria-disabled="true"><x-icon name="chevron-left" class="h-4 w-4"/>前へ</span>
            @else
                <a class="inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-[13px] font-medium text-on-surface transition-colors hover:bg-surface-container" href="{{ $paginator->previousPageUrl() }}" rel="prev"><x-icon name="chevron-left" class="h-4 w-4"/>前へ</a>
            @endif

            {{-- ページ番号。elements は「数値=>URL の配列」か省略記号 "..." の並び --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="num flex h-8 w-8 select-none items-center justify-center text-[13px] text-outline" aria-disabled="true">…</span>
                @else
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="num flex h-8 w-8 items-center justify-center rounded-full bg-ink text-[13px] font-medium text-white shadow-sm" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="num flex h-8 w-8 items-center justify-center rounded-full text-[13px] font-medium text-on-surface transition-colors hover:bg-surface-container" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- 次へ --}}
            @if ($paginator->hasMorePages())
                <a class="inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-[13px] font-medium text-on-surface transition-colors hover:bg-surface-container" href="{{ $paginator->nextPageUrl() }}" rel="next">次へ<x-icon name="chevron-right" class="h-4 w-4"/></a>
            @else
                <span class="inline-flex cursor-not-allowed items-center gap-1 rounded-full px-3 py-1.5 text-[13px] font-medium text-outline-variant" aria-disabled="true">次へ<x-icon name="chevron-right" class="h-4 w-4"/></span>
            @endif
        </div>
    </nav>
@endif
