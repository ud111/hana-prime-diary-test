<?php

namespace App\Http\Controllers;

use App\Exceptions\ImageProcessingException;
use App\Http\Requests\StoreDiaryRequest;
use App\Http\Requests\UpdateDiaryRequest;
use App\Models\Diary;
use App\Services\DiaryImageProcessor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiaryController extends Controller
{
    /** 一覧の 1 ページあたりの件数 (課題仕様: 5 件ごと) */
    public const PER_PAGE = 5;

    public function __construct(private readonly DiaryImageProcessor $images) {}

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
        // 前後の日記 (一覧と同じ並び) への導線
        $older = $diary->older();
        $newer = $diary->newer();

        return view('diaries.show', compact('diary', 'older', 'newer'));
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

        return $this->saveWithImage($diary, $request, '投稿しました。');
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
        $diary->fill($request->safe()->only(['diary_date', 'content']));

        return $this->saveWithImage($diary, $request, '更新しました。');
    }

    /**
     * 画像の反映と保存を、新規と編集で共通に行う。
     * 新しい画像があれば保存して軽量版を作り、削除チェックがあれば外す。DB の保存が成功してから不要になった旧ファイルを消す
     */
    private function saveWithImage(Diary $diary, StoreDiaryRequest $request, string $message): RedirectResponse
    {
        // 編集前の画像パス (新規なら null)。保存後に差し替え・削除された旧ファイルを消すために控える
        $oldPath = $diary->getOriginal('image_path');

        try {
            if ($request->hasFile('image')) {
                // 新しい画像を選んだときは差替。削除チェックが同時に付いていても新しい画像を優先する
                $diary->attachImage($request->file('image'));
                $this->images->process($diary);
            } elseif ($request->boolean('remove_image')) {
                $diary->detachImage();
            }
            $diary->save();
        } catch (ImageProcessingException $e) {
            // 読めない・大きすぎる画像は、新しく置いたファイルだけ消して入力エラーとして返す (元の画像は残る)
            $this->discardNewImage($diary, $oldPath);

            return back()->withInput()->withErrors(['image' => $e->getMessage()]);
        } catch (\Throwable $e) {
            // 保存に失敗したら新しく置いたファイルだけ消し、元の画像は残す
            $this->discardNewImage($diary, $oldPath);
            throw $e;
        }

        // DB に保存できてから、不要になった元の画像ファイルを消す
        if ($oldPath !== null && $oldPath !== $diary->image_path) {
            Diary::deleteImageFile($oldPath);
        }

        return redirect()->route('diaries.index')->with('status', $message);
    }

    /**
     * 今回の操作で新しく置いた画像ファイル (軽量版含む) を消す。元からあった画像には触らない
     */
    private function discardNewImage(Diary $diary, ?string $oldPath): void
    {
        if ($diary->image_path !== null && $diary->image_path !== $oldPath) {
            Diary::deleteImageFile($diary->image_path);
        }
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
