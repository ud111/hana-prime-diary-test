{{-- 一覧の 1 件分 (カード)。日付・本文・画像と、持ち主向けの操作 --}}
<article class="card flex flex-col gap-4 p-5 transition-shadow hover:shadow-md hover:shadow-black/5 sm:flex-row sm:gap-6 sm:p-6">
    {{-- 画像は左。無い場合はダミー画像を出してカードの形を揃える --}}
    <a href="{{ route('diaries.show', $diary) }}" class="block shrink-0 overflow-hidden rounded-xl sm:rounded-lg border border-outline-variant bg-surface-low sm:w-40 focus:outline-none focus-visible:ring-4 focus-visible:ring-primary/20" aria-label="{{ $diary->diary_date->isoFormat('YYYY年M月D日') }} の日記を見る">
        <img class="aspect-video sm:aspect-square h-auto min-h-full w-full object-cover" src="{{ $diary->hasImage() ? $diary->image_url : asset('images/no-image.svg') }}" alt="{{ $diary->hasImage() ? $diary->diary_date->toDateString().' の写真' : '' }}" loading="lazy">
    </a>
    <div class="flex min-w-0 flex-1 flex-col gap-1 sm:gap-3">
        {{-- 日付が詳細ページへのリンク --}}
        <a href="{{ route('diaries.show', $diary) }}" class="inline-flex items-baseline gap-2 self-start rounded text-on-surface-variant hover:text-primary focus:outline-none focus-visible:ring-4 focus-visible:ring-primary/20">
            <time datetime="{{ $diary->diary_date->toDateString() }}" class="num text-[13px] font-semibold text-on-surface-variant">{{ $diary->diary_date->isoFormat('YYYY.MM.DD') }}</time>
            <span class="text-xs">{{ $diary->diary_date->isoFormat('ddd') }}曜日</span>
        </a>
        <p class="text-[20px] sm:text-[24px] font-bold tracking-wide sm:tracking-wider leading-normal sm:leading-normal text-on-surface break-all">{{ $diary->content }}</p>
        {{-- 編集・削除は持ち主だけ。未ログインには表示しない (URL 直打ちは auth ミドルウェアが止める) --}}
        @auth
            <div class="mt-auto flex items-center gap-1 pt-1 -ml-2.5">
                <a href="{{ route('diaries.edit', $diary) }}" class="action-link">
                    <x-icon name="pencil" class="h-3.5 w-3.5"/>
                    編集
                </a>
                @include('diaries._delete_form', ['diary' => $diary])
            </div>
        @endauth
    </div>
</article>
