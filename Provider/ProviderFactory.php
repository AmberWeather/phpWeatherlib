<?php
/**
 * @date: 2015/12/12
 * @author: Tiger <DropFan@Gmail.com>
 */
namespace Weatherlib\Provider;

use Weatherlib\Model\Location;
use Weatherlib\Provider\WundergroundProvider;
use Weatherlib\Provider\AmberweatherProvider;

/**
 * This class is a creater of provider.
 * You should add a new provider in getProvier() method if you want to use other provider class.
 * @date: 2015/12/12
 * @author: Tiger <DropFan@Gmail.com>
 */
class ProviderFactory
{

    private function __construct()
    {
        return false;
    }

    /**
     * [getProvider description]
     * @param  string         $provider which provider object you want
     * @param  Location|null  $location object of Location
     * @return Provider|false object of Provier
     */
    public static function getProvider($provider = '', Location $location = null)
    {
        if (!($location instanceof Location)) {
            return false;
        }

        $wprovider = null;

        switch ($provider) {
            case 'wunderground':
                $wprovider = new WundergroundProvider($location);
                break;
            case 'amberweather':
                $wprovider = new AmberweatherProvider($location);
                break;
            default:
                throw new \Exception('Invalid weather data provider!');
                break;
        }

        return $wprovider;
    }
}
