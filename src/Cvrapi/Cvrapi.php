<?php

namespace Cvrapi;

/**
 * CVR API
 *
 * @author Kristian Just Iversen
 * @source http://cvrapi.dk/examples
 */
class Cvrapi {
    
    /**
     * Get company by VAT, P-number or company name.
     * 
     * @param  mixed  $search
     * @param  string $country
     * @param  string $project Optional. Description of your project.
     * @return object
     */
    public static function get($search, $country, $project = 'mit projekt')
    {
        return self::request($search, $country, 'search', $project);
    }
    
    /**
     * Request company information
     * 
     * @param  mixed  $search
     * @param  string $country
     * @param  string $type    Optional. Type of search. Default searches in VAT,
     *                         P-number or company name. Allowed types is 'search',
     *                         'vat', 'name', 'produ' or 'phone'.
     * @param  string $project Optional. Description of your project.
     * @return array|string    Array of data or XML string.
     */
    public static function request($search, $country, $type = 'search', $project = 'mit projekt')
    {
        
        // Validate search term
        if (in_array($type, array('vat', 'produ', 'phone'))) {
            $search = Validate::int($search);
        }

        Validate::search($search);
        Validate::country($country);
        
        // Start cURL
        $ch = curl_init();

        // Determine protocol
        $protocol = Config::$secure ? 'https' : 'http';

        $parameters = Array(
            $type     => $search,
            'country' => $country,
            'version' => Config::$version,
            'format'  => Config::$format,
        );
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $protocol . '://cvrapi.dk/api?' . http_build_query($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $project);

        // Parse result
        $result = curl_exec($ch);

        // Close connection when done
        curl_close($ch);

        // Return our decoded result
        if (Config::$format === 'json') {
            return json_decode($result);
        } else {
            return new \SimpleXMLElement($result);
        }

    }
    
}

