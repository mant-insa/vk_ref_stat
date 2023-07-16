<?php

namespace Src;

use Src\Api;

class VkAuth
{
    private $id;
    private $secret;
    private $redirectUri;
    private $apiVersion;

    public function __construct($id, $secret, $redirectUri, $apiVersion)
    {
        $this->id = $id;
        $this->secret = $secret;
        $this->redirectUri = $redirectUri;
        $this->apiVersion = $apiVersion;
    }

    public function getAccessToken($code)
    {
        return API::makeRequest(
            "https://oauth.vk.com/access_token",
            [
                "client_id"     => $this->id,
                "client_secret" => $this->secret,
                "redirect_uri"  => $this->redirectUri,
                "code"          => $code,
            ],
            'GET'
        );
    }

    public function getAuthUrl()
    {
        return "https://oauth.vk.com/authorize?client_id=$this->id&display=page&redirect_uri=$this->redirectUri&scope=offline&response_type=code&v=$this->apiVersion";
    }
}