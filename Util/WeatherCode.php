<?php
/**
 * @date: 2015/12/08
 * @author: Tiger <DropFan@Gmail.com>
 */
namespace Weatherlib\Util;

/**
 *
 */
class WeatherCode
{

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

    private function __construct($code = 'w_999', $lang = 'en')
    {
        // code...
    }

    public static function getTrans($code = 'w_999', $lang = 'zh')
    {
        // global $weatherCodeMap;
        // return $weatherCodeMap[$lang][$code];
        $c = "Weatherlib\Lang\WeatherCode_$lang";

        return $a;
    }
}
