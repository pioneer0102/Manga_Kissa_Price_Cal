<?php

declare(strict_types=1);

namespace App;

/**
 * マンガ喫茶のコース種別定義.
 */
class CourseType
{
    public const REGULAR_1HOUR = 'regular_1hour';
    public const PACK_3HOUR = 'pack_3hour';
    public const PACK_5HOUR = 'pack_5hour';
    public const PACK_8HOUR = 'pack_8hour';

    /**
     * コース別の料金と時間を取得.
     */
    public static function getCourseInfo(): array
    {
        return [
            self::REGULAR_1HOUR => [
                'name' => '通常料金（入室から1時間）',
                'price' => 500,
                'hours' => 1,
            ],
            self::PACK_3HOUR => [
                'name' => '3時間パック（入室から3時間）',
                'price' => 800,
                'hours' => 3,
            ],
            self::PACK_5HOUR => [
                'name' => '5時間パック（入室から5時間）',
                'price' => 1500,
                'hours' => 5,
            ],
            self::PACK_8HOUR => [
                'name' => '8時間パック（入室から8時間）',
                'price' => 1900,
                'hours' => 8,
            ],
        ];
    }

    /**
     * 有効なコースタイプかチェック.
     */
    public static function isValidCourseType(string $courseType): bool
    {
        return array_key_exists($courseType, self::getCourseInfo());
    }

    /**
     * コース情報を取得.
     */
    public static function getCourseDetails(string $courseType): ?array
    {
        $courseInfo = self::getCourseInfo();

        return $courseInfo[$courseType] ?? null;
    }
}
