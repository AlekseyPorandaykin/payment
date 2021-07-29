<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Класс для работы с датами
 */
class DateTimeHelper
{
    public const DATETIME_FORMAT_STR = 'Y-m-d H:i:s';

    public const DATETIME_FORMAT_TOGETHER_STR = 'Y-m-d_H_i';

    public const DATE_FORMAT_STR = 'Y-m-d';

    public const FIRST_DAY_STR = '0000-01-01';

    /**
     * Создавать дату без времени
     *
     * @param string $date
     * @return \DateTime
     */
    public function createDateWithoutTime(string $date): \DateTime
    {
        return \DateTime::createFromFormat(self::DATE_FORMAT_STR, $date)->setTime(0, 0, 0, 0);
    }

    public function createDateWithMaxTime(string $date): \DateTime
    {
        return \DateTime::createFromFormat(self::DATE_FORMAT_STR, $date)->setTime(23, 59, 59, 59);
    }

    /**
     * Создать текущую дату без времени
     *
     * @return \DateTime
     */
    public function createCurrentDateWithoutTime(): \DateTime
    {
        return (new \DateTime())->setTime(0, 0, 0, 0);
    }

    /**
     * @return \DateTime
     */
    public function createCurrentDateWithoutMaxTime(): \DateTime
    {
        return (new \DateTime())->setTime(23, 59, 59, 59);
    }

    /**
     * Проверяем соответствие строки формату даты
     *
     * @param string $dateStr
     * @return bool
     */
    public function validateFormatDateString(string $dateStr): bool
    {
        $date = \DateTime::createFromFormat(self::DATE_FORMAT_STR, $dateStr);
        return $date && $date->format(self::DATE_FORMAT_STR) === $dateStr;
    }

    /**
     * Создаём текущую дату в строковом представлении
     *
     * @return string
     */
    public function createCurrentDateString(): string
    {
        return (new \DateTime())->format(self::DATETIME_FORMAT_STR);
    }

    /**
     * @return \DateTime
     */
    public function createCurrentDate(): \DateTime
    {
        return (new \DateTime());
    }

    /**
     * Создаём самую первую дату
     *
     * @return \DateTime
     */
    public function createFirstDateString(): \DateTime
    {
        return $this->createDateWithoutTime(self::FIRST_DAY_STR);
    }

    /**
     * Этот день уже прошёл?
     *
     * @param string $dateStr
     * @return bool
     */
    public function isPastDay(string $dateStr): bool
    {
        $day = (int)$this->createDateWithoutTime($dateStr)->format('d');
        $currentDay = (int)$this->createCurrentDateWithoutTime()->format('d');

        return $day < $currentDay;
    }
}