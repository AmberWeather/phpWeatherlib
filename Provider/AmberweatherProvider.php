<?php
/**
 * @date: 2016/10/20
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

class AmberweatherProvider extends Provider
{

    private $appid = 'testweb';
    private $token = 'just4test';

    private $params = [
        'lid'  => '20101816670',
        'lat'  => '39.558157',
        'lon'  => '116.529225',
        'lang' => 'en',
    ];
    private $settings = '';
    private $query    = 'lat=39.891506&lon=116.509321';
    private $format   = 'json';

    public function __construct(Location $location = null, $apiKey = '', $apptoken = '', $format = 'json')
    {
        parent::__construct($location);

        $this->baseUrl  = Config::AmberWS['baseurl'];
        $this->appid    = (string) $apiKey ?: Config::AmberWS['appid'];
        $this->apptoken = (string) $apptoken ?: Config::AmberWS['token'];

        $this->format = (string) $format ?: 'json';
    }

    public function buildUrl()
    {
        $this->buildParams();
        $this->queryUrl = "{$this->baseUrl}?{$this->query}&appid={$this->appid}&token={$this->token}";
    }

    public function buildParams()
    {
        $this->params['lid']  = (string) $this->location->amberid ?: '';
        $this->params['lat']  = (string) $this->location->latitude ?: '';
        $this->params['lon']  = (string) $this->location->longitude ?: '';
        $this->params['lang'] = (string) $this->location->lang ?: 'en';

        $s = [];
        foreach ($this->params as $k => $v) {
            if (!empty($v)) {
                $s[] = "{$k}={$v}";
            }
        }
        $this->query = implode('&', $s);
    }

    public function checkResult()
    {

        $j = json_decode($this->rawData, 1);
        // var_dump($j);

        if (!$j) {
            $this->errno = 2001;
            $this->error = 'Fetched data decode failed, maybe it is not json format.';

            return false;
        }

        if (isset($j['error']['error'])) {
            $this->error = $j['error'];

            # other error return by wunderground
            $this->errno = $j['errno'];
            $this->error = $err['description'];
        }

        if (isset($j['status']) && $j['status'] == 'ok') {
            if (!empty($j['cc']) && !empty($j['fch']) && !empty($j['fcd'])) {
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

        return $j->cc;
    }

    public function getRawDailyForecast()
    {
        $j = json_decode($this->rawData);

        return $j->fcd;
    }

    public function getRawHourlyForecast()
    {
        $j = json_decode($this->rawData);

        return $j->fch;
    }

    public function getCurrentCondition()
    {
        $j = json_decode($this->rawData, 1);
        // var_dump($j);
        if (!isset($j['cc'])) {
            return false;
        }
        $c = $j['cc'];

        // var_dump($c);
        $ret = new Current();
        isset($c['dt']) && $ret->setObservation_time(strtotime($c['dt']));
        isset($c['txt']) && $ret->setWeather($c['txt']);
        isset($c['s']) && $ret->setWeatherID($this->getWeatherCode($c['s']));
        isset($c['txt']) && $ret->setDescription($c['txt']);
        isset($c['t']) && $ret->setTemperature($c['t']);
        isset($c['rh']) && $ret->setHumidity($c['rh']);

        $w = [];

        isset($c['ws']) && $w['speed']                = $c['ws'];
        isset($c['wn']) && $w['direction']            = $c['wn'];
        isset($c['wind_degrees']) && $w['degrees']    = $c['wind_degrees'];
        isset($c['wind_gust_kph']) && $w['gust']      = $c['wind_gust_kph'];
        isset($c['wind_string']) && $w['description'] = $c['wind_string'];
        $ret->setWind($w);

        # 1 mbar = 1 hPa = 0.1 kPa = 0.76 mmHg = 0.03 inHg
        isset($c['p']) && $ret->setPressure($c['p']);
        isset($c['prestend']) && $ret->setPressure_trend($c['prestend']);
        isset($c['td']) && $ret->setDewPoint($c['td']);
        isset($c['windchill_c']) && $ret->setWindchill($c['windchill_c']);
        isset($c['tf']) && $ret->setFeelsLike($c['tf']);
        isset($c['v']) && $ret->setVisibility($c['v']);
        isset($c['uv']) && $ret->setUV($c['uv']);
        isset($c['c']) && $ret->setCloudcover($c['c']);
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
        if (!isset($j['fcd']) || !is_array($j['fcd'])) {
            return false;
        }
        // var_dump($j);

        $dl  = $j['fcd'];
        $ret = new DailyForecastList();
        foreach ($dl as $k => $v) {
            // var_dump($dl_txt[$k], $dl_txt[$k+1]);
            // var_dump($dl[$k]);
            $d = new DailyForecast();
            // $d->setDescription();
            isset($v['dt']) && $d->setTime(strtotime($v['dt']));
            isset($v['txt']) && $d->setWeather($v['txt']);
            isset($v['s']) && $d->setWeatherID($this->getWeatherCode($v['s']));

            $t = [];

            isset($v['tx']) && $t['high'] = $v['tx'];
            isset($v['tn']) && $t['low']  = $v['tn'];
            $d->setTemperature($t);

            $precip = [];

            isset($v['pr']) && $precip['amount']        = $v['pr'];
            isset($v['prd']) && $precip['amount_day']   = $v['prd'];
            isset($v['prn']) && $precip['amount_night'] = $v['prn'];
            isset($v['pp']) && $precip['probability']   = $v['pp'];
            $d->setPrecip($precip);

            $wind = [];

            isset($v['wsx']) && $wind['speed']       = $v['wsx'];
            isset($v['wn']) && $wind['direction']    = $v['wn'];
            isset($v['degrees']) && $wind['degrees'] = $v['degrees'];
            $d->setWind($wind);

            $wind_max = [];

            isset($v['wsx']) && $wind_max['speed']       = $v['wsx'];
            isset($v['wn']) && $wind_max['direction']    = $v['wn'];
            isset($v['degrees']) && $wind_max['degrees'] = $v['degrees'];
            $d->setWind_max($wind_max);

            $humidity = [];

            isset($v['rh_avg']) && $humidity['high'] = $v['rh_avg'];
            isset($v['rh_avg']) && $humidity['low']  = $v['rh_avg'];
            isset($v['rh_avg']) && $humidity['ave']  = $v['rh_avg'];
            $d->setHumidity($humidity);

            isset($v['uv']) && $d->setUV($v['uv']);
            isset($v['ca']) && $d->setCloudcover($v['ca']);
            isset($v['p']) && $d->setPressure($v['p']);
            $ret[] = $d;
        }

        return $ret;
    }

    public function getHourlyForecast()
    {
        $j = json_decode($this->rawData, 1);
        if (!isset($j['fch'])) {
            return false;
        }
        $hl  = $j['fch'];
        $ret = new HourlyForecastList();
        foreach ($hl as $k => $v) {
            $h = new HourlyForecast();
            // var_dump($v);
            isset($v['dt']) && $h->setTime(strtotime($v['dt']));
            isset($v['txt']) && $h->setWeather($v['txt']);
            isset($v['s']) && $h->setWeatherID($this->getWeatherCode($v['s']));
            isset($v['t']) && $h->setTemperature($v['t']);
            isset($v['td']) && $h->setDewpoint($v['td']);

            $w = [];

            isset($v['ws']) && $w['speed']        = $v['ws'];
            isset($v['wn']) && $w['direction']    = $v['wn'];
            isset($v['degrees']) && $w['degrees'] = $v['degrees'];
            $h->setWind($w);

            isset($v['p']) && $h->setPressure($v['p']);
            isset($v['rh']) && $h->setHumidity($v['rh']);
            // isset($v['windchill']) && $h->setWindchill($v['windchill']);
            isset($v['tf']) && $h->setFeelsLike($v['tf']);

            $precip = [];

            isset($v['pr']) && $precip['amount']      = $v['pr'];
            isset($v['pp']) && $precip['probability'] = $v['pp'];
            $h->setPrecip($precip);

            isset($v['c']) && $h->setCloudcover($v['c']);

            isset($v['v']) && $h->setVisibility($v['v']);
            isset($v['uv']) && $h->setUV($v['uv']);
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
        if (!isset($j['loc'])) {
            return false;
        }
        $l = $j['loc'];
        // $l
        $ret = $this->location;

        isset($l['name']) && $ret->setCity($l['name']);
        isset($l['name']) && $ret->setFull_text("{$l['name']}, {$l['adm1']}");
        isset($l['adm1']) && $ret->setstate($l['adm1']);
        isset($l['cc']) && $ret->setCountryCode(strtoupper($l['cc']));
        isset($l['tzname']) && $ret->setTimezone_full($l['tzname']);
        isset($l['tz']) && $ret->setTimezone($l['tz']);
        isset($l['lid']) && $ret->setAmberid($l['lid']);
        // isset($o['country']) && $ret->setCountry($o['country']);
        isset($l['lat']) && $ret->setLatitude($l['lat']);
        isset($l['lon']) && $ret->setLongitude($l['lon']);
        // var_dump($ret);die;

        return $ret;
    }

    public function getWeatherCode($symbol = '')
    {
        if (!is_string($symbol) || empty($symbol)) {
            return WeatherCode::NOT_AVAILABLE;
        }
        $weather = strtolower($symbol);

        if (strlen($symbol) === 4 && ($symbol{0} === 'd' || $symbol{0} == 'n')) {
            $symbol = trim($symbol, 'dn');
        }
        switch ($symbol) {
            case '000':
            case '100':
            case '110':
            case '111':
            case '112':
            case '120':
            case '121':
            case '122':
            case '130':
            case '131':
            case '132':
            case '140':
            case '141':
            case '142':
                return WeatherCode::SUNNY;
                break;
            case '200':
                return WeatherCode::PARTLY_CLOUD;
                break;
            case '300':
                return WeatherCode::CLOUDY;
                break;
            case '400':
                return WeatherCode::MOSTLY_CLOUDY_DAY;
                break;
            case '210':
            case '310':
            case '410':
                return WeatherCode::DRIZZLE;
                break;
            case '211':
            case '311':
            case '411':
                return WeatherCode::LIGHT_SNOW_SHOWERS;
                break;
            case '212':
            case '312':
            case '412':
                return WeatherCode::LIGHT_SNOW_SHOWERS;
                break;
            case '220':
            case '320':
            case '420':
                return WeatherCode::SHOWERS;
                break;
            case '221':
            case '321':
            case '421':
                return WeatherCode::SLEET;
                break;
            case '222':
            case '322':
            case '422':
                return WeatherCode::SNOW_SHOWERS;
                break;
            case '230':
            case '330':
            case '430':
                return WeatherCode::SHOWERS;
                break;
            case '231':
            case '331':
            case '431':
                return WeatherCode::SNOW_SHOWERS;
                break;
            case '232':
            case '332':
            case '432':
                return WeatherCode::SNOW;
                break;
            case '240':
            case '241':
            case '242':
            case '340':
            case '341':
            case '342':
            case '440':
            case '441':
            case '442':
                return WeatherCode::THUNDERSTORMS;
                break;
            case '408':
                return WeatherCode::TORNADO;
                break;
            case '423':
                return WeatherCode::HAIL;
                break;
            case '424':
                return WeatherCode::FREEZING_RAIN;
                break;
            case '443':
                return WeatherCode::THUNDERSTORMS;
                break;
            case '600':
            case '700':
            case '800':
                return WeatherCode::FOGGY;
                break;
            case '605':
            case '606':
            case '705':
            case '706':
                return WeatherCode::DUST;
                break;
            case '607':
            case '707':
                return WeatherCode::SMOKY;
                break;
            case '900':
            case '905':
            case '906':
            case '907':
                return WeatherCode::HAZE;
                break;
            default:
                return WeatherCode::NOT_AVAILABLE;
                break;
        }
        /*

    const TORNADO                   = 'w_000';
    const TROPICAL_STORM            = 'w_001';
    const HURRICANE                 = 'w_002';
    const SEVERE_THUNDERSTORMS      = 'w_003';
    const THUNDERSTORMS             = 'w_004';
    const MIXED_RAIN_SNOW           = 'w_005';
    const MIXED_RAIN_SLEET          = 'w_006';
    const MIXED_SNOW_SLEET          = 'w_007';
    const FREEZING_DRIZZLE          = 'w_008';
    const DRIZZLE                   = 'w_009';
    const FREEZING_RAIN             = 'w_010';
    const SHOWERS                   = 'w_011';
    const HEAVY_SHOWERS             = 'w_012';
    const SNOW_FLURRIES             = 'w_013';
    const LIGHT_SNOW_SHOWERS        = 'w_014';
    const BLOWING_SNOW              = 'w_015';
    const SNOW                      = 'w_016';
    const HAIL                      = 'w_017';
    const SLEET                     = 'w_018';
    const DUST                      = 'w_019';
    const FOGGY                     = 'w_020';
    const HAZE                      = 'w_021';
    const SMOKY                     = 'w_022';
    const BLUSTERY                  = 'w_023';
    const WINDY                     = 'w_024';
    const COLD                      = 'w_025';
    const CLOUDY                    = 'w_026';
    const MOSTLY_CLOUDY_NIGHT       = 'w_027';
    const MOSTLY_CLOUDY_DAY         = 'w_028';
    const PARTLY_CLOUDY_NIGHT       = 'w_029';
    const PARTLY_CLOUDY_DAY         = 'w_030';
    const CLEAR_NIGHT               = 'w_031';
    const SUNNY                     = 'w_032';
    const FAIR_NIGHT                = 'w_033';
    const FAIR_DAY                  = 'w_034';
    const MIXED_RAIN_AND_HAIL       = 'w_035';
    const HOT                       = 'w_036';
    const ISOLATED_THUNDERSTORMS    = 'w_037';
    const SCATTERED_THUNDERSTORMS   = 'w_038';
    const SCATTERED_THUNDERSTORMS_1 = 'w_039';
    const SCATTERED_SHOWERS         = 'w_040';
    const HEAVY_SNOW                = 'w_041';
    const SCATTERED_SNOW_SHOWERS    = 'w_042';
    const PARTLY_CLOUD              = 'w_044';
    const THUNDERSHOWERS            = 'w_045';
    const SNOW_SHOWERS              = 'w_046';
    const ISOLATED_THUDERSHOWERS    = 'w_047';
    const NOT_AVAILABLE             = 'w_999';
     */
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
