<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Src\VkAuth;
use Src\Logger;
use Src\VkTokenManager;

try
{   
    if (!isset($_REQUEST)) {
        die();
    }
    
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $vkAuth = new VkAuth($_ENV['VK_APP_ID'], $_ENV['VK_APP_SECRET'], $_ENV['VK_REDIRECT_URI'], '5.131');

    $data = $_REQUEST;

    if(isset($data['code']))
    {
        $res = $vkAuth->getAccessToken($data['code']);
        Logger::log($res, 'test', __DIR__ . '/test_vk.log');
        if(isset($res['access_token']))
        {
            $tokenManager = new VkTokenManager(__DIR__ . '/token_data.json');
            $tokenManager->putTokenDataInFile(json_encode($res));
            echo 'Авторизация завершена';
        }
        else
        {
            Logger::log($res, 'No access token', __DIR__ . '/errors_vk_auth.log');
        }
    }
    else
    {
        echo "<a href=\"" . $vkAuth->getAuthUrl() . "\">Авторизируйтесь, пожалуйста</a>";
    }

}catch(Exception $e){
    Logger::log($e->getMessage(), 'Exception', __DIR__ . '/errors_vk_auth.log');
}
?>