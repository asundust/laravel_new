<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IdRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->isValidCard($value)) {
            $fail('身份证号错误');
        }
    }

    /**
     * 身份证号验证
     *
     * @param $id
     * @return bool
     */
    private function isValidCard($id): bool
    {
        if (18 != strlen($id)) {
            return false;
        }
        $weight = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        $code = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
        $mode = 0;
        $ver = substr($id, -1);
        if ($ver == 'x') {
            $ver = 'X';
        }
        foreach ($weight as $key => $val) {
            if ($key == 17) {
                continue;
            }
            $digit = intval(substr($id, $key, 1));
            $mode += $digit * $val;
        }
        $mode %= 11;
        if ($ver != $code[$mode]) {
            return false;
        }
        [$month, $day, $year] = self::getMDYFromCard($id);
        $check = checkdate($month, $day, $year);
        if (!$check) {
            return false;
        }
        $today = date('Ymd');
        $date = substr($id, 6, 8);
        if ($date >= $today) {
            return false;
        }
        return true;
    }

    /**
     * 获取身份证年月日
     *
     * @param $id
     * @return array
     */
    private function getMDYFromCard($id): array
    {
        $date = substr($id, 6, 8);
        $year = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $day = substr($date, 6);
        return [$month, $day, $year];
    }
}
