<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/10
 */
namespace Weatherlib\Provider;

use Weatherlib\Model\Location;
use Weatherlib\Model\Current;
use Weatherlib\Model\DailyForecast;
use Weatherlib\Model\DailyForecastList;
use Weatherlib\Model\HourlyForecast;
use Weatherlib\Model\HourlyForecastList;

use Weatherlib\Util\WeatherCode;

use Weatherlib\Config;

/**
*
*/
class WundergroundProvider extends Provider {

    private $params = [
                       'features' => ['astronomy', 'conditions', 'forecast10day', 'hourly10day'],
                       'settings' => ['lang' => 'CN', /*'pwd' => 1, 'bestfct' => 1*/],
                       'query' => '40.0494806,116.4073421',
                       'format' => 'json'
                   ];
    private $features = 'astronomy/conditions/forecast10day/hourly';
    private $settings = 'lang:CN';
    private $query = '40.0494806,116.4073421';
    private $format = 'json';
// https://api.wunderground.com/api/2b0d6572c90d3e4a/lang:CN/astronomy/conditions/forecast10day/hourly/q/40.0494806,116.4073421.json

    public function __construct($location = '', $features = [], $format = 'json', $apiKey = '') {
        parent::__construct($location);
        $this->baseUrl = 'http://api.wunderground.com/api/';
        if (is_array($features) && !empty($features)) {
            $this->params['features'] = $features;
        }
        $this->params['settings']['lang'] = (string)$this->location->lang ?
                                                    $this->getLangCode($this->location->lang):'EN';
        $this->params['format'] = (string)$format ?:'json';
        $this->apiKey = (string)$apiKey ?:Config::API_KEY['Wunderground'];
    }

    public function buildUrl() {
        $this->buildParams();
        $this->queryUrl = "{$this->baseUrl}{$this->apiKey}/{$this->features}/{$this->settings}/q/{$this->query}.{$this->format}";
    }

    public function buildParams() {
        $p = '';
        $this->features = implode('/', $this->params['features']);
        $s = [];
        foreach ($this->params['settings'] as $k => $v) {
            $s[] = $k . ':' . $v;
        }
        $this->settings = implode('/', $s);
        $this->query = $this->location->latitude . ',' . $this->location->longitude;
        $this->format = $this->params['format'];
    }

    public function checkResult() {
        try {
            $j = json_decode($this->rawData, 1);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }

        if (isset($j['response']['error'])) {
            $err = $j['response']['error'];
            //log
            if ($err['type'] == 'querynotfound') {
                //log
                echo $this->queryUrl;
                echo '<br>';
                echo $err['description'];
                return false;
            }
        }

        return true;
    }

    public function getRawCurrentCondition() {
        $j = json_decode($this->rawData);
        return $j->current_observation;
    }

    public function getRawForecast(){
        $j = json_decode($this->rawData);
        return $j->forecast;
    }

    public function getRawDailyForecast() {
        $j = json_decode($this->rawData);
        return $j->forecast;
    }

    public function getRawHourlyForecast() {
        $j = json_decode($this->rawData);
        return $j->hourly_forecast;
    }

    public function getCurrentCondition() {
        $j = json_decode($this->rawData, 1);
        // var_dump($j);
        $c = $j['current_observation'];

        // var_dump($c);
        $ret = new Current();
        $ret->setObservation_time($c['observation_epoch']);
        $ret->setWeather($c['weather']);
        $ret->setWeatherID($this->getWeatherCode($c['icon']));
        $ret->setDescription($c['weather']);
        $ret->setTemperature($c['temp_c']);
        $ret->setHumidity($c['relative_humidity']);
        $w = [
                'speed' => $c['wind_kph'],
                'direction' => $c['wind_dir'],
                'degrees' => $c['wind_degrees'],
                'gust' => $c['wind_gust_kph'],
                'description' => $c['wind_string']
        ];
        $ret->setWind($w);
        $ret->setPressure($c['pressure_mb']); # 1 mbar = 1 hPa = 0.1 kPa = 0.76 mmHg = 0.03 inHg
        $ret->setPressure_trend($c['pressure_trend']);
        $ret->setDewPoint($c['dewpoint_c']);
        $ret->setWindchill($c['windchill_c']);
        $ret->setFeelsLike($c['feelslike_c']);
        $ret->setVisibility($c['visibility_km']);
        $ret->setUV($c['UV']);
        $ret->setSolarradiation($c['solarradiation']);
        $ret->setHeat_index($c['heat_index_c']);

        $ret->setPrecip($c['precip_1hr_metric']);

        // $ret->set
        // echo $this->queryUrl;
        // var_dump($ret);die;
        return $ret;
    }

