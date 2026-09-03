{{-- XML 宣言は Blade 内に書くと ?> が PHP の終了タグと解釈されるため、コントローラで先頭に付ける --}}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('diaries.index') }}</loc>
        @if ($diaries->isNotEmpty())
            <lastmod>{{ $diaries->max('updated_at')->toAtomString() }}</lastmod>
        @endif
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    @foreach ($diaries as $diary)
        <url>
            <loc>{{ route('diaries.show', $diary) }}</loc>
            <lastmod>{{ $diary->updated_at->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>
