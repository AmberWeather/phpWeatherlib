<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/12
 */
namespace Weatherlib\Provider;

use Weatherlib\Model\Location;
use Weatherlib\Provider\WundergroundProvider;

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
     * @param  string        $provider which provider object you want
     * @param  Location|null $location object of Location
     * @return Provider|false          object of Provier
     */
    public static function getProvider(string $provider = 'wunderground',
                                       Location $location = null) {

        if (!($location instanceof Location)) {
            return false;
        }
        if ($provider === 'wunderground') {
            return new WundergroundProvider($location);
        }
        return false;
    }
}
