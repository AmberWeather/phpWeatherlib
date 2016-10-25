<?php
/**
 * @date: 2015/12/10
 * @author: Tiger <DropFan@Gmail.com>
 */
namespace Weatherlib\Provider;

use Weatherlib\Config;
use Weatherlib\Model\Current;
use Weatherlib\Model\DailyForecast;
use Weatherlib\Model\DailyForecastList;
use Weatherlib\Model\HourlyForecast;
use Weatherlib\Model\HourlyForecastList;
use Weatherlib\Model\Location;
use Weatherlib\Util\WeatherCode;

/**
 *
 */
class WundergroundProvider extends Provider
{

    private $params = [
        'features' => ['astronomy', 'conditions', 'forecast10day', 'hourly10day'],
        'settings' => ['lang' => 'CN' /*'pwd' => 1, 'bestfct' => 1*/],
        'query'    => '40.0494806,116.4073421',
        'format'   => 'json',
    ];
    private $features = 'astronomy/conditions/forecast10day/hourly';
    private $settings = 'lang:CN';
    private $query    = '40.0494806,116.4073421';
    private $format   = 'json';
    // https://api.wunderground.com/api/2b0d6572c90d3e4a/lang:CN/astronomy/conditions/forecast10day/hourly/q/40.0494806,116.4073421.json

    public function __construct(
        Location $location = null,
        $features = [],
        $format = 'json',
        $apiKey = '') {
        parent::__construct($location);

        $this->baseUrl = 'http://api.wunderground.com/api/';
        $this->apiKey  = (string) $apiKey ?: Config::WU_CONF['api_key'];

        if (is_array($features) && !empty($features)) {
            $this->params['features'] = $features;
        } else {
            $this->params['features'] = Config::WU_CONF['features'];
        }

        $this->params['settings']['lang'] = (string) $this->location->lang ?
        $this->getLangCode($this->location->lang) : 'EN';
        $this->params['format'] = (string) $format ?: 'json';
    }

    public function buildUrl()
    {
        $this->buildParams();
        $this->queryUrl = "{$this->baseUrl}{$this->apiKey}/{$this->features}/{$this->settings}/q/{$this->query}.{$this->format}";
    }

    public function buildParams()
    {
        $p = '';

        $this->features = implode('/', $this->params['features']);

        $s = [];

        foreach ($this->params['settings'] as $k => $v) {
            $s[] = $k . ':' . $v;
        }
        $this->settings = implode('/', $s);
        $this->query    = $this->location->latitude . ',' . $this->location->longitude;
        $this->format   = $this->params['format'];
    }

    public function checkResult()
    {

        $j = json_decode($this->rawData, 1);
        // var_dump($j);

        if (!$j) {
            $this->errno = 2001;
            $this->error = 'Fetched data decode failed, maybe it is not a json.';

            return false;
        }

        if (isset($j['response']['error'])) {
            $err = $j['response']['error'];

            if ($err['type'] == 'querynotfound') {
                $this->errno = 2002;
                $this->error = $err['description'];

                return false;
            } elseif ($err['type'] == 'keynotfound') {
                $this->errno = 2003;
                $this->error = $err['description'];

                return false;
            }

            # other error return by wunderground
            $this->errno = 2004;
            $this->error = $err['description'];
        }

        if (isset($j['response']['features']) && !empty($j['response']['features'])) {
            if (isset($j['response']['features']['conditions'])) {
                return true;
            }
        } elseif ($this->errno != 2004) {
            $this->errno = 2000;
            $this->error = 'Check Data failed Unknown Error.';
        }

        return false;
    }

    public function getRawCurrentCondition()
    {
        $j = json_decode($this->rawData);

        return $j->current_observation;
    }

    public function getRawForecast()
    {
        $j = json_decode($this->rawData);

        return $j->forecast;
    }

    public function getRawDailyForecast()
    {
        $j = json_decode($this->rawData);

        return $j->forecast;
    }

    public function getRawHourlyForecast()
    {
        $j = json_decode($this->rawData);

        return $j->hourly_forecast;
    }

