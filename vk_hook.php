<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Logger;
use Src\Api;
use Src\VkTokenManager;

try{
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    if (!isset($_REQUEST)) {
        die();
    }
    
    //Строка для подтверждения адреса сервера из настроек Callback API
    $confirmation_token = $_ENV['VK_CONFIRMATION_TOKEN'];
    
    //Ключ доступа сообщества
    $tokenManager = new VkTokenManager(__DIR__ . '/token_data.json');
    $token = $tokenManager->getAccessToken();
    
    //Получаем и декодируем уведомление
    $data = json_decode(file_get_contents('php://input'));

    Logger::log($data, 'Request params', __DIR__ . '/request.log');
    
    //Проверяем, что находится в поле "type"
    switch ($data->type) 
    {
        //Если это уведомление для подтверждения адреса...
        case 'confirmation':
            echo $confirmation_token;
            die();
            break;
    
        //Если это уведомление о новом сообщении...
        case 'message_new':
            $messageObject = $data->object->message;
            $user_ref = property_exists($messageObject, 'ref') ? $messageObject->ref : null;
            if($user_ref === null || $user_ref == '')
            {
                Logger::log($data, 'No user_ref', __DIR__ . '/errors_vk.log');
                die();
            }
            //получаем id автора
            $userId = $data->object->message->from_id;
            $params =  [
                'user_ids'      => $userId,
                'fields'        => "domain",
                'access_token'  => $token,
                'v'             => 5.103
            ];
            //затем с помощью users.get получаем данные об авторе
            $userInfo = API::makeRequest(
                "https://api.vk.com/method/users.get",
                $params,
                "GET"
            );
            Logger::log([
                $userInfo,
                $params
            ], 'userInfo', __DIR__ . '/userInfo.log');
            //и извлекаем из ответа его адрес
            $userDomain = $userInfo['response'][0]['domain'];

            if(!file_exists(__DIR__ . "/users/$userDomain.json"))
            {
                file_put_contents(__DIR__ . "/users/$userDomain.json", json_encode(["user_ref" => $user_ref]));
            }
            
            //Возвращаем "ok" серверу Callback API
            echo('ok');
            break; 
    }
}catch(Exception $e){
    Logger::log($e->getMessage(), 'Exception', __DIR__ . '/errors_vk_hook.log');
}

?>