{{-- Laravel 標準のページネーションを CSS フレームワーク非依存で描画する --}}
@if ($paginator->hasPages())
    <nav class="pagination" aria-label="ページ送り">
        {{-- 前へ --}}
        @if ($paginator->onFirstPage())
            <span class="page-link is-disabled" aria-disabled="true">前へ</span>
        @else
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">前へ</a>
        @endif

        {{-- ページ番号。elements は「数値=>URL の配列」か省略記号 "..." の並び --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="page-link is-disabled" aria-disabled="true">{{ $element }}</span>
            @else
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-link is-current" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- 次へ --}}
        @if ($paginator->hasMorePages())
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">次へ</a>
        @else
            <span class="page-link is-disabled" aria-disabled="true">次へ</span>
        @endif
    </nav>
@endif
