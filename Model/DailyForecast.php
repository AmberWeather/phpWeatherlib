<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/08
 */

namespace Weatherlib\Model;

use Weatherlib\Util\Temperature;
use Weatherlib\Util\Wind;
use Weatherlib\Util\Planet;
use Weatherlib\Util\Precip;

class DailyForecast extends Forecast {

    public $sun;
    public $moon;
    public $wind_max;
    public $rain;
    public $snow;

    public function __construct() {

        $this->sun = new Planet();
        $this->moon = new Planet();
        $this->wind_max = new Wind();
        $this->wind = new Wind();
        $this->precip = new Precip();
        $this->rain = new Precip();
        $this->snow = new Precip();
        $this->humidity = [
                            'max' => null,
                            'min' => null,
                            'ave' => null
                        ];
    }

    public function setHighLow($key, $high, $low, $ave) {
        $this->$key = [
                    'high' => $high,
                    'low' => $low,
                    'ave' => $ave
        ];
    }

    public function setWind_max($v) {
        $this->wind_max->setValue($v);
    }

    public function setHumidity($v) {
        $this->setHighLow('humidity', $v['high'], $v['low'], $v['ave']);
    }

    public function setTemperature($v) {
        $this->temperature = [
                            'high' => $v['high'],
                            'low' => $v['low'],
                            'high_night' => isset($v['high_night']) ? $v['high_night']:null,
                            'low_night' => isset($v['low_night']) ? $v['low_night']:null,
                            'ave' => isset($v['ave']) ? $v['ave']:null
        ];
    }
}
