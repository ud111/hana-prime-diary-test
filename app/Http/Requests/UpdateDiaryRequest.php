<?php

namespace App\Http\Requests;

class UpdateDiaryRequest extends StoreDiaryRequest
{
    /**
     * 編集は新規投稿と同じルールに、「画像を削除」チェックを加える
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return parent::rules() + [
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return parent::attributes() + [
            'remove_image' => '画像の削除',
        ];
    }
}
