<?php

declare(strict_types=1);

namespace App\Service\Report;

use App\Dto\ReportDto;
use App\Entity\Currency;
use App\Exception\ApplicationException;
use App\Repository\ClientRepository;
use App\Repository\HistoryWalletRepository;
use App\Service\DateTimeHelper;

/**
 * Класс для получения данных и создания соответствующих отчётов
 */
class ReportGenerator
{
    /**
     * @var HistoryWalletRepository
     */
    private HistoryWalletRepository $historyWalletRepository;
    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;
    /**
     * @var DateTimeHelper
     */
    private DateTimeHelper $dateTimeHelper;
    /**
     * @var FileReportGenerator
     */
    private FileReportGenerator $fileReportGenerator;

    public function __construct(
        HistoryWalletRepository $historyWalletRepository,
        ClientRepository $clientRepository,
        DateTimeHelper $dateTimeHelper,
        FileReportGenerator $fileReportGenerator
    ) {
        $this->historyWalletRepository = $historyWalletRepository;
        $this->clientRepository = $clientRepository;
        $this->dateTimeHelper = $dateTimeHelper;
        $this->fileReportGenerator = $fileReportGenerator;
    }

    /**
     * @param string      $clientGuid
     * @param string|null $dateTimeFromStr
     * @param string|null $dateTimeToStr
     * @return ReportDto
     * @throws \App\Exception\NotFoundEntityException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \League\Csv\Exception
     * @throws ApplicationException
     */
    public function generateReport(
        string $clientGuid,
        string $dateTimeFromStr = '',
        string $dateTimeToStr = ''
    ): ReportDto {
        $client = $this->clientRepository->findByGuid($clientGuid);
        $dateTimeFrom = $this->dateTimeHelper->validateFormatDateString($dateTimeFromStr)
            ? $this->dateTimeHelper->createDateWithoutTime($dateTimeFromStr)
            : $this->dateTimeHelper->createFirstDateString();
        $dateTimeTo = (
            $this->dateTimeHelper->validateFormatDateString($dateTimeToStr)
            && $this->dateTimeHelper->isPastDay($dateTimeToStr)
        )
            ? $this->dateTimeHelper->createDateWithMaxTime($dateTimeToStr)
            : $this->dateTimeHelper->createCurrentDate();

        #Уникальное название отчёта
        $keyReport = sprintf(
            '%s_%s_%s',
            $client->getGuid(),
            $dateTimeFrom->format(DateTimeHelper::DATETIME_FORMAT_TOGETHER_STR),
            $dateTimeTo->format(DateTimeHelper::DATETIME_FORMAT_TOGETHER_STR)
        );
        #Если существует отчёт сохранённый в системе, то данные получаем из файла
        if (
            ($csvFile = $this->fileReportGenerator->getPathToExistingCsvByKey($keyReport))
            && ($xmlFile = $this->fileReportGenerator->getPathToExistingXmlByKey($keyReport))
        ) {
            $data = $this->getDataFromFile($keyReport);
        } else {
            $data = $this->historyWalletRepository->getRecordByParams($client->getGuid(), $dateTimeFrom, $dateTimeTo);
            if (count($data) === 0) {
                throw new ApplicationException('Данные не найдены');
            }
            $csvFile = $this->fileReportGenerator->generateCsv($keyReport, $data);
            $xmlFile = $this->fileReportGenerator->generateXml($keyReport);
        }
        $totalAmount = $this->calculateTotalAmount($data);

        return new ReportDto(
            $csvFile,
            $xmlFile,
            $totalAmount,
            new RecordCollection($data)
        );
    }

    /**
     * Получаем данные из файла в виде массива
     *
     * @param string $key
     * @return array
     * @throws \League\Csv\Exception
     */
    private function getDataFromFile(string $key): array
    {
        return \iterator_to_array($this->fileReportGenerator->getDataFromFile($key));
    }

    /**
     * Считаем общую информацию по движения денежных средств
     *
     * @param array| \Iterator $data
     * @return TotalAmountCollection
     */
    private function calculateTotalAmount($data): TotalAmountCollection
    {
        $totalAmount = new TotalAmountCollection();
        foreach ($data as $item) {
            $totalAmount->increaseAmount(Currency::MAIN_CURRENCY_CODE, (float)$item['amount']);
            $totalAmount->increaseAmount($item['currencyCode'], (float)$item['currencyAmount']);
        }
        return $totalAmount;
    }
}