<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ReportDto;
use App\Validation\ReportParamValidate;
use App\Service\Report\ReportGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    /**
     * @var ReportGenerator
     */
    private ReportGenerator $reportService;
    /**
     * @var ReportParamValidate
     */
    private ReportParamValidate $validate;

    public function __construct(ReportGenerator $reportService, ReportParamValidate $validate)
    {
        $this->reportService = $reportService;
        $this->validate = $validate;
    }

    public function __invoke(Request $request, int $page)
    {
        $errorMessage = '';
        $reportDto = null;
        $totalPage = 0;
        $client = $request->query->get('client');
        $dateFrom = $request->query->get('date_from');
        $dateTo = $request->query->get('date_to');
        try {
            $this->validate->validate($request->query->all());
            $reportDto = $this->reportService->generateReport($client, $dateFrom, $dateTo);
            $totalPage = $reportDto->getRecordCollection()->countAllParts();
        } catch (\Throwable $exception) {
            $errorMessage = $exception->getMessage();
        }
        return $this->render('main/index.html.twig', [
            'client'         => $client,
            'date_from'      => $dateFrom,
            'date_to'        => $dateTo,
            'error_message'  => $errorMessage,
            'report'         => $reportDto,
            'start_position' => 1,
            'end_position'   => 2,
            'current_page'   => $page,
            'total_page'     => $totalPage,
            'uri'            => strrchr($request->getRequestUri(), "?")
                ? substr(strrchr($request->getRequestUri(), "?"), 1)
                : '',
        ]);
    }
}