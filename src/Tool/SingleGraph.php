<?php

namespace App\Tool;

use DateTimeImmutable;
use App\Dto\StatDtoInterface;

class SingleGraph
{
    private LineGraph $graph;

    public function __construct(private int $width, private int $height)
    {
        $this->graph = new LineGraph($width, $height);
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
        $allDatas = $this->makePrintableArray($datas);
        $allDates = $allDatas['dates'];
        $allValues = $allDatas['values'];
        $dates = $this->getMinMaxDate($datas);
        $minDate = $dates['startDate']->getTimestamp();
        $maxDate = $dates['endDate']->getTimestamp();
        $this->graph->setXAxisValues($allDates, $minDate, $maxDate);
        $this->graph->addLine($allValues);
        $this->graph->drawGraph($title);
    }

    public function save(string $path)
    {
        $this->graph->save($path);
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
