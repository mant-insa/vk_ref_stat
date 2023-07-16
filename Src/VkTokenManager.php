<?php

namespace Src;

class VkTokenManager
{
    public function __construct(private string $path)
    {}

    public function getAccessToken()
    {
        return $this->getAllTokenData($this->path)['access_token'];
    }

    public function getAllTokenData()
    {
        $rawTokenData = file_get_contents($this->path);
        if($rawTokenData === false)
        {
            throw new \Exception("File $this->path doesn't contain token data!");
        }
        return json_decode($rawTokenData, true);
    }

    public function putTokenDataInFile($tokenData)
    {
        file_put_contents($this->path, $tokenData);
    }
}