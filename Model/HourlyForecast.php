<?php
/**
 * @date: 2015/12/10
 * @author: Tiger <DropFan@Gmail.com>
 */

namespace Weatherlib\Model;

class HourlyForecast extends Forecast
{

    public $rain;
    public $snow;

    public function __construct($d = [])
    {
        // $this->weatherID = $d['weather'];
        // $this->weather =
        parent::__construct();
    }
}
