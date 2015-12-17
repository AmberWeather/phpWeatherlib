<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/10
 */

namespace Weatherlib\Model;

use Weatherlib\Util\Wind;
use Weatherlib\Util\Precip;

class HourlyForecast extends Forecast {

    public $rain;
    public $snow;

    public function __construct($d = []) {
        // $this->weatherID = $d['weather'];
        // $this->weather =
        parent::__construct();
    }
}
