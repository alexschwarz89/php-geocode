<?php

namespace KamranAhmed\Geocode;

/**
 * A wrapper around Google's Geocode API that parses the address,
 * to get different details regarding the address
 *
 * @author  Kamran Ahmed <kamranahmed.se@gmail.com>
 * @license http://www.opensource.org/licenses/MIT
 * @version v2.0
 */
class Geocode
{
    /**
     * API URL through which the address will be obtained.
     */
    private $serviceUrl = "://maps.googleapis.com/maps/api/geocode/json?";

    /**
     * Array containing the query results
     */
    private $serviceResults;

    /**
     * Contains an ISO 3166-A-2 Country Code
     *
     * @var string
     */
    private $region;

    /**
     * Constructor
     *
     * @param string $key Google Maps Geocoding API key
     */
    public function __construct($key = '')
    {
        $this->serviceUrl = (!empty($key))
            ? 'https' . $this->serviceUrl . "key={$key}"
            : 'http' . $this->serviceUrl;
    }

    /**
     * Sets google maps region parameter
     * Example: "US" for United States
     *
     * @param $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * Returns the private $serviceUrl
     *
     * @return string The service URL
     */
    public function getServiceUrl()
    {
        return $this->serviceUrl;
    }

    /**
     * Sends request to the passed Google Geocode API URL and fetches the address details and returns them
     *
     * @param $address
     *
     * @return   bool|object false if no data is returned by URL and the detail otherwise
     * @throws   \Exception
     * @internal param string $url Google geocode API URL containing the address or latitude/longitude
     */
    public function get($address)
    {
        if (empty($address)) {
            throw new \Exception("Address is required in order to process");
        }

        $url = $this->getServiceUrl() . "&address=" . urlencode($address);

        if (!empty($this->region)) {
            $url .= '&region=' . urlencode($this->region);
        }

        $ch  = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $serviceResults = json_decode(curl_exec($ch));
        if ($serviceResults && $serviceResults->status === 'OK') {
            $this->serviceResults = $serviceResults;

            return new Location($address, $this->serviceResults);
        }

        return new Location($address, new \stdClass);
    }
}
