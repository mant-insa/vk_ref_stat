<?php

namespace Src;

class API{

    /**
     * Makes request
     *
     * @param string $url URL of the request
     * @param array $param  Body or query parameters of the request
     * @param string $type  Type of the request (GET, POST)
     * @param array $headers  Headers of the request. The headers of accept and send content types are set according to $sendJson and $acceptJson parameters.
     * @param boolean $sendJson 'true' if the request supposed to send json-data
     * @param boolean $acceptJson 'true' if the request supposed to accept json-data
     * @return mixed Associative array, if $acceptJson = true, raw string response otherwise
     */
    public static function makeRequest($url, $param = [], $type = "POST", $headers = [], $sendJson = false, $acceptJson = true) 
    {
        if($type != "POST" && $type != "GET")
        {
            throw new \Exception("Wrong request type. \"{$type}\" request not allowed");
        }

        $defaultHeaders = [];

        if($sendJson)
        {
            $defaultHeaders[] = "Content-type: application/json";
        }

        if($acceptJson)
        {
            $defaultHeaders[] = 'Accept: application/json';
        }

        $curl = curl_init();
        $opt_array = array(
            CURLOPT_URL            => $type == "POST" ? $url : $url . '?' . http_build_query($param),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER     => array_merge($defaultHeaders, $headers),
        );
        
        if($type == 'POST')
        {
            $opt_array[CURLOPT_CUSTOMREQUEST] = $type;
            $opt_array[CURLOPT_POSTFIELDS] = $sendJson ? json_encode($param) : http_build_query($param);
        }

        curl_setopt_array($curl, $opt_array);

        $response = curl_exec($curl);
        $err      = curl_error($curl);

        curl_close($curl);
        
        if ($err) 
        {
            throw new \Exception("cURL Error #:" . $err);
        } 

        return ($acceptJson ? json_decode($response, true) : $response);
    }
}
