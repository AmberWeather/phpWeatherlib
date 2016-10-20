<?php
/**
 * @date: 2015/12/12
 * @author: Tiger <DropFan@Gmail.com>
 */
namespace Weatherlib;

use Weatherlib\Model\Current;
use Weatherlib\Model\DailyForecastList;
use Weatherlib\Model\HourlyForecastList;
use Weatherlib\Model\Location;
use Weatherlib\Provider\ProviderFactory;


class Weatherlib
{

    public $location;
    public $currentCondition;
    public $dailyForecast;
    public $hourlyForecast;
    public $sun_moon;

    private $provider;
    private $errno = 0;
    private $error = '';

    public function __construct($locationData = [], $datasource = 'wunderground')
    {
        $this->initData($locationData);
        $this->setLocation($locationData);
        $this->provider = ProviderFactory::getProvider($datasource, $this->location);
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function setProvider(Provider $provider)
    {
        if ($provider instanceof Provider) {
            $this->provider = $provider;
        } else {
            return false;
        }
    }

    public function setLocation($data = [])
    {
        // $data = [
        //         'id' => '1',
        //         'geohash' => '',
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
        if ($this->provider) {
            $this->provider->setLocation($this->location);
        }
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function fetchWeather()
    {
        if ($this->provider->fetchRaw()) {
            $this->currentCondition = $this->provider->getCurrentCondition();
            $this->dailyForecast    = $this->provider->getDailyForecast();
            $this->hourlyForecast   = $this->provider->getHourlyForecast();
            $this->sun_moon         = $this->provider->getSunAndMoon();
            $this->location         = $this->provider->getLocation();

            return true;
        } else {
            $this->errno = $this->provider->errno();
            $this->error = $this->provider->error();
        }

        return false;
    }

    public function getWeather($format = 'json')
    {
        if ($format == 'json') {
            if (!($this->location instanceof Location)) {
                $this->location = null;
            }
            if (!($this->currentCondition instanceof Current)) {
                $this->currentCondition = null;
            }
            if (!($this->dailyForecast instanceof DailyForecastList)) {
                $this->dailyForecast = null;
            }
            if (!($this->hourlyForecast instanceof HourlyForecastList)) {
                $this->hourlyForecast = null;
            }
            if (empty($this->sun_moon) || !$this->sun_moon) {
                $this->sun_moon = null;
            }

            return json_encode($this);
        } elseif ($format == 'xml') {
            return '{"Only JSON here. If you need xml format, please complete it by yourself."}';
        }

        return json_encode($this);
    }

    public function getQueryUrl()
    {
        return $this->provider->getQueryUrl();
    }

    public function getRawData()
    {
        return $this->provider->getRawData();
    }

    public function setRawData($raw)
    {
        $this->provider->setRawData($raw);
    }

    public function getCurrentCondition()
    {
        if (!($this->currentCondition instanceof Current)) {
            $this->currentCondition = $this->provider->getCurrentCondition();
        }

        return $this->currentCondition;
    }

    private function setCurrentCondition(Current $cur = null)
    {
        if (!($cur instanceof Current)) {
            $this->currentCondition = new Current();
        } else {
            $this->currentCondition = $cur;
        }
    }

    public function getDailyForecast()
    {
        if (!($this->dailyForecast instanceof DailyForecastList)) {
            $this->dailyForecast = $this->provider->getDailyForecast();
        }

        return $this->dailyForecast;
    }

    private function setDailyForecast(DailyForecastList $dl = null)
    {
        if (!($dl instanceof DailyForecastList)) {
            $this->dailyForecast = new DailyForecastList();
        } else {
            $this->dailyForecast = $dl;
        }
    }

    public function getHourlyForecast()
    {
        if (!($this->hourlyForecast instanceof HourlyForecastList)) {
            $this->hourlyForecast = $this->provider->getHourlyForecast();
        }

        return $this->hourlyForecast;
    }

    private function setHourlyForecast(HourlyForecastList $hl = null)
    {
        if (!($hl instanceof HourlyForecastList)) {
            $this->hourlyForecast = new HourlyForecastList();
        } else {
            $this->hourlyForecast = $hl;
        }
    }

    private function initData($locationData = [])
    {
        $this->setLocation($locationData);
        $this->setCurrentCondition();
        $this->setDailyForecast();
        $this->setHourlyForecast();
    }

    public function error()
    {
        return $this->error;
    }

    public function errno()
    {
        return $this->errno;
    }
}
