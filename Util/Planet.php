<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/08
 */

namespace Weatherlib\Util;

/**
*
*/
class Planet extends Base {
    public $rise;
    public $set;

    public function __construct($rise = '--:--', $set = '--:--') {
        $this->rise = $rise;
        $this->set = $set;
    }

    public function setValue($rise, $set) {
        $this->rise = $rise;
        $this->set = $set;
    }

    public function getRise() {
        return $this->rise;
    }

    public function setRise($value) {
        $this->rise = $value;
    }

    public function getSet() {
        return $this->set;
    }

    public function setSet($value) {
        $this->set = $value;
    }
}
