{{-- 一覧の 1 件分 (カード)。画像・日付・本文と、持ち主向けの操作 --}}
<article class="card group flex flex-col gap-4 transition hover:shadow-md sm:flex-row">
    <a href="{{ route('diaries.show', $diary) }}" class="block h-36 w-full shrink-0 overflow-hidden rounded-lg bg-surface-low sm:w-44" aria-label="{{ $diary->diary_date->isoFormat('YYYY年M月D日') }} の日記を見る">
        @if ($diary->hasImage())
            <img class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105" src="{{ $diary->image_url }}" alt="{{ $diary->diary_date->toDateString() }} の写真" loading="lazy">
        @else
            <span class="flex h-full w-full flex-col items-center justify-center gap-1 text-outline">
                <x-icon name="image" class="h-7 w-7 text-outline-variant"/>
                <span class="text-[11px] font-semibold uppercase tracking-wider">No Image</span>
            </span>
        @endif
    </a>
    <div class="flex min-w-0 flex-1 flex-col justify-between gap-3">
        <div class="flex flex-col gap-1.5">
            {{-- 日付が詳細ページへのリンク --}}
            <a href="{{ route('diaries.show', $diary) }}" class="inline-flex items-center gap-1.5 self-start text-xs font-semibold text-on-surface-variant hover:text-primary">
                <x-icon name="calendar" class="h-3.5 w-3.5"/>
                <time datetime="{{ $diary->diary_date->toDateString() }}">{{ $diary->diary_date->isoFormat('YYYY年M月D日(ddd)') }}</time>
            </a>
            <p class="text-base font-medium leading-relaxed text-on-surface break-all sm:text-lg">{{ $diary->content }}</p>
        </div>
        {{-- 編集・削除は持ち主だけ。未ログインには表示しない (URL 直打ちは auth ミドルウェアが止める) --}}
        @auth
            <div class="flex items-center justify-end gap-1 border-t border-outline-variant/30 pt-3">
                <a href="{{ route('diaries.edit', $diary) }}" class="action-link">
                    <x-icon name="pencil" class="h-3.5 w-3.5"/>
                    編集
                </a>
                @include('diaries._delete_form', ['diary' => $diary])
            </div>
        @endauth
    </div>
</article>
