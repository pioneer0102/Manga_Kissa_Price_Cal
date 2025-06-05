<?php

declare(strict_types=1);

namespace App;

use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;

require_once 'CourseType.php';

/**
 * マンガ喫茶料金計算クラス.
 */
class MangaCafeCalculator
{
    /** @var float 税率 */
    private const TAX_RATE = 0.10;

    /** @var int 延長料金（10分あたり、税抜） */
    private const EXTENSION_FEE_PER_10MIN = 100;

    /** @var float 深夜割増率（15%） */
    private const NIGHT_SURCHARGE_RATE = 0.15;

    /** @var int 深夜時間開始（時） */
    private const NIGHT_START_HOUR = 22;

    /** @var int 深夜時間終了（時） */
    private const NIGHT_END_HOUR = 5;

    /**
     * 料金を計算する.
     *
     * @param DateTimeImmutable $checkIn 入店日時
     * @param DateTimeImmutable $checkOut 退店日時
     * @param string $courseType コース種別
     *
     * @return array 計算結果
     *
     * @throws InvalidArgumentException
     */
    public function calculate(DateTimeImmutable $checkIn, DateTimeImmutable $checkOut, string $courseType): array
    {
        // バリデーション
        if (!CourseType::isValidCourseType($courseType)) {
            throw new InvalidArgumentException("無効なコースタイプです: {$courseType}");
        }

        if ($checkIn >= $checkOut) {
            throw new InvalidArgumentException('退店日時は入店日時より後である必要があります');
        }

        $courseDetails = CourseType::getCourseDetails($courseType);

        if (null === $courseDetails) {
            throw new InvalidArgumentException("コース詳細が取得できません: {$courseType}");
        }

        $totalMinutes = $this->calculateTotalMinutes($checkIn, $checkOut);

        // コース基本料金
        $baseFee = $courseDetails['price'];
        $courseMinutes = $courseDetails['hours'] * 60;

        // 延長時間の計算
        $extensionMinutes = max(0, $totalMinutes - $courseMinutes);
        $extensionFee = $this->calculateExtensionFee(
            $checkIn,
            $checkOut,
            (int) $courseMinutes,
            (int) $extensionMinutes
        );

        // 合計金額（税抜）
        $totalExcludingTax = $baseFee + $extensionFee;

        // 税込金額
        $totalIncludingTax = $totalExcludingTax * (1 + self::TAX_RATE);

        return [
            'course_info' => $courseDetails,
            'total_minutes' => $totalMinutes,
            'extension_minutes' => $extensionMinutes,
            'base_fee' => $baseFee,
            'extension_fee' => $extensionFee,
            'total_excluding_tax' => $totalExcludingTax,
            'total_including_tax' => (int) $totalIncludingTax,
            'tax_amount' => (int) ($totalIncludingTax - $totalExcludingTax),
            'breakdown' => $this->getDetailedBreakdown(
                $checkIn,
                $checkOut,
                (int) $courseMinutes,
                (int) $extensionMinutes
            ),
        ];
    }

    /**
     * 利用可能なコース一覧を取得.
     */
    public function getAvailableCourses(): array
    {
        return CourseType::getCourseInfo();
    }

    /**
     * 総利用時間（分）を計算.
     */
    private function calculateTotalMinutes(DateTimeImmutable $checkIn, DateTimeImmutable $checkOut): int
    {
        $diff = $checkOut->diff($checkIn);

        return ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i + ($diff->s > 0 ? 1 : 0);
    }

    /**
     * 延長料金を計算.
     */
    private function calculateExtensionFee(
        DateTimeImmutable $checkIn,
        DateTimeImmutable $checkOut,
        int $courseMinutes,
        int $extensionMinutes
    ): int {
        if ($extensionMinutes <= 0) {
            return 0;
        }

        // 延長開始時刻を計算
        $extensionStart = $checkIn->add(new DateInterval("PT{$courseMinutes}M"));

        // 10分単位で切り上げ
        $extensionBlocks = (int) ceil($extensionMinutes / 10);

        $totalExtensionFee = 0;

        // 各10分ブロックごとに深夜割増を計算
        for ($i = 0; $i < $extensionBlocks; ++$i) {
            $blockStart = $extensionStart->add(new DateInterval('PT' . ($i * 10) . 'M'));
            $blockEnd = $extensionStart->add(new DateInterval('PT' . (($i + 1) * 10) . 'M'));

            // 実際の終了時刻を超えないように調整
            if ($blockEnd > $checkOut) {
                $blockEnd = $checkOut;
            }

            $baseFee = self::EXTENSION_FEE_PER_10MIN;

            // 深夜時間帯に1分でも含まれているかチェック
            if ($this->isNightTime($blockStart, $blockEnd)) {
                $baseFee = (int) round($baseFee * (1 + self::NIGHT_SURCHARGE_RATE));
            }

            $totalExtensionFee += $baseFee;
        }

        return $totalExtensionFee;
    }

    /**
     * 指定期間が深夜時間帯に含まれるかチェック.
     */
    private function isNightTime(
        DateTimeImmutable $start,
        DateTimeImmutable $end
    ): bool {
        $current = $start;

        while ($current < $end) {
            $hour = (int) $current->format('H');

            if ($hour >= self::NIGHT_START_HOUR || $hour < self::NIGHT_END_HOUR) {
                return true;
            }

            $current = $current->add(new DateInterval('PT1M'));
        }

        return false;
    }

    /**
     * 詳細な内訳を取得.
     */
    private function getDetailedBreakdown(
        DateTimeImmutable $checkIn,
        DateTimeImmutable $checkOut,
        int $courseMinutes,
        int $extensionMinutes
    ): array {
        $breakdown = [
            'check_in' => $checkIn->format('Y-m-d H:i:s'),
            'check_out' => $checkOut->format('Y-m-d H:i:s'),
            'course_time' => $courseMinutes . '分',
            'extension_time' => $extensionMinutes . '分',
        ];

        if ($extensionMinutes > 0) {
            $extensionStart = $checkIn->add(new DateInterval("PT{$courseMinutes}M"));
            $extensionBlocks = (int) ceil($extensionMinutes / 10);

            $breakdown['extension_details'] = [];

            for ($i = 0; $i < $extensionBlocks; ++$i) {
                $blockStart = $extensionStart->add(new DateInterval('PT' . ($i * 10) . 'M'));
                $blockEnd = $extensionStart->add(new DateInterval('PT' . (($i + 1) * 10) . 'M'));

                if ($blockEnd > $checkOut) {
                    $blockEnd = $checkOut;
                }

                $isNight = $this->isNightTime($blockStart, $blockEnd);
                $fee = self::EXTENSION_FEE_PER_10MIN;

                if ($isNight) {
                    $fee = (int) round($fee * (1 + self::NIGHT_SURCHARGE_RATE));
                }

                $breakdown['extension_details'][] = [
                    'period' => $blockStart->format('H:i') . '-' . $blockEnd->format('H:i'),
                    'is_night_time' => $isNight,
                    'fee' => $fee,
                ];
            }
        }

        return $breakdown;
    }
}
