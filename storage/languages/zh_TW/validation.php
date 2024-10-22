<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */
return [
    /*
    |--------------------------------------------------------------------------
    | 驗證語言行
    |--------------------------------------------------------------------------
    |
    | 以下語言行包含驗證器類使用的預設錯誤消息。某些規則有多個版本，
    | 例如大小規則。您可以自由地在這裡調整每個消息。
    |
    */

    'accepted' => ':attribute 必須接受',
    'active_url' => ':attribute 必須是一個合法的 URL',
    'after' => ':attribute 必須是 :date 之後的一個日期',
    'after_or_equal' => ':attribute 必須是 :date 之後或相同的日期',
    'alpha' => ':attribute 只能包含字母',
    'alpha_dash' => ':attribute 只能包含字母、數字、中劃線或下劃線',
    'alpha_num' => ':attribute 只能包含字母和數字',
    'array' => ':attribute 必須是一個數組',
    'before' => ':attribute 必須是 :date 之前的一個日期',
    'before_or_equal' => ':attribute 必須是 :date 之前或相同的日期',
    'between' => [
        'numeric' => ':attribute 必須在 :min 到 :max 之間',
        'file' => ':attribute 必須在 :min 到 :max kb 之間',
        'string' => ':attribute 必須在 :min 到 :max 個字符之間',
        'array' => ':attribute 必須在 :min 到 :max 項之間',
    ],
    'boolean' => ':attribute 字符必須是 true 或 false, 1 或 0',
    'confirmed' => ':attribute 二次確認不匹配',
    'date' => ':attribute 必須是一個合法的日期',
    'date_format' => ':attribute 與給定的格式 :format 不符合',
    'different' => ':attribute 必須不同於 :other',
    'digits' => ':attribute 必須是 :digits 位',
    'digits_between' => ':attribute 必須在 :min 和 :max 位之間',
    'dimensions' => ':attribute 具有無效的圖片尺寸',
    'distinct' => ':attribute 字段具有重複值',
    'email' => ':attribute 必須是一個合法的電子郵件地址',
    'exists' => '選定的 :attribute 是無效的',
    'file' => ':attribute 必須是一個文件',
    'filled' => ':attribute 的字段是必填的',
    'gt' => [
        'numeric' => ':attribute 必須大於 :value',
        'file' => ':attribute 必須大於 :value kb',
        'string' => ':attribute 必須大於 :value 個字符',
        'array' => ':attribute 必須大於 :value 項',
    ],
    'gte' => [
        'numeric' => ':attribute 必須大於等於 :value',
        'file' => ':attribute 必須大於等於 :value kb',
        'string' => ':attribute 必須大於等於 :value 個字符',
        'array' => ':attribute 必須大於等於 :value 項',
    ],
    'image' => ':attribute 必須是 jpg, jpeg, png, bmp 或者 gif 格式的圖片',
    'in' => '選定的 :attribute 是無效的',
    'in_array' => ':attribute 字段不存在於 :other',
    'integer' => ':attribute 必須是一個整數',
    'ip' => ':attribute 必須是一個合法的 IP 地址',
    'ipv4' => ':attribute 必須是一個合法的 IPv4 地址',
    'ipv6' => ':attribute 必須是一個合法的 IPv6 地址',
    'json' => ':attribute 必須是一個合法的 JSON 字符串',
    'lt' => [
        'numeric' => ':attribute 必須小於 :value',
        'file' => ':attribute 必須小於 :value kb',
        'string' => ':attribute 必須小於 :value 個字符',
        'array' => ':attribute 必須小於 :value 項',
    ],
    'lte' => [
        'numeric' => ':attribute 必須小於等於 :value',
        'file' => ':attribute 必須小於等於 :value kb',
        'string' => ':attribute 必須小於等於 :value 個字符',
        'array' => ':attribute 必須小於等於 :value 項',
    ],
    'max' => [
        'numeric' => ':attribute 的最大值為 :max',
        'file' => ':attribute 的最大為 :max kb',
        'string' => ':attribute 的最大長度為 :max 字符',
        'array' => ':attribute 至多有 :max 項',
    ],
    'mimes' => ':attribute 的文件類型必須是 :values',
    'mimetypes' => ':attribute 的文件MIME必須是 :values',
    'min' => [
        'numeric' => ':attribute 的最小值為 :min',
        'file' => ':attribute 大小至少為 :min kb',
        'string' => ':attribute 的最小長度為 :min 字符',
        'array' => ':attribute 至少有 :min 項',
    ],
    'not_in' => '選定的 :attribute 是無效的',
    'not_regex' => ':attribute 不能匹配給定的正則',
    'numeric' => ':attribute 必須是數字',
    'present' => ':attribute 字段必須存在',
    'regex' => ':attribute 格式是無效的',
    'required' => ':attribute 字段是必須的',
    'required_if' => ':attribute 字段是必須的，當 :other 是 :value',
    'required_unless' => ':attribute 字段是必須的，除非 :other 是在 :values 中',
    'required_with' => ':attribute 字段是必須的當 :values 是存在的',
    'required_with_all' => ':attribute 字段是必須的當 :values 是存在的',
    'required_without' => ':attribute 字段是必須的當 :values 是不存在的',
    'required_without_all' => ':attribute 字段是必須的當 沒有一個 :values 是存在的',
    'same' => ':attribute 和 :other 必須匹配',
    'size' => [
        'numeric' => ':attribute 必須是 :size',
        'file' => ':attribute 必須是 :size kb',
        'string' => ':attribute 必須是 :size 個字符',
        'array' => ':attribute 必須包括 :size 項',
    ],
    'starts_with' => ':attribute 必須以 :values 為開頭',
    'string' => ':attribute 必須是一個字符串',
    'timezone' => ':attribute 必須是一個有效的時區',
    'unique' => ':attribute 已存在',
    'uploaded' => ':attribute 上傳失敗',
    'url' => ':attribute 無效的格式',
    'uuid' => ':attribute 無效的UUID格式',
    'max_if' => [
        'numeric' => '當 :other 為 :value 時 :attribute 不能大於 :max',
        'file' => '當 :other 為 :value 時 :attribute 不能大於 :max kb',
        'string' => '當 :other 為 :value 時 :attribute 不能大於 :max 個字符',
        'array' => '當 :other 為 :value 時 :attribute 最多只有 :max 個單元',
    ],
    'min_if' => [
        'numeric' => '當 :other 為 :value 時 :attribute 必須大於等於 :min',
        'file' => '當 :other 為 :value 時 :attribute 大小不能小於 :min kb',
        'string' => '當 :other 為 :value 時 :attribute 至少為 :min 個字符',
        'array' => '當 :other 為 :value 時 :attribute 至少有 :min 個單元',
    ],
    'between_if' => [
        'numeric' => '當 :other 為 :value 時 :attribute 必須介於 :min - :max 之間',
        'file' => '當 :other 為 :value 時 :attribute 必須介於 :min - :max kb 之間',
        'string' => '當 :other 為 :value 時 :attribute 必須介於 :min - :max 個字符之間',
        'array' => '當 :other 為 :value 時 :attribute 必須只有 :min - :max 個單元',
    ],
    /*
    |--------------------------------------------------------------------------
    | 自定義驗證語言行
    |--------------------------------------------------------------------------
    |
    | 在這裡，您可以為屬性指定自定義驗證消息，使用 "attribute.rule" 的約定來命名行。
    | 這使得為特定屬性規則指定特定的自定義語言行變得快捷。
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => '自定义消息',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 自定義驗證屬性
    |--------------------------------------------------------------------------
    |
    | 以下語言行用於將屬性佔位符替換為更易讀的形式，例如 E-Mail Address 代替 "email"。
    | 這只是幫助我們使消息更乾淨一些。
    |
    */

    'attributes' => [],
    'phone_number' => ':attribute 必須為一個有效的電話號碼',
    'telephone_number' => ':attribute 必須為一個有效的手機號碼',

    'chinese_word' => ':attribute 必須包含以下有效字符 (中文/英文，數字, 下劃線)',
    'sequential_array' => ':attribute 必須是一個有序數組',
];
