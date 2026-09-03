{{--
    削除フォーム (一覧・詳細・編集で共用)。誤操作を防ぐため確認ダイアログを挟む。JS 無効時はそのまま送信される。
    $label はボタンの文言、$iconOnly が true ならアイコンだけの丸ボタンにする
--}}
@props(['diary', 'label' => '削除', 'iconOnly' => false])
<form method="POST" action="{{ route('diaries.destroy', $diary) }}"
      onsubmit="return confirm('この日記を削除します。よろしいですか？')" class="shrink-0">
    @csrf
    @method('DELETE')
    @if ($iconOnly)
        <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-full text-error transition hover:bg-error-container/60" title="{{ $label }}" aria-label="{{ $label }}">
            <x-icon name="trash" class="h-4 w-4"/>
        </button>
    @else
        <button type="submit" class="action-link action-link-danger">
            <x-icon name="trash" class="h-3.5 w-3.5"/>
            {{ $label }}
        </button>
    @endif
</form>
