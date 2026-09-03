<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiaryRequest;
use App\Models\Diary;
use Illuminate\Http\RedirectResponse;
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

    /**
     * 新規投稿フォーム
     */
    public function create(): View
    {
        return view('diaries.create');
    }

    /**
     * 新規投稿を保存する
     */
    public function store(StoreDiaryRequest $request): RedirectResponse
    {
        $diary = new Diary($request->safe()->only(['diary_date', 'content']));

        // 画像は任意。あれば public ディスクに保存してからレコードを作る
        if ($request->hasFile('image')) {
            $diary->attachImage($request->file('image'));
        }

        try {
            $diary->save();
        } catch (\Throwable $e) {
            // 保存に失敗したら、先に置いた画像ファイルを残さない
            $diary->removeImage();
            throw $e;
        }

        return redirect()->route('diaries.index')->with('status', '投稿しました。');
    }
}
