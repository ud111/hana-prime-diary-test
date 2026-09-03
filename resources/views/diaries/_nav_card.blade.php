{{-- 詳細ページの前後導線 1 枚分。$target が null なら端の案内。$direction は older (左) / newer (右) --}}
@php($isOlder = $direction === 'older')
@if ($target)
    <a href="{{ route('diaries.show', $target) }}" class="card flex items-center gap-3 p-3 transition-colors hover:border-outline {{ $isOlder ? '' : 'flex-row-reverse text-right' }}">
        <x-icon name="{{ $isOlder ? 'chevron-left' : 'chevron-right' }}" class="h-4 w-4 shrink-0 text-on-surface-variant"/>
        <img class="h-12 w-16 shrink-0 rounded-md border border-outline-variant bg-surface-low object-cover" src="{{ $target->hasImage() ? $target->image_url : asset('images/no-image.svg') }}" alt="" loading="lazy">
        <span class="flex min-w-0 flex-col gap-0.5">
            <span class="text-xs text-on-surface-variant">{{ $isOlder ? '前の日記' : '次の日記' }}</span>
            <span class="num text-[13px] font-semibold">{{ $target->diary_date->isoFormat('YYYY.MM.DD (ddd)') }}</span>
            <span class="truncate text-sm">{{ $target->content }}</span>
        </span>
    </a>
@else
    <span class="card flex items-center gap-3 p-3 text-on-surface-variant {{ $isOlder ? '' : 'flex-row-reverse text-right' }}" aria-disabled="true">
        <x-icon name="{{ $isOlder ? 'chevron-left' : 'chevron-right' }}" class="h-4 w-4 shrink-0"/>
        <span class="text-sm">{{ $isOlder ? 'これより前の日記はありません' : 'これより新しい日記はありません' }}</span>
    </span>
@endif
