<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiaryRequest;
use App\Http\Requests\UpdateDiaryRequest;
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
     * 詳細。画像を大きく見せる閲覧用のページで、存在しない ID は 404
     */
    public function show(Diary $diary): View
    {
        return view('diaries.show', compact('diary'));
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
            Diary::deleteImageFile($diary->image_path);
            throw $e;
        }

        return redirect()->route('diaries.index')->with('status', '投稿しました。');
    }

    /**
     * 編集フォーム。存在しない ID は 404
     */
    public function edit(Diary $diary): View
    {
        return view('diaries.edit', compact('diary'));
    }

    /**
     * 編集内容を保存する。画像は「新しい画像を選ぶ」「削除にチェック」「そのまま」の 3 通り
     */
    public function update(UpdateDiaryRequest $request, Diary $diary): RedirectResponse
    {
        $oldPath = $diary->image_path;
        $diary->fill($request->safe()->only(['diary_date', 'content']));

        if ($request->hasFile('image')) {
            // 新しい画像を選んだときは差替。削除チェックが同時に付いていても新しい画像を優先する
            $diary->attachImage($request->file('image'));
        } elseif ($request->boolean('remove_image')) {
            $diary->detachImage();
        }

        try {
            $diary->save();
        } catch (\Throwable $e) {
            // 保存に失敗したら新しく置いたファイルだけ消し、元の画像は残す
            if ($diary->image_path !== $oldPath) {
                Diary::deleteImageFile($diary->image_path);
            }
            throw $e;
        }

        // DB に保存できてから、不要になった元の画像ファイルを消す
        if ($oldPath !== null && $oldPath !== $diary->image_path) {
            Diary::deleteImageFile($oldPath);
        }

        return redirect()->route('diaries.index')->with('status', '更新しました。');
    }

    /**
     * 日記を削除する。画像ファイルはモデルの deleted イベントで消える
     */
    public function destroy(Diary $diary): RedirectResponse
    {
        $diary->delete();

        return redirect()->route('diaries.index')->with('status', '削除しました。');
    }
}
