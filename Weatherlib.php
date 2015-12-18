<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/12
 */
namespace Weatherlib;

use Weatherlib\Model\Location;
use Weatherlib\Model\Current;
use Weatherlib\Model\DailyForecastList;
use Weatherlib\Model\HourlyForecastList;
use Weatherlib\Util\Base;

use Weatherlib\Provider\ProviderFactory;

/**
*
*/
class Weatherlib {

    public $location;
    private $provider;
    public $currentCondition;
    public $dailyForecast;
    public $hourlyForecast;
    public $sun_moon;
    private $errno = 0;
    private $error = '';

    public function __construct($locationData = [], $datasource = 'wunderground') {
        $this->setLocation($locationData);
        $this->setProvider($datasource, $location = $this->location);
    }

    public function setProvider($datasource = 'wunderground') {
        $this->provider = ProviderFactory::getProvider($datasource, $this->location);
    }

    public function setLocation($data = []) {
        // $data = [
        //         'id' => '1',
        //         'city' => 'Beijing',
        //         'latitude' => '39.55',
        //         'longitude' => '119.55',
        //         'country' => 'China',
        //         'countryCode' => 'CN',
        //         'lang' => 'zh_CN',
        //         'full_text' => 'Beijing, China',
        //         'timezone' => '+8',
        //         'timezone_full' => 'Asia/Beijing',
        //         'osmid' => '',
        //         'accuid' => '',
        //         'owmid' => '',
        //         'yhcode' => '',
        //         'wmo' => '',
        //         'zipcode' => '100000'
        //         ];
        $this->location = new Location($data);
    }

    public function getLocation() {
        return $this->location;
    }

    public function fetchWeather() {
        if ($this->provider->fetchRaw()) {
            $this->currentCondition = $this->provider->getCurrentCondition();
            $this->dailyForecast = $this->provider->getDailyForecast();
            $this->hourlyForecast = $this->provider->getHourlyForecast();
            $this->sun_moon = $this->provider->getSunAndMoon();
            return true;
        } else {
            $this->errno = $this->provider->errno();
            $this->error = $this->provider->error();
        }
        return false;
    }

    public function getWeather($format = 'json') {
        if ($format == 'json') {
            return json_encode($this);
        } elseif ($format == 'xml') {
            return 'Only JSON here. If you need xml format, please complete it by yourself.';
        }
        return json_encode($this);
    }

    public function getRawData() {
        return $this->provider->getRawData();
    }
    public function getCurrentCondition() {
        if (!($this->currentCondition instanceof Current)) {
            $this->currentCondition = $this->provider->getCurrentCondition();
        }
        return $this->currentCondition;
    }

    public function getDailyForecast() {
        if (!($this->dailyForecast instanceof DailyForecastList)) {
            $this->dailyForecast = $this->provider->getDailyForecast();
        }
        return $this->dailyForecast;
    }

    public function getHourlyForecast() {
        if (!($this->hourlyForecast instanceof HourlyForecastList)) {
            $this->hourlyForecast = $this->provider->getHourlyForecast();
        }
        return $this->hourlyForecast;
    }

    public function error() {
        return $this->error;
    }

    public function errno() {
        return $this->errno;
    }
}
