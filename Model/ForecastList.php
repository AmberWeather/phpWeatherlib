<?php
/**
 * @date: 2015/12/10
 * @author: Tiger <DropFan@Gmail.com>
 */

namespace Weatherlib\Model;

use Weatherlib\Model\Forecast;
use Weatherlib\Util\Base;

class ForecastList extends Base implements \IteratorAggregate, \ArrayAccess
{

    public $forecast = [];

    public function __construct($var = [])
    {
        // echo 'ForecastList';
        if (is_array($var)) {
            $this->forecast = $var;
        } elseif ($var instanceof \Traversable) {
            $this->forecast = $var;
        }
    }

    public function getIterator()
    {
        return $this->forecast;
    }

    public function offsetExists($o)
    {
        return isset($this->forecast[$o]);
    }

    public function offsetGet($o)
    {
        return isset($this->forecast[$o]) ? $this->v[$o] : null;
    }

    public function offsetSet($o, $v)
    {
        if ($this->check($v)) {
            if (is_null($o)) {
                $this->forecast[] = $v;
            } else {
                $this->forecast[$o] = $value;
            }
        } else {
            throw new \Exception('Error Type');
        }
    }

    public function offsetUnset($o)
    {
        unset($this->forecast[$o]);
    }

    public function check($value)
    {
        return $value instanceof Forecast;
    }
}
