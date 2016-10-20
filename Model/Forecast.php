<?php
/**
 * @date: 2015/12/08
 * @author: Tiger <DropFan@Gmail.com>
 */

namespace Weatherlib\Model;

use Weatherlib\Util\Condition;
use Weatherlib\Util\Precip;

class Forecast extends Condition
{

    public $time;

    public function __construct()
    {
        // echo 'Forecast';
        parent::__construct();
        $this->precip = new Precip();
        $this->snow   = new Precip();
        $this->rain   = new Precip();
    }

    public function setTime($value)
    {
        $this->time = $value;
    }

    public function setPrecip($v)
    {
        $this->precip->setValue($v);
    }

    public function setRain($v)
    {
        $this->rain->setValue($v);
    }

    public function setSnow($v)
    {
        $this->snow->setValue($v);
    }

}
