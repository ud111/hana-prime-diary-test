{{-- 使う分だけのインライン SVG アイコン (線画、currentColor)。外部フォントは読み込まない --}}
@props(['name'])
<svg {{ $attributes->merge(['class' => 'h-4 w-4']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@switch($name)
    @case('plus') <path d="M12 5v14M5 12h14"/> @break
    @case('pencil') <path d="M4 20h4L18 10l-4-4L4 16v4z"/><path d="M13 7l4 4"/> @break
    @case('trash') <path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/> @break
    @case('image') <rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 16l5-5 4 4 3-3 6 6"/><circle cx="16" cy="9" r="1.5"/> @break
    @case('chevron-left') <path d="M15 6l-6 6 6 6"/> @break
    @case('chevron-right') <path d="M9 6l6 6-6 6"/> @break
    @case('logout') <path d="M10 17l5-5-5-5M15 12H3"/><path d="M21 4v16"/> @break
    @case('check-circle') <circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/> @break
    @case('x') <path d="M6 6l12 12M18 6L6 18"/> @break
    @case('link') <path d="M10 14a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1"/><path d="M14 10a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/> @break
@endswitch
</svg>
