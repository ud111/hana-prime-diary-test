{{-- 新規投稿と編集で共用する入力欄。$diary は編集時のみ渡される (新規は null) --}}
<div class="field">
    <label for="diary_date">日付</label>
    <input type="date" id="diary_date" name="diary_date" required
           value="{{ old('diary_date', $diary?->diary_date?->toDateString() ?? now()->toDateString()) }}">
    @error('diary_date')
        <p class="error">{{ $message }}</p>
    @enderror
</div>

<div class="field">
    <label for="content">本文（{{ \App\Models\Diary::CONTENT_MAX_LENGTH }}文字まで・1行）</label>
    <input type="text" id="content" name="content" required
           maxlength="{{ \App\Models\Diary::CONTENT_MAX_LENGTH }}"
           value="{{ old('content', $diary?->content) }}">
    @error('content')
        <p class="error">{{ $message }}</p>
    @enderror
</div>

<div class="field">
    @if ($diary?->hasImage())
        {{-- 編集時: 現在の画像。新しい画像を選ぶと差し替わり、チェックで削除できる --}}
        <p class="field-label">現在の画像</p>
        <img class="diary-image" src="{{ $diary->image_url }}" alt="現在の画像">
        <label class="checkbox">
            <input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))>
            画像を削除する
        </label>
        @error('remove_image')
            <p class="error">{{ $message }}</p>
        @enderror
    @endif
    <label for="image">{{ $diary?->hasImage() ? '新しい画像に差し替える' : '画像' }}（jpg・5MBまで・1枚）</label>
    <input type="file" id="image" name="image" accept="image/jpeg,.jpg,.jpeg">
    @error('image')
        <p class="error">{{ $message }}</p>
    @enderror
</div>