    public function getCurrentCondition()
    {
        $j = json_decode($this->rawData, 1);
        // var_dump($j);
        if (!isset($j['current_observation'])) {
            return false;
        }
        $c = $j['current_observation'];

        // var_dump($c);
        $ret = new Current();
        isset($c['observation_epoch']) && $ret->setObservation_time($c['observation_epoch']);
        isset($c['weather']) && $ret->setWeather($c['weather']);
        isset($c['icon']) && $ret->setWeatherID($this->getWeatherCode($c['icon']));
        isset($c['weather']) && $ret->setDescription($c['weather']);
        isset($c['temp_c']) && $ret->setTemperature($c['temp_c']);
        isset($c['relative_humidity']) && $ret->setHumidity($c['relative_humidity']);

        $w = [];

        isset($c['wind_kph']) && $w['speed']          = $c['wind_kph'];
        isset($c['wind_dir']) && $w['direction']      = $c['wind_dir'];
        isset($c['wind_degrees']) && $w['degrees']    = $c['wind_degrees'];
        isset($c['wind_gust_kph']) && $w['gust']      = $c['wind_gust_kph'];
        isset($c['wind_string']) && $w['description'] = $c['wind_string'];
        $ret->setWind($w);

        # 1 mbar = 1 hPa = 0.1 kPa = 0.76 mmHg = 0.03 inHg
        isset($c['pressure_mb']) && $ret->setPressure($c['pressure_mb']);
        isset($c['pressure_trend']) && $ret->setPressure_trend($c['pressure_trend']);
        isset($c['dewpoint_c']) && $ret->setDewPoint($c['dewpoint_c']);
        isset($c['windchill_c']) && $ret->setWindchill($c['windchill_c']);
        isset($c['feelslike_c']) && $ret->setFeelsLike($c['feelslike_c']);
        isset($c['visibility_km']) && $ret->setVisibility($c['visibility_km']);
        isset($c['UV']) && $ret->setUV($c['UV']);
        isset($c['solarradiation']) && $ret->setSolarradiation($c['solarradiation']);
        isset($c['heat_index_c']) && $ret->setHeat_index($c['heat_index_c']);

        isset($c['precip_1hr_metric']) && $ret->setPrecip($c['precip_1hr_metric']);

        // $ret->set
        // echo $this->queryUrl;
        // var_dump($ret);die;

        return $ret;
    }

    public function getDailyForecast()
    {
        $j = json_decode($this->rawData, 1);
        if (!isset($j['forecast'])) {
            return false;
        }
        // var_dump($j);
        // $dl_txt = $j['forecast']['txt_forecast']['forecastday'];
        // var_dump($dl_txt);
        if (!isset($j['forecast']['simpleforecast']['forecastday'])) {
            return false;
        }
        $dl  = $j['forecast']['simpleforecast']['forecastday'];
        $ret = new DailyForecastList();
        foreach ($dl as $k => $v) {
            // var_dump($dl_txt[$k], $dl_txt[$k+1]);
            // var_dump($dl[$k]);
            $d = new DailyForecast();
            // $d->setDescription();
            isset($v['date']['epoch']) && $d->setTime($v['date']['epoch']);
            isset($v['conditions']) && $d->setWeather($v['conditions']);
            isset($v['icon']) && $d->setWeatherID($this->getWeatherCode($v['icon']));

            $t = [];

            isset($v['high']['celsius']) && $t['high'] = $v['high']['celsius'];
            isset($v['low']['celsius']) && $t['low']   = $v['low']['celsius'];
            $d->setTemperature($t);

            $precip = [];

            isset($v['qpf_allday']['mm']) && $precip['amount']      = $v['qpf_allday']['mm'];
            isset($v['qpf_day']['mm']) && $precip['amount_day']     = $v['qpf_day']['mm'];
            isset($v['qpf_night']['mm']) && $precip['amount_night'] = $v['qpf_night']['mm'];
            isset($v['pop']) && $precip['probability']              = $v['pop'];
            $d->setPrecip($precip);

            $snow = [];

            isset($v['snow_allday']['cm']) && $snow['amount']      = $v['snow_allday']['cm'];
            isset($v['snow_day']['cm']) && $snow['amount_day']     = $v['snow_day']['cm'];
            isset($v['snow_night']['cm']) && $snow['amount_night'] = $v['snow_night']['cm'];
            $d->setSnow($snow);

            $wind = [];

            isset($v['avewind']['kph']) && $wind['speed']       = $v['avewind']['kph'];
            isset($v['avewind']['dir']) && $wind['direction']   = $v['avewind']['dir'];
            isset($v['avewind']['degrees']) && $wind['degrees'] = $v['avewind']['degrees'];
            $d->setWind($wind);

            $wind_max = [];

            isset($v['maxwind']['kph']) && $wind_max['speed']       = $v['maxwind']['kph'];
            isset($v['maxwind']['dir']) && $wind_max['direction']   = $v['maxwind']['dir'];
            isset($v['maxwind']['degrees']) && $wind_max['degrees'] = $v['maxwind']['degrees'];
            $d->setWind_max($wind_max);

            $humidity = [];

            isset($v['maxhumidity']) && $humidity['high'] = $v['maxhumidity'];
            isset($v['minhumidity']) && $humidity['low']  = $v['minhumidity'];
            isset($v['avehumidity']) && $humidity['ave']  = $v['avehumidity'];
            $d->setHumidity($humidity);
            // var_dump($d);die;
            $ret[] = $d;
        }

        return $ret;
    }

