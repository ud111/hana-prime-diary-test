<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * 画像の読み込み・変換に失敗したとき。利用者に見せるメッセージを持つ
 */
class ImageProcessingException extends RuntimeException {}
