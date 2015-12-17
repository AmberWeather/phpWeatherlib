<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/10
 */
namespace Weatherlib\Provider;

use Weatherlib\Provider\Fetcher;
use Weatherlib\Model\Location as Location;

/**
 * This abstract class is the base class of Weather Data Provider.
 * You can extend this class to implement more weather data sources.
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/10
 */
abstract class Provider /*implements IProvider*/ {

    protected $location;
    protected $apiKey = '';
    protected $baseUrl = '';
    /*protected $params = [
                         'query' => '',
                         'format' => 'json'
                        ];*/
    protected $queryUrl = '';

    // protected $query = '40.0494806,116.4073421';
    // protected $format = 'json';
    protected $rawData = '';

    protected $errno = 0;
    protected $error = '';
// https://api.wunderground.com/api/2b0d6572c90d3e4a/lang:CN/astronomy/conditions/forecast10day/hourly/q/40.0494806,116.4073421.json

    public function __construct($location) {
        if ($location instanceof Location) {
            $this->location = $location;
        } else {
            die('location error');
        }
        return false;
    }

    public function getQueryUrl() {
        $this->buildUrl();
        return $this->queryUrl;
    }

    abstract public function buildUrl();

    public function fetchRaw() {
        $fetcher = new Fetcher();
        $res = $fetcher->fetch($this->getQueryUrl());
        if (!$res) {
            return false;
        } else {
            $this->rawData = $res;
        }
        return $this->checkResult();
    }

    public function getRawData() {
        return $this->rawData;
    }

    public function setRawData($raw) {
        $this->rawData = $raw;
    }

    abstract public function getRawCurrentCondition();

    // abstract public function getRawForecast();

    abstract public function getRawDailyForecast();

    abstract public function getRawHourlyForecast();

    abstract public function getCurrentCondition();

    abstract public function getDailyForecast();

    abstract public function getHourlyForecast();

    abstract public function getWeatherCode($weather);
}
