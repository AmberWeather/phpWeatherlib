<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/08
 */

namespace Weatherlib\Model;

use Weatherlib\Util\Base;
use Weatherlib\Util\Condition;
use Weatherlib\Util\Planet;

// use Weatherlib\Util\Location;

/**
 * Current condition
 */
class Current extends Condition {

    // public $location;
    public $observation_time;

    public $sun;
    public $moon;


    public function __construct() {
        // echo 'current';
        parent::__construct();
        // $this->location = new Location();
        // $this->temperature = (string)($this->temperature);
        // $this->currentCondition = new Condition();
        $this->sun = new Planet();
        $this->moon = new Planet();
    }

    public function setObservation_time($value) {
        $this->observation_time = $value;
    }

    public function getObservation_time() {
        return $this->observation_time;
    }

    public function getSun() {
        return $this->sun;
    }

    public function setSun($value) {
        $this->sun = $value;
    }

    public function getMoon() {
        return $this->moon;
    }

    public function setMoon($value) {
        $this->moon = $value;
    }

    public function getSolarradiation() {
        return $this->solarradiation;
    }

    public function setSolarradiation($value) {
        $this->solarradiation = $value;
    }
    // public function setTemperature($v) {
    //     $this->setTemperature = $v;
    // }
}
