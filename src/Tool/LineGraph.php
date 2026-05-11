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

    /**
     * Add a new line to graph
     * xAxisValues must be set before adding a new line
     *
     * @param array $values
     * @return boolean true if line is added, false else
     */
    public function addLine(array $values, ?string $legend= null, ?string $color=null): bool
    {
        if (0 === ($this->xValues)) {
            return false;
        }
        $plot = new LinePlot($values, $this->xValues);
        if(null !== $legend) {
            $plot->SetLegend($legend);
        }
        if(null !== $color) {
            $plot->setColor($color);
        }
        $this->lines[] = $plot;
        return true;
    }

    /**
     * Draw Graph in memory
     * X axis and 1 line must be set before
     * @param string|null $title
     * @return boolean return true if graph is create, false else
     */
    public function drawGraph(?string $title = null): bool
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
        $xLegend = $this->width - ($this->width / 2);
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
}
