<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/10
 */

namespace Weatherlib\Model;

use Weatherlib\Model\DailyForecast as DailyForecast;

class DailyForecastList extends ForecastList {

    // public $arr;

    public function __construct($var = []) {
        // echo 'DailyForecastList';
        parent::__construct($var);
    }

    public function getIterator() {
        return $this->v;
    }

    public function check($value) {
        return $value instanceof DailyForecast;
    }
}
