<?php
/**
 * @date: 2015/12/08
 * @author: Tiger <DropFan@Gmail.com>
 */

namespace Weatherlib\Util;

use Weatherlib\Util\Precip;
use Weatherlib\Util\Temperature;
use Weatherlib\Util\Wind;

class Condition extends Base
{

    public $weather;
    public $weatherID;
    public $description;
    public $temperature;
    public $wind;
    public $pressure;
    public $pressure_trend;
    public $cloudcover;
    public $precip;
    public $feelslike;
    public $windchill;
    public $visibility;
    public $humidity;
    public $dewpoint;
    public $UV;
    public $heat_index;

    public function __construct()
    {
        // echo 'condition';
        // $this->temperature;
        $this->wind = new Wind();
    }

    public function getWeather()
    {
        return $this->weather;
    }

    public function setWeather($value)
    {
        $this->weather = $value;
    }

    public function getWeatherID()
    {
        return $this->weatherID;
    }

    public function setWeatherID($value)
    {
        $this->weatherID = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getTemperature()
    {
        return $this->temperature;
    }

    public function setTemperature($v)
    {
        $this->temperature = $v;
    }

    public function getWind()
    {
        return $this->wind;
    }

    public function setWind($v)
    {
        $this->wind->setValue($v);
    }

    public function getPressure()
    {
        return $this->pressure;
    }

    public function setPressure($value)
    {
        $this->pressure = $value;
    }

    public function getPressure_trend()
    {
        return $this->pressure_trend;
    }

    public function setPressure_trend($value)
    {
        $this->pressure_trend = $value;
    }

    public function getCloudcover()
    {
        return $this->cloudcover;
    }

    public function setCloudcover($value)
    {
        $this->cloudcover = $value;
    }

    public function getPrecip()
    {
        return $this->precip;
    }

    public function setPrecip($value)
    {
        $this->precip = $value;
    }

    public function getFeelsLike()
    {
        return $this->feelslike;
    }

    public function setFeelsLike($value)
    {
        $this->feelslike = $value;
    }

    public function getWindchill()
    {
        return $this->windchill;
    }

    public function setWindchill($value)
    {
        $this->windchill = $value;
    }

    public function getVisibility()
    {
        return $this->visibility;
    }

    public function setVisibility($value)
    {
        $this->visibility = $value;
    }

    public function getHumidity()
    {
        return $this->humidity;
    }

    public function setHumidity($value)
    {
        $this->humidity = $value;
    }

    public function getDewpoint()
    {
        return $this->dewpoint;
    }

    public function setDewpoint($value)
    {
        $this->dewpoint = $value;
    }

    public function getUV()
    {
        return $this->UV;
    }

    public function setUV($value)
    {
        $this->UV = $value;
    }

    public function getHeat_index()
    {
        return $this->heat_index;
    }

    public function setHeat_index($value)
    {
        $this->heat_index = $value;
    }
}
