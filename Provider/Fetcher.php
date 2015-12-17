<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/10
 */
namespace Weatherlib\Provider;

use Weatherlib\Config as Config;

// use Weatherlib\Exception;
/**
 *
 */
class Fetcher {

    /**
     * curl options array.
     * @see [https://secure.php.net/manual/zh/function.curl-setopt.php]
     * @var array
     */
    private $curlOptions = [];

    /**
     * You can replace this string as your UserAgent.
     * @var string
     */
    private $useragent = Config::CURL_OPT['useragent'];

    /**
     * set your cookies here
     * @var string
     */
    private $cookies = Config::CURL_OPT['cookies'];


    public function __construct($curlOptions = [], $useragent = '', $cookies = '') {
        is_array($curlOptions) && !empty($curlOptions) && $this->curlOptions = $curlOptions;
        !empty($useragent) && $this->useragent = $useragent;
        !empty($cookies) && $this->cookies = $cookies;
    }

    /**
     * Fetch data from url
     * @param  string  $url  target url to fetch
     * @param  integer $mode fetch contents via curl (mode=1) or file_get_contents (other value)
     * @return string        The content fetched from url
     */
    public function fetch($url, $mode = 1) {
        if (1 == $mode) {
            return $this->curlFetch($url);
        } else {
            return $this->fileGetContentsFetch($url);
        }
    }

    public function curlFetch($url, $post = false) {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        !empty($this->useragent) && curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        !empty($this->cookies) && curl_setopt($ch, CURLOPT_COOKIE, $this->cookies);

        if ($post && !empty($post)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, Config::CURL_OPT['CURLOPT_CONNECTTIMEOUT_MS']);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, Config::CURL_OPT['CURLOPT_TIMEOUT_MS']);

        curl_setopt_array($ch, $this->curlOptions);

        $content = curl_exec($ch);

        if (!$content) {
            // var_dump(curl_errno($ch)); //28 timeout
            // echo curl_error($ch);
            //log
        }
        curl_close($ch);
        return $content;
    }

    public function fileGetContentsFetch($url) {
        return file_get_contents($url);
    }
}
