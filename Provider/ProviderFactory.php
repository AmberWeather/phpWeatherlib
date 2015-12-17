<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/12
 */
namespace Weatherlib\Provider;

use Weatherlib\Provider\WundergroundProvider;

use Weatherlib\Model\Location;

/**
*
*/
class ProviderFactory {

    private function __construct() {
        return false;
    }

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
