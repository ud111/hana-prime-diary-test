{{-- 使う分だけのインライン SVG アイコン (線画、currentColor)。外部フォントは読み込まない --}}
@props(['name'])
<svg {{ $attributes->merge(['class' => 'h-4 w-4']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@switch($name)
    @case('plus') <path d="M12 5v14M5 12h14"/> @break
    @case('pencil') <path d="M4 20h4L18 10l-4-4L4 16v4z"/><path d="M13 7l4 4"/> @break
    @case('trash') <path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/> @break
    @case('image') <rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 16l5-5 4 4 3-3 6 6"/><circle cx="16" cy="9" r="1.5"/> @break
    @case('arrow-left') <path d="M19 12H5M11 6l-6 6 6 6"/> @break
    @case('chevron-left') <path d="M15 6l-6 6 6 6"/> @break
    @case('chevron-right') <path d="M9 6l6 6-6 6"/> @break
    @case('login') <path d="M14 7l5 5-5 5M19 12H7"/><path d="M5 4v16"/> @break
    @case('logout') <path d="M10 17l5-5-5-5M15 12H3"/><path d="M21 4v16"/> @break
    @case('check-circle') <circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/> @break
    @case('code') <path d="M8 8l-4 4 4 4M16 8l4 4-4 4M14 4l-4 16"/> @break
    @case('calendar') <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/> @break
    @case('x') <path d="M6 6l12 12M18 6L6 18"/> @break
    @case('link') <path d="M10 14a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1"/><path d="M14 10a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/> @break
    @case('share') <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/> @break
@endswitch
</svg>
