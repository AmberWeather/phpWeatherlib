<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/12
 */
namespace Weatherlib\Provider;

use Weatherlib\Provider\WundergroundProvider;

use Weatherlib\Model\Location;

/**
 * This clss is a creater of provider.
 * You should add a new provider in getProvier() method if you want to use other provider class.
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/12
 */
class ProviderFactory {

    private function __construct() {
        return false;
    }

    /**
     * [getProvider description]
     * @param  string $provider which provider object you want
     * @param  object $location object of Location
     * @return object           object of Provier
     */
    public static function getProvider($provider = 'wunderground', $location) {

        if (!($location instanceof Location)) {
            return false;
        }
        if ($provider === 'wunderground') {
            return new WundergroundProvider($location);
        }
        return false;
    }
}
