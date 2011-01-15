<?php

namespace Bundle\GoogleChartBundle\Library;

use Bundle\GoogleChartBundle\Library\AbstractChart;
use Bundle\GoogleChartBundle\Library\Axis;

abstract class AbstractAxisChart extends AbstractChart {

    public function __construct(array $options = array()) {
        $yAxis = new Axis('y');
        $yAxis->setMin(0);
        
        $this->defaultOptions = array_merge(
            $this->defaultOptions,
            array('axis' => array (
                new Axis('x'),
                $yAxis,
            ))
        );
        parent::__construct($options);
    }
    
    public function getAxis() {
        return $this->options['axis'];
    }
    
    public function getXAxis() {
        return $this->_getAxis('x');
    }
    
    public function getYAxis() {
        return $this->_getAxis('y');
    }
    
    protected function _getAxis($position) {
        foreach ($this->options['axis'] as &$axis) {
            if ($axis->getPosition() == $position) {
                return $axis;
            }
        }
    }
    
    
    protected function getUrlParts() {
        return array_merge(
            parent::getUrlParts(),
            array (
                'chxt' =>  $this->getAxisUrlPart(),
                'chxr' =>  $this->getScaleUrlPart(),
            )
        );
    }
    
    protected function getAxisUrlPart() {
        $axisArray = array();
        foreach ($this->getAxis() as $axis) {
            if ($axis->isEnabled()) {
                $axisArray[] = $axis->getPosition();
            }
        }
        return implode(',', $axisArray);
    }
    
    protected function getScaleUrlPart() {
        //$this->calculateDimensions(); // update chart dimensions
        $scalesArray = array();
        /*$disabledAxis = */$index = 0;
        foreach ($this->getAxis() as $axis) {
            if ($this->hasToPrint($axis)) {
                // if scale is set to 'auto' get minimal and maximal values found among all collections
                if ($axis->getMax() === Axis::AUTO || $axis->getMin() === Axis::AUTO) {
                    list($min, $max) = $axis->isVertical() ? $this->getYDimensions() : $this->getXDimensions();
                }
                $scalesArray[] = 
                    /*($index - $disabledAxis)*/ $index++ . ',' .
                    ($axis->getMin() === Axis::AUTO ? $min : $axis->getMin()) . ',' . 
                    ($axis->getMax() === Axis::AUTO ? $max : $axis->getMax());
                
            }/* elseif (!$axis->isEnabled()) {
                // each axis has index, we need to know how many disabled axis we want to skip
                $disabledAxis++;
            }*/
        }
        if ($scalesArray) {
            return implode('|', $scalesArray);
        } else {
            return null;
        }
    }
    
    public function getYDimensions() {
        return $this->calculateAxisDimensions('vertical');
    }
    
    public function getXDimensions() {
        return $this->calculateAxisDimensions('horizontal');
    }
    
    /**
     * Get minimum and maximum values among all data collections for particular axis
     */
    protected function calculateAxisDimensions($dimension) {
        /*if ($dimension != 'x' && $position != 'y' && $position != 'right' && $position != 'top') {
            throw new \InvalidArgumentException('Invalid axis, use only x, y, right or top');
        }*/
        
        $min = null;
        $max = null;
        foreach ($this->getAxis() as $axis) {
            //if ($dimension == 'vertical' && ($axis->getPosition() == 'y' || $axis->getPosition() = 'right')) {
            foreach ($this->getData() as $collection) {
                if (($dimension == 'vertical' && ($axis->getPosition() == 'y' || $axis->getPosition() == 'right'))
                        || ($dimension == 'horizontal' && ($axis->getPosition() == 'x' || $axis->getPosition() == 'top'))) {
                    
                    if ($axis->getMin() === Axis::AUTO) {
                        $min = is_null($min) ? ($dimension == 'vertical' ? $collection->getMinY() : $collection->getMinX())
                                             : min(($dimension == 'vertical' ? $collection->getMinY() : $collection->getMinX()), $min);
                    } else {
                        $min = $axis->getMin();
                    }
                    if ($axis->getMax() === Axis::AUTO) {
                        $max = is_null($max) ? ($dimension == 'vertical' ? $collection->getMaxY() : $collection->getMaxX())
                                             :  max(($dimension == 'vertical' ? $collection->getMaxY() : $collection->getMaxX()), $max);
                    } else {
                        $max = $axis->getMax();
                    }
                }
            }
            /*} elseif ($dimension == 'horizontal' && ($axis->getPosition() == 'x' || $axis->getPosition() = 'top')) {
                foreach ($this->getData() as $collection) {
                    if ($axis->getMin() == Axis::AUTO) {
                        $min = $axis->getMin();
                    } else {
                        $min = is_null($min) ? $collection->getMinY() : min($collection->getMinY(), $min);
                    }
                    if ($axis->getMax() == Axis::AUTO) {
                        $max = $axis->getMax();
                    } else {
                        $max = is_null($max) ? $collection->getMaxY() : max($collection->getMaxY(), $max);
                    }
                }
            }*/
        }
        
        /*if ($position == 'x' || $position == 't') { // set dimensions for x axis
            foreach ($this->getData() as $collection) {
                $min = is_null($min) ? $collection->getMinX() : min($collection->getMinX(), $min);
                $max = is_null($max) ? $collection->getMaxX() : max($collection->getMaxX(), $max);
            }
        } elseif ($position == 'y' || $position == 'r') { // set dimensions for y axis
            foreach ($this->getData() as $collection) {
                $min = is_null($min) ? $collection->getMinY() : min($collection->getMinY(), $min);
                $max = is_null($max) ? $collection->getMaxY() : max($collection->getMaxY(), $max);
            }
            foreach ($this->getAxis() as $posititon => $axis) {
                //if ($posititon == '')
            }
        }*/
        return array($min, $max);
    }
    
    /**
     *
     * @return boolean  True if it's necessary to print this axis
     */
    protected function hasToPrint(Axis $axis) {
        return $axis->isEnabled() && !$axis->hasDefaultSettings();
    }

}

