<?php

namespace GoogleChartGenerator\Chart;

use GoogleChartGenerator\Chart\AbstractAxisChart;
use GoogleChartGenerator\Chart\LineChart\Line;
use GoogleChartGenerator\Font;
use GoogleChartGenerator\Axis;

class MdLineChart extends AbstractAxisChart {
    protected function getChartName() {
        return 'md-line';
    }
}
