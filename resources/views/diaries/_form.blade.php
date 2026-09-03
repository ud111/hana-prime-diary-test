{{-- 新規投稿と編集で共用する入力欄。$diary は編集時のみ渡される (新規は null) --}}
@php($maxLength = \App\Models\Diary::CONTENT_MAX_LENGTH)

<div>
    <label for="diary_date" class="field-label">日付</label>
    <input type="date" id="diary_date" name="diary_date" required class="field-input num sm:w-52"
           value="{{ old('diary_date', $diary?->diary_date?->toDateString() ?? now()->toDateString()) }}">
    @error('diary_date')
        <p class="field-error">{{ $message }}</p>
    @enderror
</div>

<div>
    <div class="mb-2 flex items-baseline justify-between gap-3">
        <label for="content" class="field-label mb-0">本文</label>
        {{-- 残り文字数。JS 無効時は「/ 100」だけ出る --}}
        <span class="num text-[13px] text-on-surface-variant"><span data-content-count>0</span> / {{ $maxLength }}</span>
    </div>
    <input type="text" id="content" name="content" required data-content-input
           maxlength="{{ $maxLength }}" placeholder="今日の実装や気づきを 1 行で"
           class="field-input h-12 text-[17px]"
           value="{{ old('content', $diary?->content) }}">
    <p class="field-hint">{{ $maxLength }} 文字まで。改行は使えません。</p>
    @error('content')
        <p class="field-error">{{ $message }}</p>
    @enderror
</div>

<div>
    <p class="field-label">画像 <span class="ml-1 text-xs font-normal text-on-surface-variant">任意・JPEG・5MB まで・1 枚</span></p>
    @if ($diary?->hasImage())
        {{-- 編集時: 現在の画像。新しい画像を選ぶと差し替わり、チェックで削除できる --}}
        <div class="mb-3 flex items-center gap-4 rounded-lg border border-outline-variant p-3">
            <img class="h-20 w-28 shrink-0 rounded-md object-cover" src="{{ $diary->image_url }}" alt="現在の画像">
            <div class="flex flex-col gap-1.5 text-sm">
                <span class="text-on-surface-variant">現在の画像。下で新しい画像を選ぶと差し替わります。</span>
                <label class="inline-flex items-center gap-2 font-medium text-error">
                    <input type="checkbox" name="remove_image" value="1" class="h-4 w-4 rounded border-outline-variant accent-error" @checked(old('remove_image') === '1')>
                    この画像を削除する
                </label>
            </div>
        </div>
        @error('remove_image')
            <p class="field-error mb-2">{{ $message }}</p>
        @enderror
    @endif
    <label for="image" class="flex cursor-pointer items-center gap-4 rounded-lg border border-dashed border-outline-variant px-4 py-4 transition-colors hover:border-primary hover:bg-primary-fixed/20">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-surface-low text-on-surface-variant">
            <x-icon name="image" class="h-5 w-5"/>
        </span>
        <span class="flex min-w-0 flex-col gap-0.5">
            <span class="text-sm font-medium">{{ $diary?->hasImage() ? '新しい画像を選ぶ' : '画像を選ぶ' }}</span>
            <span class="truncate text-[13px] text-on-surface-variant" data-image-name>ファイルは選ばれていません</span>
        </span>
        <input type="file" id="image" name="image" accept="image/jpeg,.jpg,.jpeg" class="sr-only" data-image-input>
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
                name.textContent = file.files.length ? file.files[0].name : 'ファイルは選ばれていません';
            });
        }
    })();
</script>
