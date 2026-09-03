{{-- 新規投稿と編集で共用する入力欄。$diary は編集時のみ渡される (新規は null) --}}
@php($maxLength = \App\Models\Diary::CONTENT_MAX_LENGTH)

<div>
    <label for="diary_date" class="field-label">日付</label>
    <input type="date" id="diary_date" name="diary_date" required class="field-input sm:w-56"
           value="{{ old('diary_date', $diary?->diary_date?->toDateString() ?? now()->toDateString()) }}">
    @error('diary_date')
        <p class="field-error">{{ $message }}</p>
    @enderror
</div>

<div>
    <div class="mb-1.5 flex items-end justify-between gap-3">
        <label for="content" class="field-label mb-0">1行日記の本文 <span class="ml-1 rounded bg-error-container px-1.5 py-0.5 text-[11px] font-semibold text-on-error-container">必須</span></label>
        {{-- 残り文字数。JS 無効時は「/ 100文字」だけ出る --}}
        <span class="text-xs text-on-surface-variant"><span data-content-count>0</span> / {{ $maxLength }}文字</span>
    </div>
    <input type="text" id="content" name="content" required data-content-input
           maxlength="{{ $maxLength }}" placeholder="今日の実装・気づき・学びを1行で残す…"
           class="field-input py-3 text-base"
           value="{{ old('content', $diary?->content) }}">
    <p class="field-hint">改行は使えません。{{ $maxLength }}文字以内で書きます。</p>
    @error('content')
        <p class="field-error">{{ $message }}</p>
    @enderror
</div>

<div>
    @if ($diary?->hasImage())
        {{-- 編集時: 現在の画像。新しい画像を選ぶと差し替わり、チェックで削除できる --}}
        <p class="field-label">添付画像</p>
        <div class="flex flex-col gap-3 rounded-lg border border-outline-variant bg-surface-low p-3 sm:flex-row sm:items-center">
            <img class="h-24 w-32 shrink-0 rounded-md object-cover" src="{{ $diary->image_url }}" alt="現在の画像">
            <div class="flex flex-col gap-2 text-sm">
                <span class="text-on-surface-variant">現在の画像。新しい画像を選ぶと差し替わります。</span>
                <label class="inline-flex items-center gap-2 text-sm font-medium text-error">
                    <input type="checkbox" name="remove_image" value="1" class="h-4 w-4 rounded border-outline-variant accent-error" @checked(old('remove_image') === '1')>
                    画像を削除する
                </label>
            </div>
        </div>
        @error('remove_image')
            <p class="field-error">{{ $message }}</p>
        @enderror
        <label for="image" class="field-label mt-4">新しい画像に差し替える <span class="ml-1 text-xs font-normal text-on-surface-variant">JPG・5MBまで・1枚</span></label>
    @else
        <label for="image" class="field-label">写真・画像の添付 <span class="ml-1 rounded bg-surface-container px-1.5 py-0.5 text-[11px] font-semibold text-on-surface-variant">任意</span> <span class="ml-1 text-xs font-normal text-on-surface-variant">JPG・5MBまで・1枚</span></label>
    @endif
    <label for="image" class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-outline-variant bg-surface-low px-4 py-8 text-center transition hover:border-primary hover:bg-primary-fixed/30">
        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-surface-lowest text-primary shadow-sm">
            <x-icon name="image" class="h-5 w-5"/>
        </span>
        <span class="text-sm font-medium">クリックして写真を選択</span>
        <span class="text-xs text-on-surface-variant">JPEG 形式のファイルに対応</span>
        <input type="file" id="image" name="image" accept="image/jpeg,.jpg,.jpeg" class="sr-only" data-image-input>
        <span class="text-xs text-primary" data-image-name></span>
    </label>
    @error('image')
        <p class="field-error">{{ $message }}</p>
    @enderror
</div>

{{-- 残り文字数と選択したファイル名の表示 (無くても送信できる、補助的な JS) --}}
<script>
    (function () {
        var input = document.querySelector('[data-content-input]');
        var count = document.querySelector('[data-content-count]');
        if (input && count) {
            var update = function () { count.textContent = Array.from(input.value).length; };
            input.addEventListener('input', update);
            update();
        }
        var file = document.querySelector('[data-image-input]');
        var name = document.querySelector('[data-image-name]');
        if (file && name) {
            file.addEventListener('change', function () {
                name.textContent = file.files.length ? file.files[0].name : '';
            });
        }
    })();
</script>
