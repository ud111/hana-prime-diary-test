<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use Illuminate\View\View;

class DiaryController extends Controller
{
    /** 一覧の 1 ページあたりの件数 (課題仕様: 5 件ごと) */
    public const PER_PAGE = 5;

    /**
     * 日記一覧。新しい日付順に 5 件ずつページ送りする
     */
    public function index(): View
    {
        // 日付の新しい順、同じ日付なら後から登録したものを先に出す
        $diaries = Diary::query()
            ->latestFirst()
            ->paginate(self::PER_PAGE);

        return view('diaries.index', compact('diaries'));
    }
}
