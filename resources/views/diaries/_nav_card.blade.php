{{-- 詳細ページの前後導線 1 枚分。$target が null なら端の案内。$direction は older (左) / newer (右) --}}
@php($isOlder = $direction === 'older')
@if ($target)
    <a href="{{ route('diaries.show', $target) }}" class="card flex w-full min-w-0 items-center gap-3 overflow-hidden p-3 transition-colors hover:border-outline {{ $isOlder ? '' : 'flex-row-reverse text-right' }}">
        <x-icon name="{{ $isOlder ? 'chevron-left' : 'chevron-right' }}" class="h-4 w-4 shrink-0 text-on-surface-variant"/>
        <img class="h-12 w-16 shrink-0 rounded-md border border-outline-variant bg-surface-low object-cover" src="{{ $target->hasImage() ? $target->image_url : asset('images/no-image.svg') }}" alt="" loading="lazy">
        <span class="flex min-w-0 flex-1 flex-col gap-0.5 {{ $isOlder ? '' : 'items-end' }}">
            <span class="flex items-baseline gap-2 text-xs text-on-surface-variant {{ $isOlder ? '' : 'flex-row-reverse' }}">
                <span>{{ $isOlder ? '前の日記' : '次の日記' }}</span>
                <span class="num">{{ $target->diary_date->isoFormat('YYYY.MM.DD (ddd)') }}</span>
            </span>
            <span class="block w-full truncate text-sm">{{ $target->content }}</span>
        </span>
    </a>
@else
    <span class="card flex w-full min-w-0 items-center gap-3 p-3 text-on-surface-variant {{ $isOlder ? '' : 'flex-row-reverse text-right' }}" aria-disabled="true">
        <x-icon name="{{ $isOlder ? 'chevron-left' : 'chevron-right' }}" class="h-4 w-4 shrink-0"/>
        <span class="text-sm">{{ $isOlder ? 'これより前の日記はありません' : 'これより新しい日記はありません' }}</span>
    </span>
@endif
