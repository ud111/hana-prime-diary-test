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
    {{-- 1 行日記だが、長い文を書きやすいように見た目はテキストエリア。改行は JS で防ぎ、サーバー側でも弾く --}}
    <textarea id="content" name="content" required data-content-input rows="2"
              maxlength="{{ $maxLength }}" placeholder="例: N+1 を 1 か所つぶした。ログを見る癖がついてきた。"
              class="field-input h-auto resize-none py-3 text-[17px] leading-relaxed">{{ old('content', $diary?->content) }}</textarea>
    <p class="field-hint">{{ $maxLength }} 文字まで。改行は使えません。</p>
    @error('content')
        <p class="field-error">{{ $message }}</p>
    @enderror
</div>

<div>
    <p class="field-label">画像 <span class="ml-1 text-xs font-normal text-on-surface-variant">任意・JPEG・5MB まで・1 枚</span></p>
    @if ($diary?->hasImage())
        {{-- 編集時: 現在の画像。新しい画像を選ぶと差し替わり、チェックで削除できる --}}
        <div class="mb-3 flex items-center gap-4 rounded-lg border border-outline-variant p-3" data-current-image>
            <img class="h-20 w-28 shrink-0 rounded-md object-cover" src="{{ $diary->image_url }}" alt="現在の画像">
            <div class="flex flex-col gap-1.5 text-sm">
                <span class="text-on-surface-variant" data-current-note>現在の画像。下で新しい画像を選ぶと差し替わります。</span>
                <label class="inline-flex items-center gap-2 font-medium text-error">
                    <input type="checkbox" name="remove_image" value="1" class="h-4 w-4 rounded border-outline-variant accent-error" data-remove-image @checked(old('remove_image') === '1')>
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
    {{-- 選んだ画像のプレビュー (JS で表示。送信前に取り違えに気づけるように) --}}
    <div class="mt-3 flex items-start gap-4 rounded-lg border border-outline-variant bg-surface-low p-3" data-preview hidden>
        <img class="h-24 w-32 shrink-0 rounded-md object-cover" src="" alt="選択した画像のプレビュー" data-preview-img>
        <div class="flex min-w-0 flex-1 flex-col gap-1.5 text-sm">
            <span class="font-medium" data-preview-title>{{ $diary?->hasImage() ? 'この画像に差し替えます' : 'この画像を添付します' }}</span>
            <span class="truncate text-[13px] text-on-surface-variant" data-preview-name></span>
            <span class="text-[13px] text-error" data-preview-warning hidden></span>
            <button type="button" class="action-link self-start -ml-2.5" data-preview-clear>
                <x-icon name="x" class="h-3.5 w-3.5"/>
                選択を解除
            </button>
        </div>
    </div>
    @error('image')
        <p class="field-error">{{ $message }}</p>
    @enderror
</div>

{{-- 残り文字数、選んだ画像のプレビュー (無くても送信できる、補助的な JS。検証はサーバー側が本体) --}}
<script>
    (function () {
        var input = document.querySelector('[data-content-input]');
        var count = document.querySelector('[data-content-count]');
        if (input && count) {
            var update = function () { count.textContent = Array.from(input.value).length; };
            // 1 行日記なので改行は入れない。Enter は無効にし、貼り付けで入った改行は取り除く
            input.addEventListener('keydown', function (e) { if (e.key === 'Enter') e.preventDefault(); });
            input.addEventListener('input', function () {
                if (/[\r\n]/.test(input.value)) input.value = input.value.replace(/[\r\n]+/g, ' ');
                update();
            });
            update();
        }

        var file = document.querySelector('[data-image-input]');
        var name = document.querySelector('[data-image-name]');
        var preview = document.querySelector('[data-preview]');
        if (!file || !preview) return;
        var img = preview.querySelector('[data-preview-img]');
        var previewName = preview.querySelector('[data-preview-name]');
        var warning = preview.querySelector('[data-preview-warning]');
        var clear = preview.querySelector('[data-preview-clear]');
        var current = document.querySelector('[data-current-image]');
        var currentNote = document.querySelector('[data-current-note]');
        var removeCheck = document.querySelector('[data-remove-image]');
        var objectUrl = null;
        var maxBytes = 5 * 1024 * 1024;

        var reset = function () {
            if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = null; }
            img.removeAttribute('src');
            preview.hidden = true;
            warning.hidden = true;
            if (name) name.textContent = 'ファイルは選ばれていません';
            // 編集画面: 「現在の画像」の表示を元に戻す
            if (current) { current.classList.remove('opacity-50'); }
            if (currentNote) { currentNote.textContent = '現在の画像。下で新しい画像を選ぶと差し替わります。'; }
            if (removeCheck) { removeCheck.disabled = false; }
        };

        file.addEventListener('change', function () {
            reset();
            var selected = file.files && file.files[0];
            if (!selected) return;
            if (name) name.textContent = selected.name;
            previewName.textContent = selected.name + '（' + (selected.size / 1024 / 1024).toFixed(2) + ' MB）';
            // その場で分かる範囲の注意 (最終的な検証はサーバー側)
            var problems = [];
            if (selected.type !== 'image/jpeg') problems.push('JPEG 形式のファイルを選んでください。');
            if (selected.size > maxBytes) problems.push('5MB 以下のファイルを選んでください。');
            if (problems.length) { warning.textContent = problems.join(' '); warning.hidden = false; }
            if (selected.type.indexOf('image/') === 0) {
                objectUrl = URL.createObjectURL(selected);
                img.src = objectUrl;
            }
            preview.hidden = false;
            // 編集画面: 新しい画像を選んだら「現在の画像」は差し替え対象になり、削除チェックは意味を持たないので外す
            if (current) { current.classList.add('opacity-50'); }
            if (currentNote) { currentNote.textContent = '下で選んだ画像に差し替わります。'; }
            if (removeCheck) { removeCheck.checked = false; removeCheck.disabled = true; }
        });

        clear.addEventListener('click', function () {
            file.value = '';
            reset();
        });
    })();
</script>
