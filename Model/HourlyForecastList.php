<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/10
 */

namespace Weatherlib\Model;

use Weatherlib\Model\HourlyForecast as HourlyForecast;

class HourlyForecastList extends ForecastList {

    // public $arr;
    // public $i;

    public function __construct($var = []) {
        // echo 'HourlyForecastList';

        parent::__construct($var);
    }

    public function getIterator() {
        return $this->v;
    }

    public function check($value) {
        // $this->i++;
        // echo ' Hourly check ' . $this->i;
        return $value instanceof HourlyForecast;
    }
}
