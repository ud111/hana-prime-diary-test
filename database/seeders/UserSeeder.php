<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /** 動作確認用の持ち主アカウント (README に記載。公開環境では必ず変更する) */
    public const EMAIL = 'admin@example.com';

    public const PASSWORD = 'password';

    /**
     * 持ち主のユーザーを 1 人だけ作る。すでにあれば何もしない
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => self::EMAIL],
            ['name' => '持ち主', 'password' => self::PASSWORD],
        );
    }
}
