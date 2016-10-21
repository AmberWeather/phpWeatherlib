<?php

/**
 * @package: weather
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/10
 */
namespace Weatherlib\Model;

use Weatherlib\Util\Base;

class Location extends Base {

    private $id;
    private $geohash;
    public $city;
    public $latitude;
    public $longitude;
    private $osmid;
    private $owmid;
    private $accuid;
    private $yhcode;
    private $amberid;
    private $fcaid;
    public $state;
    public $country;
    public $countryCode;
    public $lang;
    public $full_text;
    public $wmo;
    public $zipcode;
    public $timezone_full;
    public $timezone;

    public function __construct($data = [
                                'id' => '',
                                'geohash' => '',
                                'city' => '',
                                'latitude' => '',
                                'longitude' => '',
                                'state' => '',
                                'country' => '',
                                'countryCode' => '',
                                'lang' => '',
                                'full_text' => '',
                                'timezone' => '',
                                'timezone_full' => '',
                                'osmid' => '',
                                'accuid' => '',
                                'owmid' => '',
                                'yhcode' => '',
                                'wmo' => '',
                                'zipcode' => ''
                                ]) {
        if (is_array($data) && !empty($data)) {
            $this->setValue($data);
        }
    }

    public function setValue($data = [
                                'id' => '',
                                'geohash' => '',
                                'city' => '',
                                'latitude' => '',
                                'longitude' => '',
                                'state' => '',
                                'country' => '',
                                'countryCode' => '',
                                'lang' => '',
                                'full_text' => '',
                                'timezone' => '',
                                'timezone_full' => '',
                                'osmid' => '',
                                'accuid' => '',
                                'owmid' => '',
                                'yhcode' => '',
                                'wmo' => '',
                                'zipcode' => ''
                                ]) {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
    }

    public function getID() {
        return $this->ID;
    }

    public function setID($value) {
        $this->ID = $value;
    }

    public function getOsmid() {
        return $this->osmid;
    }

    public function setOsmid($value) {
        $this->osmid = $value;
    }

    public function getOwmid() {
        return $this->owmid;
    }

    public function setOwmid($value) {
        $this->owmid = $value;
    }

    public function getAccuid() {
        return $this->accuid;
    }

    public function setAccuid($value) {
        $this->accuid = $value;
    }

    public function getYhcode() {
        return $this->yhcode;
    }

    public function setYhcode($value) {
        $this->yhcode = $value;
    }

    public function getAmberid() {
        return $this->amberid;
    }

    public function setAmberid($value) {
        $this->amberid = $value;
    }

    public function getFcaid() {
        return $this->fcaid;
    }

    public function setFcaid($value) {
        $this->fcaid = $value;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($value) {
        $this->city = $value;
    }

    public function getLang() {
        return $this->lang;
    }

    public function setLang($value) {
        $this->lang = $value;
    }

    public function getState() {
        return $this->state;
    }

    public function setState($value) {
        $this->state = $value;
    }

    public function getCountry() {
        return $this->country;
    }

    public function setCountry($value) {
        $this->country = $value;
    }

    public function getCountryCode() {
        return $this->countryCode;
    }

    public function setCountryCode($value) {
        $this->countryCode = $value;
    }

    public function getFull_text() {
        return $this->full_text;
    }

    public function setFull_text($value) {
        $this->full_text = $value;
    }

    public function getWmo() {
        return $this->wmo;
    }

    public function setWmo($value) {
        $this->wmo = $value;
    }

    public function getLatitude() {
        return $this->latitude;
    }

    public function setLatitude($value) {
        $this->latitude = $value;
    }

    public function getLongitude() {
        return $this->longitude;
    }

    public function setLongitude($value) {
        $this->longitude = $value;
    }

    public function getZipcode() {
        return $this->zipcode;
    }

    public function setZipcode($value) {
        $this->zipcode = $value;
    }

    public function getTimezone() {
        return $this->timezone;
    }

    public function setTimezone($value) {
        $this->timezone = $value;
    }

    public function getTimezone_full() {
        return $this->timezone_full;
    }

    public function setTimezone_full($value) {
        $this->timezone_full = $value;
    }
}