    public function getDailyForecast() {
        $j = json_decode($this->rawData, 1);
        // var_dump($j);
        $dl_txt = $j['forecast']['txt_forecast']['forecastday'];
        // var_dump($dl_txt);
        $dl = $j['forecast']['simpleforecast']['forecastday'];
        $ret = new DailyForecastList();
        foreach ($dl as $k => $v) {
            // var_dump($dl_txt[$k], $dl_txt[$k+1]);
            // var_dump($dl[$k]);
            $d = new DailyForecast();
            // $d->setDescription();
            $d->setTime($v['date']['epoch']);
            $d->setWeather($v['conditions']);
            $d->setWeatherID($this->getWeatherCode($v['icon']));
            $t = [
                'high' => $v['high']['celsius'],
                'low' => $v['low']['celsius']
            ];
            $d->setTemperature($t);
            $precip = [
                        'amount' => $v['qpf_allday']['mm'],
                        'amount_day' => $v['qpf_day']['mm'],
                        'amount_night' => $v['qpf_night']['mm'],
                        'probability' => $v['pop']
            ];
            $d->setPrecip($precip);
            $snow = [
                    'amount' => $v['snow_allday']['cm'],
                    'amount_day' => $v['snow_day']['cm'],
                    'amount_night' => $v['snow_night']['cm']
            ];
            $d->setSnow($snow);
            $wind = [
                    'speed' => $v['avewind']['kph'],
                    'direction' => $v['avewind']['dir'],
                    'degrees' => $v['avewind']['degrees']
            ];
            $d->setWind($wind);
            $wind_max = [
                    'speed' => $v['maxwind']['kph'],
                    'direction' => $v['maxwind']['dir'],
                    'degrees' => $v['maxwind']['degrees']
            ];
            $d->setWind_max($wind_max);
            $humidity = [
                        'high' => $v['maxhumidity'],
                        'low' => $v['minhumidity'],
                        'ave' => $v['avehumidity']
            ];
            $d->setHumidity($humidity);
            // var_dump($d);
            $ret[] = $d;
        }
        return $ret;
    }

    public function getHourlyForecast() {
        $j = json_decode($this->rawData, 1);
        $hl = $j['hourly_forecast'];
        $ret = new HourlyForecastList();
        foreach ($hl as $k => $v) {
            $h = new HourlyForecast();
            // var_dump($v);
            $h->setTime($v['FCTTIME']['epoch']);
            $h->setWeather($v['condition']);
            $h->setWeatherID($this->getWeatherCode($v['icon']));
            $h->setTemperature($v['temp']['metric']);
            $h->setDewpoint($v['dewpoint']['metric']);
            $w = [
                'speed' => $v['wspd']['metric'],
                'direction' => $v['wdir']['dir'],
                'degrees' => $v['wdir']['degrees']
            ];
            $h->setWind($w);
            $h->setPressure($v['mslp']['metric']);
            $h->setHumidity($v['humidity']);
            $h->setWindchill($v['windchill']['metric']);
            $h->setFeelsLike($v['feelslike']['metric']);
            $precip = [
                'amount' => $v['qpf']['metric'],
                'probability' => $v['pop']
            ];
            $h->setPrecip($precip);
            $snow = [
                'amount' => $v['snow']['metric'],
            ];
            $h->setSnow($snow);
            $h->setHeat_index($v['heatindex']['metric']);
            // var_dump($h);
            $ret[] = $h;
        }
        return $ret;
    }

    public function getSunAndMoon() {
        $j = json_decode($this->rawData, 1);
        $m = $j['moon_phase'];
        // var_dump($m);
        $ret = [
                'sunrise' => $m['sunrise']['hour'] .':'.$m['sunrise']['minute'],
                'sunset' => $m['sunset']['hour'] .':'.$m['sunset']['minute'],
                'moonrise' => $m['moonrise']['hour'] .':'.$m['moonrise']['minute'],
                'moonset' => $m['moonset']['hour'] .':'.$m['moonset']['minute'],
                'percentIlluminated' => $m['percentIlluminated'],
                'ageOfMoon' => $m['ageOfMoon'],
                'phaseofMoon' => $m['phaseofMoon'],
                'hemisphere' => $m['hemisphere']
        ];

        // var_dump($ret);die;
        return $ret;
    }

    public function getWeatherCode($weather) {
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

    public function getLangCode($lang) {

        $map = require(WEATHERLIB_DIR . 'Weatherlib/Lang/lang_map_wu.php');
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