    public function getHourlyForecast()
    {
        $j = json_decode($this->rawData, 1);
        if (!isset($j['hourly_forecast'])) {
            return false;
        }
        $hl  = $j['hourly_forecast'];
        $ret = new HourlyForecastList();
        foreach ($hl as $k => $v) {
            $h = new HourlyForecast();
            // var_dump($v);
            isset($v['FCTTIME']['epoch']) && $h->setTime($v['FCTTIME']['epoch']);
            isset($v['condition']) && $h->setWeather($v['condition']);
            isset($v['icon']) && $h->setWeatherID($this->getWeatherCode($v['icon']));
            isset($v['temp']['metric']) && $h->setTemperature($v['temp']['metric']);
            isset($v['dewpoint']['metric']) && $h->setDewpoint($v['dewpoint']['metric']);

            $w = [];

            isset($v['wspd']['metric']) && $w['speed']    = $v['wspd']['metric'];
            isset($v['wdir']['dir']) && $w['direction']   = $v['wdir']['dir'];
            isset($v['wdir']['degrees']) && $w['degrees'] = $v['wdir']['degrees'];
            $h->setWind($w);

            isset($v['mslp']['metric']) && $h->setPressure($v['mslp']['metric']);
            isset($v['humidity']) && $h->setHumidity($v['humidity']);
            isset($v['windchill']['metric']) && $h->setWindchill($v['windchill']['metric']);
            isset($v['feelslike']['metric']) && $h->setFeelsLike($v['feelslike']['metric']);

            $precip = [];

            isset($v['qpf']['metric']) && $precip['amount'] = $v['qpf']['metric'];
            isset($v['pop']) && $precip['probability']      = $v['pop'];
            $h->setPrecip($precip);
            $snow = [];

            isset($v['snow']['metric']) && $snow['amount'] = $v['snow']['metric'];
            $h->setSnow($snow);

            isset($v['heatindex']['metric']) && $h->setHeat_index($v['heatindex']['metric']);
            // var_dump($h);
            $ret[] = $h;
        }

        return $ret;
    }

