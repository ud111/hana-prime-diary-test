<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * RefreshDatabase などのトレイトが動く前に、接続先がテスト用 DB であることを確かめる。
     * 開発用 DB に向いたままテストを走らせるとスキーマの作り直しでデータが消えるため、
     * データベース名が _test で終わらない場合は中止する。
     */
    protected function setUpTraits(): array
    {
        $database = DB::connection()->getDatabaseName();

        if (! str_ends_with($database, '_test')) {
            throw new RuntimeException(
                "テストは *_test データベースでのみ実行できます (現在の接続先: {$database})。"
                .' phpunit.xml の DB_DATABASE と、環境変数 DB_DATABASE が上書きしていないかを確認してください。'
            );
        }

        return parent::setUpTraits();
    }
}
