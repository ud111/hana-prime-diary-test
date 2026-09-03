<?php

namespace App\Http\Requests;

use App\Models\Diary;
use App\Services\DiaryImageProcessor;
use Illuminate\Foundation\Http\FormRequest;

class StoreDiaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 投稿できるのはログイン済みの持ち主だけだが、その制御はルートの auth ミドルウェアで行う
        return true;
    }

    /**
     * 新規投稿のバリデーション。編集 (#7) でも同じルールを使う
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            // <input type="date"> の値は Y-m-d 固定
            'diary_date' => ['required', 'date_format:Y-m-d'],
            // 1 行日記なので改行は禁止。文字数は上限を定数で共有
            'content' => ['required', 'string', 'max:'.Diary::CONTENT_MAX_LENGTH, 'regex:/\A[^\r\n]*\z/u'],
            // jpg のみ。拡張子だけでなく実ファイルの MIME も検査し、5MB (5120KB) まで
            // 寸法の上限は巨大な画像でメモリを使い切らないため (DiaryImageProcessor::MAX_DIMENSION と同じ値)
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg', 'mimetypes:image/jpeg', 'max:5120', 'dimensions:max_width='.DiaryImageProcessor::MAX_DIMENSION.',max_height='.DiaryImageProcessor::MAX_DIMENSION],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'diary_date' => '日付',
            'content' => '本文',
            'image' => '画像',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'content.regex' => ':attributeに改行は使えません。',
            'image.mimes' => ':attributeは jpg 形式のファイルを選んでください。',
            'image.mimetypes' => ':attributeは jpg 形式のファイルを選んでください。',
            'image.dimensions' => ':attributeは縦横それぞれ '.DiaryImageProcessor::MAX_DIMENSION.'px 以内の画像を選んでください。',
        ];
    }
}
