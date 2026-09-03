<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * 動作確認用のダミーデータを投入する (php artisan db:seed)
     */
    public function run(): void
    {
        $this->call(DiarySeeder::class);
    }
}
