<?php
/**
 * @date: 2015/12/08
 * @author: Tiger <DropFan@Gmail.com>
 */

namespace Weatherlib\Util;

use Weatherlib\Exception;

/**
 *
 */
class Precip extends Base
{

    public $amount;
    public $amount_day;
    public $amount_night;
    public $probability;

    public function __construct()
    {
        # code...
    }

    public function setValue($v)
    {
        if (is_array($v)) {
            isset($v['amount']) && $this->amount             = $v['amount'];
            isset($v['amount_day']) && $this->amount_day     = $v['amount_day'];
            isset($v['amount_night']) && $this->amount_night = $v['amount_night'];
            isset($v['probability']) && $this->probability   = $v['probability'];
        } elseif (is_object($v)) {
            isset($v->amount) && $this->amount             = $v->amount;
            isset($v->amount_day) && $this->amount_day     = $v->amount_day;
            isset($v->amount_night) && $this->amount_night = $v->amount_night;
            isset($v->probability) && $this->probability   = $v->probability;
        } else {
            throw new Exception('Invalid type of params. You should pass an array or object.');
        }
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($value)
    {
        $this->amount = $value;
    }

    public function getAmount_day()
    {
        return $this->amount_day;
    }

    public function setAmount_day($value)
    {
        $this->amount_day = $value;
    }

    public function getAmount_night()
    {
        return $this->amount_night;
    }

    public function setAmount_night($value)
    {
        $this->amount_night = $value;
    }

    public function getPop()
    {
        return $this->probability;
    }

    public function setPop($value)
    {
        $this->probability = $value;
    }

    public function getProbability()
    {
        return $this->probability;
    }

    public function setProbability($value)
    {
        $this->probability = $value;
    }
}
