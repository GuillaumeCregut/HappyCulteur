<?php

namespace App\Tool;

use DateTimeImmutable;
use App\Dto\StatDtoInterface;
use Graph;
use LinePlot;
use mitoteam\jpgraph\MtJpGraph;

class SingleGraph
{
    private Graph $graph;

    public function __construct(private int $width, private int $height)
    {
        MtJpGraph::load(['line', 'date']);
        $this->graph = new Graph($width, $height);
    }

    /**
     * create graph in memory
     *
     * @param string $title
     * @param StatDtoInterface[] $datas
     * @return void
     */
    public function draw(string $title, array $datas)
    {
        $grace = 40;
        $dates = $this->getMinMaxDate($datas);
        $minDate = $dates['startDate']->getTimestamp();
        $maxDate = $dates['endDate']->getTimestamp();
        $xmin = $minDate - $grace;
        $xmax = $maxDate + $grace;
        $this->graph->SetScale('intlin', 0, 0, $xmin, $xmax); //X int, Y linear
        $this->graph->title->Set($title);
        $this->graph->title->SetFont(FF_ARIAL, FS_NORMAL, 12);
        $this->graph->xaxis->SetPos('min');
        $this->graph->xaxis->SetLabelMargin(10);
        $this->graph->xaxis->SetLabelFormatCallback([$this, 'formdate']);
        $this->graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 9);
        $this->graph->xaxis->SetLabelAngle(75);
        $this->graph->xgrid->Show();
        $allDatas = $this->makePrintableArray($datas);
        $allDates = $allDatas['dates'];
        $allValues = $allDatas['values'];
        $p = new LinePlot($allValues, $allDates);
        $this->graph->Add($p);
        $xLegend = $this->width - ($this->width / 2);
        $yLegend = 40;
        $this->graph->legend->SetAbsPos($xLegend, $yLegend, 'center', 'bottom');
    }

    public function save(string $path)
    {
        $img = $this->graph->Stroke(_IMG_HANDLER);
        imagepng($img, $path);
    }

    public function formDate(mixed $aVal)
    {
        return date('d-m-y', $aVal);
    }

    /**
     * Get the minimum date and maximum date in an array
     *
     * @param StatDtoInterface[] $values
     * @return DateTimeImmutable[]
     */
    private function getMinMaxDate(array $values): array
    {
        $start = $values[0]->date;
        $end = end($values)->date;
        return ['startDate' => $start, 'endDate' => $end];
    }

    /**
     * Create 2 arrays from data array. 
     * One for x axis, the other for y axis
     *
     * @param StatDtoInterface[] $values
     * @return array[]
     */
    private function makePrintableArray(array $values): array
    {
        $arrayDates = [];
        $arrayValues = [];
        foreach ($values as $dto) {
            $date = $dto->date->getTimestamp();
            $arrayDates[] = $date;
            $arrayValues[] = $dto->getValue();
        }
        return [
            'dates' => $arrayDates,
            'values' => $arrayValues
        ];
    }
}
