<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/08
 */

namespace Weatherlib\Util;

use Weatherlib\Exception;

class Wind extends Base {

    public $speed;
    public $direction;
    public $degrees;
    public $gust;
    public $description;

    public function __construct($w = []) {

    }
    public function setValue($v) {
        if (is_array($v)) {
            isset($v['speed']) && $this->speed = $v['speed'];
            isset($v['direction']) && $this->direction = $v['direction'];
            isset($v['degrees']) && $this->degrees = $v['degrees'];
            isset($v['gust']) && $this->gust = $v['gust'];
            isset($v['description']) && $this->description = $v['description'];
        } elseif (is_object($v)) {
            isset($v->speed) && $this->speed = $v->speed;
            isset($v->direction) && $this->direction = $v->direction;
            isset($v->degrees) && $this->degrees = $v->degrees;
            isset($v->gust) && $this->gust = $v->gust;
            isset($v->description) && $this->description = $v->description;
        } else {
            throw new Exception('Invalid type of params. You should pass an array or object.');
        }
    }

    public function getSpeed() {
        return $this->speed;
    }

    public function setSpeed($value) {
        $this->speed = $value;
    }

    public function getDirection() {
        return $this->direction;
    }

    public function setDirection($value) {
        $this->direction = $value;
    }

    public function getDegrees() {
        return $this->degrees;
    }

    public function setDegrees($value) {
        $this->degrees = $value;
    }

    public function getGust() {
        return $this->gust;
    }

    public function setGust($value) {
        $this->gust = $value;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($value) {
        $this->description = $value;
    }

}
