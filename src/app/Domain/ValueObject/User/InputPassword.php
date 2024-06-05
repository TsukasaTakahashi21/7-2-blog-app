<?php

namespace App\Domain\ValueObject;
use Exception;

final class InputPassword
{
    /**
     * パスワードの書式の正規表現
     * 半角英小文字大文字数字をそれぞれ1種類以上含む8文字以上100文字以下の正規表現
     */
    const PASSWORD_REGULAR_EXPRESSIONS = '/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{8,100}+\z/';

    const INVALID_MESSAGE = 'パスワードの形式が正しくありません';

    private $value;

    public function __construct(string $value)
    {
        if ($this->isInvalid($value)) {
            throw new Exception(self::INVALID_MESSAGE);
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function hash(): HashedPassword
    {
        return new HashedPassword(
            password_hash($this->value, PASSWORD_DEFAULT)
        );
    }

    private function isInvalid(string $value): bool
    {
        return !preg_match(self::PASSWORD_REGULAR_EXPRESSIONS, $value);
    }
}