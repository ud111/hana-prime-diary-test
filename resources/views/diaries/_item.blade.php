{{-- 一覧の 1 件分。日付・本文・画像 (あれば) を表示する --}}
<li class="diary-item">
    {{-- 日付が詳細ページへのリンク --}}
    <a class="diary-date" href="{{ route('diaries.show', $diary) }}">
        <time datetime="{{ $diary->diary_date->toDateString() }}">{{ $diary->diary_date->isoFormat('YYYY年M月D日(ddd)') }}</time>
    </a>
    <p class="diary-content">{{ $diary->content }}</p>
    {{-- 編集・削除は持ち主だけ。未ログインには表示しない (URL 直打ちは auth ミドルウェアが止める) --}}
    @auth
        <div class="diary-actions">
            <a href="{{ route('diaries.edit', $diary) }}">編集</a>
            {{-- 削除は誤操作を防ぐため確認ダイアログを挟む。JS 無効時はそのまま送信される --}}
            <form method="POST" action="{{ route('diaries.destroy', $diary) }}"
                  onsubmit="return confirm('この日記を削除します。よろしいですか？')">
                @csrf
                @method('DELETE')
                <button type="submit" class="link-button">削除</button>
            </form>
        </div>
    @endauth
    @if ($diary->hasImage())
        <img class="diary-image" src="{{ $diary->image_url }}" alt="{{ $diary->diary_date->toDateString() }} の写真" loading="lazy">
    @endif
</li>
