<?php

declare(strict_types=1);

namespace App\Dto;

use App\Service\Report\RecordCollection;
use App\Service\Report\TotalAmountCollection;

class ReportDto
{
    private string $urlToCsvReport;
    private string $urlToXmlReport;
    /**
     * @var TotalAmountCollection
     */
    private TotalAmountCollection $totalAmountCollection;
    /**
     * @var RecordCollection
     */
    private RecordCollection $recordCollection;


    public function __construct(
        string $urlToCsvReport,
        string $urlToXmlReport,
        TotalAmountCollection $totalAmountCollection,
        RecordCollection $recordCollection
    ) {
        $this->urlToCsvReport = $urlToCsvReport;
        $this->urlToXmlReport = $urlToXmlReport;
        $this->totalAmountCollection = $totalAmountCollection;
        $this->recordCollection = $recordCollection;
    }

    /**
     * @return string
     */
    public function getUrlToCsvReport(): string
    {
        return $this->urlToCsvReport;
    }

    /**
     * @return string
     */
    public function getUrlToXmlReport(): string
    {
        return $this->urlToXmlReport;
    }

    /**
     * @return TotalAmountCollection
     */
    public function getTotalAmountCollection(): TotalAmountCollection
    {
        return $this->totalAmountCollection;
    }

    /**
     * @return RecordCollection
     */
    public function getRecordCollection(): RecordCollection
    {
        return $this->recordCollection;
    }
}