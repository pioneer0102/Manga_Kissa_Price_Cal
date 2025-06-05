<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;
use Exception;

require_once 'MangaCafeCalculator.php';

/**
 * マンガ喫茶料金計算の使用例.
 */
$calculator = new MangaCafeCalculator();

echo "=== マンガ喫茶料金計算システム ===\n\n";

// 利用可能なコース一覧を表示
echo "【利用可能なコース】\n";
$courses = $calculator->getAvailableCourses();

foreach ($courses as $key => $course) {
    echo "- {$key}: {$course['name']} - {$course['price']}円（税抜）\n";
}
echo "\n";

// 例1: 通常利用（延長なし）
echo "【例1: 3時間パック、延長なし】\n";

try {
    $checkIn = new DateTimeImmutable('2025-06-04 14:00:00');
    $checkOut = new DateTimeImmutable('2025-06-04 17:00:00');
    $course = CourseType::PACK_3HOUR;
    $result = $calculator->calculate($checkIn, $checkOut, $course);

    printResult($result);
} catch (Exception $e) {
    echo 'エラー: ' . $e->getMessage() . "\n";
}

echo "\n" . str_repeat('-', 50) . "\n\n";

// 例2: 延長あり（深夜時間なし）
echo "【例2: 3時間パック、1時間30分延長（深夜時間なし）】\n";

try {
    $checkIn = new DateTimeImmutable('2025-06-04 14:00:00');
    $checkOut = new DateTimeImmutable('2025-06-04 18:30:00');
    $course = CourseType::PACK_3HOUR;
    $result = $calculator->calculate($checkIn, $checkOut, $course);

    printResult($result);
} catch (Exception $e) {
    echo 'エラー: ' . $e->getMessage() . "\n";
}

echo "\n" . str_repeat('-', 50) . "\n\n";

// 例3: 深夜時間帯を含む延長
echo "【例3: 8時間パック、深夜時間帯を含む延長】\n";

try {
    $checkIn = new DateTimeImmutable('2025-06-04 20:00:00');
    $checkOut = new DateTimeImmutable('2025-06-05 06:30:00');
    $course = CourseType::PACK_8HOUR;
    $result = $calculator->calculate($checkIn, $checkOut, $course);

    printResult($result);
} catch (Exception $e) {
    echo 'エラー: ' . $e->getMessage() . "\n";
}

echo "\n" . str_repeat('-', 50) . "\n\n";

// 例4: 短時間利用（1分延長）
echo "【例4: 1時間コース、1分延長】\n";

try {
    $checkIn = new DateTimeImmutable('2025-06-04 15:00:00');
    $checkOut = new DateTimeImmutable('2025-06-04 16:01:00');
    $course = CourseType::REGULAR_1HOUR;
    $result = $calculator->calculate($checkIn, $checkOut, $course);

    printResult($result);
} catch (Exception $e) {
    echo 'エラー: ' . $e->getMessage() . "\n";
}

/**
 * 計算結果を表示する関数.
 */
function printResult(array $result): void
{
    echo "コース: {$result['course_info']['name']}\n";
    echo "利用時間: {$result['total_minutes']}分\n";
    echo "延長時間: {$result['extension_minutes']}分\n";
    echo "基本料金: {$result['base_fee']}円（税抜）\n";
    echo "延長料金: {$result['extension_fee']}円（税抜）\n";
    echo "小計: {$result['total_excluding_tax']}円（税抜）\n";
    echo "税額: {$result['tax_amount']}円\n";
    echo "合計: {$result['total_including_tax']}円（税込）\n";

    echo "\n【詳細】\n";
    echo "入店: {$result['breakdown']['check_in']}\n";
    echo "退店: {$result['breakdown']['check_out']}\n";

    if (isset($result['breakdown']['extension_details'])) {
        echo "\n【延長料金詳細】\n";

        foreach ($result['breakdown']['extension_details'] as $detail) {
            $nightInfo = $detail['is_night_time'] ? '（深夜割増）' : '';
            echo "- {$detail['period']}: {$detail['fee']}円{$nightInfo}\n";
        }
    }
}
