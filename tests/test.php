<?php

require_once 'src/MangaCafeCalculator.php';

/**
 * マンガ喫茶料金計算のテストケース.
 */
class MangaCafeCalculatorTest
{
    private MangaCafeCalculator $calculator;
    /** @var array<bool> */
    private array $testResults = [];

    public function __construct()
    {
        $this->calculator = new MangaCafeCalculator();
    }

    /**
     * 全テストを実行.
     */
    public function runAllTests(): void
    {
        echo "=== マンガ喫茶料金計算システムテスト ===\n\n";

        $this->testBasicCourse();
        $this->testExtensionFee();
        $this->testNightSurcharge();
        $this->testEdgeCases();
        $this->testValidation();

        $this->printSummary();
    }

    /**
     * 基本コース料金のテスト.
     */
    private function testBasicCourse(): void
    {
        echo "【基本コース料金テスト】\n";

        // 3時間パック、延長なし
        $checkIn = new DateTimeImmutable('2025-06-04 14:00:00');
        $checkOut = new DateTimeImmutable('2025-06-04 17:00:00');
        $result = $this->calculator->calculate($checkIn, $checkOut, CourseType::PACK_3HOUR);

        $this->assert(800 === $result['total_excluding_tax'], '3時間パック基本料金', "期待値: 800円, 実際: {$result['total_excluding_tax']}円");
        $this->assert(0 === $result['extension_minutes'], '延長時間なし', "期待値: 0分, 実際: {$result['extension_minutes']}分");
        $this->assert(880 === $result['total_including_tax'], '税込金額', "期待値: 880円, 実際: {$result['total_including_tax']}円");

        echo "\n";
    }

    /**
     * 延長料金のテスト.
     */
    private function testExtensionFee(): void
    {
        echo "【延長料金テスト】\n";

        // 1時間コース + 1分延長（10分ブロック1つ分）
        $checkIn = new DateTimeImmutable('2025-06-04 15:00:00');
        $checkOut = new DateTimeImmutable('2025-06-04 16:01:00');
        $result = $this->calculator->calculate($checkIn, $checkOut, CourseType::REGULAR_1HOUR);

        $this->assert(1 === $result['extension_minutes'], '1分延長', "期待値: 1分, 実際: {$result['extension_minutes']}分");
        $this->assert(100 === $result['extension_fee'], '延長料金', "期待値: 100円, 実際: {$result['extension_fee']}円");
        $this->assert(600 === $result['total_excluding_tax'], '合計（税抜）', "期待値: 600円, 実際: {$result['total_excluding_tax']}円");

        // 3時間パック + 25分延長（10分ブロック3つ分）
        $checkIn = new DateTimeImmutable('2025-06-04 14:00:00');
        $checkOut = new DateTimeImmutable('2025-06-04 17:25:00');
        $result = $this->calculator->calculate($checkIn, $checkOut, CourseType::PACK_3HOUR);

        $this->assert(25 === $result['extension_minutes'], '25分延長', "期待値: 25分, 実際: {$result['extension_minutes']}分");
        $this->assert(300 === $result['extension_fee'], '延長料金（3ブロック）', "期待値: 300円, 実際: {$result['extension_fee']}円");

        echo "\n";
    }

    /**
     * 深夜割増のテスト.
     */
    private function testNightSurcharge(): void
    {
        echo "【深夜割増テスト】\n";

        // 8時間パック（20:00-04:00）+ 深夜時間帯延長
        $checkIn = new DateTimeImmutable('2025-06-04 20:00:00');
        $checkOut = new DateTimeImmutable('2025-06-05 04:30:00');
        $result = $this->calculator->calculate($checkIn, $checkOut, CourseType::PACK_8HOUR);

        $this->assert(30 === $result['extension_minutes'], '30分延長', "期待値: 30分, 実際: {$result['extension_minutes']}分");

        // 深夜時間帯なので割増適用: 100 * 1.15 = 115円 × 3ブロック = 345円
        $this->assert(345 === $result['extension_fee'], '深夜割増延長料金', "期待値: 345円, 実際: {$result['extension_fee']}円");

        echo "\n";
    }

    /**
     * エッジケースのテスト.
     */
    private function testEdgeCases(): void
    {
        echo "【エッジケーステスト】\n";

        // 1秒延長
        $checkIn = new DateTimeImmutable('2025-06-04 15:00:00');
        $checkOut = new DateTimeImmutable('2025-06-04 16:00:01');
        $result = $this->calculator->calculate($checkIn, $checkOut, CourseType::REGULAR_1HOUR);

        $this->assert(1 === $result['extension_minutes'], '1秒延長は1分とカウント', "期待値: 1分, 実際: {$result['extension_minutes']}分");
        $this->assert(100 === $result['extension_fee'], '1秒延長でも延長料金発生', "期待値: 100円, 実際: {$result['extension_fee']}円");

        // 深夜時間境界（22:00-22:01）
        $checkIn = new DateTimeImmutable('2025-06-04 21:00:00');
        $checkOut = new DateTimeImmutable('2025-06-04 22:01:00');
        $result = $this->calculator->calculate($checkIn, $checkOut, CourseType::REGULAR_1HOUR);

        $this->assert(1 === $result['extension_minutes'], '深夜境界延長', "期待値: 1分, 実際: {$result['extension_minutes']}分");
        $this->assert(115 === $result['extension_fee'], '深夜境界割増', "期待値: 115円, 実際: {$result['extension_fee']}円");

        echo "\n";
    }

    /**
     * バリデーションのテスト.
     */
    private function testValidation(): void
    {
        echo "【バリデーションテスト】\n";

        $checkIn = new DateTimeImmutable('2025-06-04 15:00:00');
        $checkOut = new DateTimeImmutable('2025-06-04 14:00:00'); // 入店より前

        try {
            $this->calculator->calculate($checkIn, $checkOut, CourseType::REGULAR_1HOUR);
            $this->assert(false, '退店日時が入店日時より前', '例外がスローされるべき');
        } catch (InvalidArgumentException $e) {
            $this->assert(true, '退店日時が入店日時より前', '正しく例外がスローされた');
        }

        try {
            $checkIn = new DateTimeImmutable('2025-06-04 15:00:00');
            $checkOut = new DateTimeImmutable('2025-06-04 16:00:00');
            $this->calculator->calculate($checkIn, $checkOut, 'invalid_course');
            $this->assert(false, '無効なコースタイプ', '例外がスローされるべき');
        } catch (InvalidArgumentException $e) {
            $this->assert(true, '無効なコースタイプ', '正しく例外がスローされた');
        }

        echo "\n";
    }

    /**
     * アサーション.
     */
    private function assert(bool $condition, string $testName, string $message = ''): void
    {
        $result = $condition ? 'PASS' : 'FAIL';
        $this->testResults[] = $condition;

        echo "- {$testName}: {$result}";
        if (!empty($message)) {
            echo " ({$message})";
        }
        echo "\n";
    }

    /**
     * テスト結果サマリー
     */
    private function printSummary(): void
    {
        $total = count($this->testResults);
        $passed = array_sum($this->testResults);
        $failed = $total - $passed;

        echo str_repeat('=', 50)."\n";
        echo "テスト結果サマリー\n";
        echo "総テスト数: {$total}\n";
        echo "成功: {$passed}\n";
        echo "失敗: {$failed}\n";

        if (0 === $failed) {
            echo "すべてのテストが成功しました！\n";
        } else {
            echo "一部のテストが失敗しました。\n";
        }
    }
}

// テスト実行
$test = new MangaCafeCalculatorTest();
$test->runAllTests();