    public function getSunAndMoon()
    {
        $j = json_decode($this->rawData, 1);
        if (!isset($j['moon_phase'])) {
            return false;
        }
        $m = $j['moon_phase'];
        // var_dump($m);
        $ret = [];

        isset($m['sunrise']) && $ret['sunrise']   = $m['sunrise']['hour'] . ':' . $m['sunrise']['minute'];
        isset($m['sunset']) && $ret['sunset']     = $m['sunset']['hour'] . ':' . $m['sunset']['minute'];
        isset($m['moonrise']) && $ret['moonrise'] = $m['moonrise']['hour'] . ':' . $m['moonrise']['minute'];
        isset($m['moonset']) && $ret['moonset']   = $m['moonset']['hour'] . ':' . $m['moonset']['minute'];

        isset($m['percentIlluminated']) && $ret['percentIlluminated'] = $m['percentIlluminated'];

        isset($m['ageOfMoon']) && $ret['ageOfMoon']     = $m['ageOfMoon'];
        isset($m['phaseofMoon']) && $ret['phaseofMoon'] = $m['phaseofMoon'];
        isset($m['hemisphere']) && $ret['hemisphere']   = $m['hemisphere'];

        // var_dump($ret);die;

        return $ret;
    }

    public function getLocation()
    {
        $j = json_decode($this->rawData, 1);
        if (!isset($j['current_observation']['display_location'])) {
            return false;
        }
        $l = $j['current_observation']['display_location'];
        $o = $j['current_observation']['observation_location'];
        // $l
        $ret = $this->location;

        isset($l['city']) && $ret->setCity($l['city']);
        isset($l['full']) && $ret->setFull_text($l['full']);
        isset($l['state_name']) && $ret->setstate($l['state_name']);
        isset($l['country_iso3166']) && $ret->setCountryCode($l['country_iso3166']);
        isset($l['zip']) && $ret->setZipcode($l['zip']);
        isset($l['wmo']) && $ret->setWmo($l['wmo']);
        isset($o['country']) && $ret->setCountry($o['country']);

        // var_dump($ret);die;

        return $ret;
    }

    public function getWeatherCode($weather = '')
    {
        if (!is_string($weather) || empty($weather)) {
            return WeatherCode::NOT_AVAILABLE;
        }
        $weather = strtolower($weather);

        switch ($weather) {
            case 'chanceflurries':
                return weatherCode::SNOW_FLURRIES;
                break;
            case 'chancerain':
                return weatherCode::SHOWERS;
                break;
            case 'chancesleet':
                return weatherCode::SLEET;
                break;
            case 'chancesnow':
                return weatherCode::SNOW;
                break;
            case 'chancetstorms':
                return weatherCode::THUNDERSTORMS;
                break;
            case 'clear':
                return WeatherCode::FAIR_DAY;
                break;
            case 'cloudy':
                return WeatherCode::CLOUDY;
                break;
            case 'flurries':
                return WeatherCode::SNOW_FLURRIES;
                break;
            case 'fog':
                return WeatherCode::FOGGY;
                break;
            case 'hazy':
                return WeatherCode::HAZE;
                break;
            case 'mostlycloudy':
                return weatherCode::MOSTLY_CLOUDY_DAY;
                break;
            case 'mostlysunny':
                return weatherCode::FAIR_DAY;
                break;
            case 'partlycloudy':
                return weatherCode::PARTLY_CLOUD;
                break;
            case 'partlysunny':
                return weatherCode::PARTLY_CLOUDY_DAY;
                break;
            case 'sleet':
                return WeatherCode::SLEET;
                break;
            case 'rain':
                return WeatherCode::SHOWERS;
                break;
            case 'snow':
                return WeatherCode::SNOW;
                break;
            case 'sunny':
                return WeatherCode::SUNNY;
                break;
            case 'tstorms':
                return WeatherCode::THUNDERSTORMS;
                break;
            case 'cloudy':
                return WeatherCode::CLOUDY;
                break;
            default:
                return WeatherCode::NOT_AVAILABLE;
                break;
        }
    }

    public function getLangCode($lang = 'en')
    {

        $map = require LIB_DIR . '/Weatherlib/Lang/lang_map_wu.php';
        if (isset($map[$lang])) {
            $ret = $map[$lang];
        } elseif (isset($map[substr($lang, 0, 3)])) {
            $ret = $map[substr($lang, 0, 3)];
        } else {
            $ret = 'EN';
        }
        // var_dump($lang, $ret, $map);die;

        return $ret;
    }
}
