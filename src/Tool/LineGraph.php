<?php

namespace App\Tool;

use Graph;
use LinePlot;
use mitoteam\jpgraph\MtJpGraph;

class LineGraph
{
    private Graph $graph;
    private array $xValues = [];
    private array $lines = [];
    private int $minXValue = 0;
    private int $maxXValue = 0;

    public function __construct(private int $width, private int $height)
    {
        MtJpGraph::load(['line', 'date']);
        $this->graph = new Graph($width, $height);
    }


    /**
     * Set values that will be displayed on X axis
     * Must be set before adding lines
     * @param array $xAxisValues
     * @return void
     */
    public function setXAxisValues(array $xAxisValues, int $minValue, int $maxValue): void
    {
        $this->xValues = $xAxisValues;
        $this->minXValue = $minValue;
        $this->maxXValue = $maxValue;
    }

    public function setMinMax(int $min, int $max): void
    {
        $this->minXValue = $min;
        $this->maxXValue = $max;
    }

    /**
     * Add a new line to graph
     * xAxisValues must be set before adding a new line
     * Must be used with SetMinMax function (to setup the x scale value)
     * @param array $values
     * @param string|null $legend
     * @param string|null $color
     * @param boolean|null $isMarked
     * @param string|null $picture
     * @param string|null $markColor
     * 
     * @return boolean true if line is added, false else
     */
    public function addLine(array $values, ?string $legend = null, ?string $color = null, ?bool $isMarked = false, ?string $picture = null, ?string $markColor = null): bool
    {
        if (0 === ($this->xValues)) {
            return false;
        }
        $this->addPlot($values, $this->xValues, $legend, $color, $isMarked, $picture, $markColor);
        return true;
    }

    /**
     * Create a line that have its own x values.
     *
     * @param array $values
     * @param array|null $xValues
     * @param string|null $legend
     * @param string|null $color
     * @param boolean|null $isMarked
     * @param string|null $picture
     * @param string|null $markColor
     * @return void
     */
    public function addMultiValuesLine(array $values, ?array $xValues = null,  ?string $legend = null, ?string $color = null, ?bool $isMarked = false, ?string $picture = null, ?string $markColor = null)
    {
        if (null === $xValues) {
            $xValues = $this->xValues;
        }
        $this->addPlot($values, $xValues, $legend, $color, $isMarked, $picture, $markColor);
    }

    /**
     * Draw Graph in memory
     * X axis and 1 line must be set before
     * @param string|null $title
     * @param int|null $legendWidth : How many time width will be divide to make legend width (default 2)
     * @return boolean return true if graph is create, false else
     */
    public function drawGraph(?string $title = null, ?int $legendWidth = 2): bool
    {
        if (0 >= count($this->lines)) {
            return false;
        }
        $grace = 40;
        $xMin = $this->minXValue - $grace;
        $xMax = $this->maxXValue + $grace;
        $this->graph->SetScale('intlin', 0, 0, $xMin, $xMax); //X int, Y linear
        if (null !== $title) {
            $this->graph->title->Set($title);
            $this->graph->title->SetFont(FF_ARIAL, FS_NORMAL, 12);
        }
        $this->graph->xaxis->SetPos('min');
        $this->graph->xaxis->SetLabelMargin(10);
        $this->graph->xaxis->SetLabelFormatCallback([$this, 'formdate']);
        $this->graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 9);
        $this->graph->xaxis->SetLabelAngle(75);
        $this->graph->xgrid->Show();
        foreach ($this->lines as $line) {
            $this->graph->add($line);
        }
        $xLegend = $this->width - ($this->width / $legendWidth);
        $yLegend = 40;
        $this->graph->legend->SetAbsPos($xLegend, $yLegend, 'center', 'bottom');
        return true;
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

    private function createRandomColor(): array
    {
        $Radomizer = rand(1, 15);
        $R = rand(0, 4096) / (rand(1, 16) + $Radomizer);
        $V = rand(0, 2048) / rand(1, 8);
        $B = rand(0, 8192) / (rand(1, 32));
        return  array($R, $V, $B);
    }

    private function addPlot(array $yDatas, array $xDatas, ?string $legend = null, ?string $color = null, ?bool $isMarked = false, ?string $picture = null, ?string $markColor = null)
    {
        $plot = new LinePlot($yDatas, $xDatas);
        if (null !== $legend) {
            $plot->SetLegend($legend);
        }
        if (null !== $color) {
            $plot->setColor($color);
        }
        if ($isMarked) {
            $plot->mark->SetColor($markColor);
            $markFillColor = $this->createRandomColor();
            $plot->mark->SetFillColor($markFillColor);
            if (null !== $picture) {
                $plot->mark->SetType(MARK_IMG, $picture, '1');
            } else {
                $plot->mark->SetType(MARK_DIAMOND);
            }
        }
        $this->lines[] = $plot;
    }
}
