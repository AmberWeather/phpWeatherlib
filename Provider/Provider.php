<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/10
 */
namespace Weatherlib\Provider;

use Weatherlib\Util\Fetcher as Fetcher;
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

    protected $fetcher = null;

    protected $errno = 0; # 1000+ curl error
    protected $error = '';

    public function __construct(Location $location = null, Fetcher $fetcher = null) {
        if ($location instanceof Location) {
            $this->location = $location;
        } else {
            die('location error');
        }

        if ($fetcher instanceof Fetcher) {
            $this->fetcher = $fetcher;
        } else {
            $this->fetcher = new Fetcher();
        }

        return false;
    }

    public function getLocation() {
        return $this->location;
    }

    public function setLocation(Location $location) {
        if ($location instanceof Location) {
            $this->location = $location;
            return true;
        } else {
            return false;
        }
    }

    public function getFetcher() {
        return $this->fetcher;
    }

    public function setFetcher(Fetcher $fetcher) {
        if ($fetcher instanceof Fetcher) {
            $this->fetcher = $fetcher;
            return true;
        } else {
            return false;
        }
    }

    public function getQueryUrl() {
        $this->buildUrl();
        return $this->queryUrl;
    }

    abstract public function buildUrl();

    public function fetchRaw($raw = null) {
        if (!$raw){
            $fetcher = $this->fetcher;
            $res = $fetcher->fetch($this->getQueryUrl());
        } else {
            $res = $raw;
        }
        // var_dump($res);
        if (is_array($res)) {
            if (is_string($res['data']) && !empty($res['data'])) {
                $this->rawData = $res['data'];
                return $this->checkResult();
            } else {
                $this->error = 'Fetched data is empty.';
                $this->errno = 2000;
            }

            # curl error
            isset($res['error']) && $this->error = $res['error'];
            isset($res['errno']) && $this->errno = 1000 + $res['errno']; # 1000 + curl errno
        } elseif (is_string($res)) {
            $this->rawData = $res;
            return $this->checkResult();
        } else {
            $this->errno = 1;
            $this->error = 'Fetch raw data failed. (Unknown ERROR.)';
        }
        // var_dump($this->errno, $this->error);
        return false;
    }

    public function getRawData() {
        return $this->rawData;
    }

    public function setRawData($raw='') {
        $this->rawData = $raw;
    }

    abstract public function getRawCurrentCondition();

    // abstract public function getRawForecast();

    abstract public function getRawDailyForecast();

    abstract public function getRawHourlyForecast();

    abstract public function getCurrentCondition();

    abstract public function getDailyForecast();

    abstract public function getHourlyForecast();

    abstract public function getWeatherCode($weather='');

    public function errno() {
        return $this->errno;
    }

    public function error() {
        return $this->error;
    }
}
