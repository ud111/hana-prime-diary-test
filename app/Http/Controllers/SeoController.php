<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SeoController extends Controller
{
    /**
     * robots.txt。書き込み系とログインは巡回対象外にし、sitemap の場所を知らせる
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /login',
            'Disallow: /logout',
            'Disallow: /diaries/create',
            'Disallow: /diaries/*/edit',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * sitemap.xml。トップと公開中の詳細ページ。1 時間キャッシュする
     */
    public function sitemap(): Response
    {
        $xml = Cache::remember('sitemap.xml', now()->addHour(), function () {
            $diaries = Diary::query()->latestFirst()->get(['id', 'updated_at']);

            return '<?xml version="1.0" encoding="UTF-8"?>'."\n".view('seo.sitemap', compact('diaries'))->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
