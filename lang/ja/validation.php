<?php

// このアプリで使うルールだけを日本語化する。未定義のルールは英語の既定文になる
return [
    'required' => ':attributeは必須です。',
    'string' => ':attributeは文字列で入力してください。',
    'date_format' => ':attributeの形式が正しくありません。',
    'file' => ':attributeのアップロードに失敗しました。',
    'uploaded' => ':attributeのアップロードに失敗しました。ファイルサイズを確認してください。',
    'max' => [
        'string' => ':attributeは:max文字以内で入力してください。',
        'file' => ':attributeは:maxKB以下のファイルを選んでください。',
    ],
    'mimes' => ':attributeは:values形式のファイルを選んでください。',
    'mimetypes' => ':attributeは:values形式のファイルを選んでください。',
    'regex' => ':attributeの形式が正しくありません。',
    'boolean' => ':attributeの値が正しくありません。',

    'attributes' => [],
];
