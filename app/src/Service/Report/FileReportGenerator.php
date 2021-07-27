<?php

declare(strict_types=1);

namespace App\Service\Report;

use App\Exception\ApplicationException;
use League\Csv\CannotInsertRecord;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use League\Csv\XMLConverter;

/**
 * Класс для формирования фалов отчёта
 */
class FileReportGenerator
{
    private string $projectDirFiles;

    public function __construct(string $projectDirFiles)
    {
        $this->projectDirFiles = $projectDirFiles;
    }

    /**
     * Проверка существования отчётов в проекте
     *
     * @param string $key
     * @return bool
     */
    public function issetByKey(string $key): bool
    {
        return \file_exists($this->generatePath($key, 'csv'))
            && \file_exists($this->generatePath($key, 'xml'));
    }

    /**
     * Формируем отчёт в csv и в xml и сохраняем в системе
     *
     * @param string $key
     * @param array  $data
     * @throws ApplicationException
     * @throws \League\Csv\Exception
     */
    public function generate(string $key, array $data): void
    {
        try {
            $this->writeDataToCsv($key, $data);
            $this->writeDataToXml($key);
        } catch (\Throwable $e) {
            throw new ApplicationException("Ошибка записи в файл({$e->getMessage()})");
        }
    }

    /**
     * Загружаем данные из csv файла
     *
     * @param string $key
     * @return \Iterator
     * @throws \League\Csv\Exception
     */
    public function getDataFromFile(string $key): \Iterator
    {
        $reader = Reader::createFromPath($this->generatePath($key));
        $reader->setHeaderOffset(0);
        return $reader->getRecords();
    }

    /**
     * Формируем путь до файла
     *
     * @param string $key
     * @param string $extension
     * @return string
     */
    private function generatePath(string $key, string $extension = 'csv'): string
    {
        return $this->projectDirFiles . "/{$key}.{$extension}";
    }

    /**
     * Формируем отчёт в виде csv файла
     *
     * @param string $key
     * @param array  $data
     * @throws CannotInsertRecord
     */
    private function writeDataToCsv(string $key, array $data): void
    {
        $fileName = $this->generatePath($key);
        $writer = Writer::createFromPath($fileName, 'w+');
        $keys = array_keys($data[0]);
        $writer->insertOne($keys);
        $writer->insertAll($data);
    }

    /**
     * Формируем отчёт в виде xml файла
     *
     * @param $key
     * @throws \League\Csv\Exception
     */
    private function writeDataToXml(string $key): void
    {
        $csv = Reader::createFromPath($this->generatePath($key), 'r');
        $csv->setHeaderOffset(0);
        $stmt = (new Statement());
        $converter = (new XMLConverter())
            ->rootElement('wallet_history')
            ->recordElement('record', 'offset')
            ->fieldElement('field', 'name');
        $records = $stmt->process($csv);
        $dom = $converter->convert($records);
        $dom->formatOutput = true;
        $dom->encoding = 'iso-8859-15';
        file_put_contents($this->generatePath($key, 'xml'), $dom->saveXML());
    }
}