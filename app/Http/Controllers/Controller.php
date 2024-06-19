<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * 简易验证方法
     *
     * @param array $rules 规则
     * @param array $attributes 字段翻译
     * @param array $messages 消息翻译
     * @param bool|array $only 返回数据设置
     * @return array
     */
    public function toValidator(array $rules, array $attributes = [], array $messages = [], bool|array $only = true): array
    {
        $input = request()->all();
        $validator = Validator::make($input, $rules, $messages, $attributes)->stopOnFirstFailure();
        if ($validator->fails()) {
            $messages = $validator->messages()->getMessages();
            api_error(Arr::first($messages)[0]);
        }
        if ($only === true) {
            return Arr::only($input, array_keys($rules));
        } elseif (is_array($only) && count($only) > 0) {
            return Arr::only($input, $only);
        }
        return $input;
    }

    /**
     * ID验证
     *
     * @param string $idField
     * @return int
     */
    public function getId(string $idField = 'id'): int
    {
        $id = request()->input($idField);
        $validator = Validator::make(['id' => $id], ['id' => 'required|integer|min:1'], ['id' => $idField . '数据传参错误'])->stopOnFirstFailure();
        if ($validator->fails()) {
            $messages = $validator->messages()->getMessages();
            api_error(Arr::first($messages)[0]);
        }
        return $id;
    }
}
