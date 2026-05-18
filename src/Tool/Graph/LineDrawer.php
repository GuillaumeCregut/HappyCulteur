<?php

namespace App\Tool\Graph;

use App\Tool\LineGraph;

class LineDrawer
{
    private LineGraph $graph;

    public function __construct(private ?int $width = 800, private ?int $height = 400)
    {
        $this->graph = new LineGraph($width, $height);
    }

    /**
     * draw a graph with one line
     *
     * @param string $title
     * @param array<int, array{date: \DateTimeImmutable, value: mixed}> $values
     * @return void
     */
    public function drawGraphOneLine(string $title, array $values): void
    {
        $xyValues = $this->formatArray($values);
        $dateValues = $xyValues['date'];
        $yValues = $xyValues['value'];
        $min = $dateValues[0]->getTimestamp();
        $max = end($dateValues)->getTimestamp();
        $xValues = [];
        foreach ($dateValues as $value) {
            $xValues[] = $value->getTimestamp();
        }
        $this->graph->setXAxisValues($xValues, $min, $max);
        $this->graph->addLine($yValues);
        $this->graph->drawGraph($title);
    }

    /**
     * draw a graph with two line
     *
     * @param string $title
     * @param array<int, array{date: \DateTimeImmutable, value: mixed}> $valuesLine1
     * @param string $legend1
     * @param array<int, array{date: \DateTimeImmutable, value: mixed}> $valuesLine2
     * @param string $legend2
     * @return void
     */
    public function drawGraphTwoLines(string $title, array $valuesLine1, string $legend1, array $valuesLine2,  string $legend2): void
    {
        $xyValuesLine1 = $this->formatArray($valuesLine1);
        $xyValuesLine2 = $this->formatArray($valuesLine2);
        $dateValues = $xyValuesLine1['date'];
        $yValuesLine1 = $xyValuesLine1['value'];
        $yValuesLine2 = $xyValuesLine2['value'];
        $min = $dateValues[0]->getTimestamp();
        $max = end($dateValues)->getTimestamp();
        $xValuesLine1 = [];
        foreach ($dateValues as $value) {
            $xValuesLine1[] = $value->getTimestamp();
        }
        $this->graph->setXAxisValues($xValuesLine1, $min, $max);
        $this->graph->addLine($yValuesLine1, $legend1, 'teal');
        $this->graph->addLine($yValuesLine2, $legend2, 'black');
        $this->graph->drawGraph($title);
    }

    public function save(string $path)
    {
        $this->graph->save($path);
    }

    /**
     * Get an array key=> value and return 2 arrays [key] and [values]
     *
     * @param array<int, array{date: \DateTimeImmutable, value: mixed}> $values
     * @return array{date: \DateTimeImmutable[], value: mixed[]}
     */
    private function formatArray(array $values): array
    {
        $returnArray = [];
        foreach ($values as $value) {
            $returnArray['date'][] = $value['date'];
            $returnArray['value'][] = $value['value'];
        }
        return $returnArray;
    }
}
