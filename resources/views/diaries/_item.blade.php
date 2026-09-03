{{-- 一覧の 1 件分。日付・本文・画像 (あれば) を表示する --}}
<li class="diary-item">
    <time class="diary-date" datetime="{{ $diary->diary_date->toDateString() }}">
        {{ $diary->diary_date->isoFormat('YYYY年M月D日(ddd)') }}
    </time>
    <p class="diary-content">{{ $diary->content }}</p>
    <div class="diary-actions">
        <a href="{{ route('diaries.edit', $diary) }}">編集</a>
    </div>
    @if ($diary->hasImage())
        <img class="diary-image" src="{{ $diary->image_url }}" alt="{{ $diary->diary_date->toDateString() }} の写真" loading="lazy">
    @endif
</li>
