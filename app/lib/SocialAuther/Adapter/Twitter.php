<?php

namespace SocialAuther\Adapter;

class Twitter extends AbstractAdapter
{
    /**
     * Social Public Key
     *
     * @var string|null
     */
    protected $publicKey = null;

    /**
     * Constructor.
     *
     * @param array $config
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($config)
    {
        define('CONSUMER_KEY', 'FHQdaEzQuwtC8gnmfUuwKDe60');
        define('CONSUMER_SECRET', '36ofMIjissKzotqOaYs4A2xFiHssVHxkKwYqDWHB8SkA5Ivpqc');

        // адрес получения токена запроса
        define('REQUEST_TOKEN_URL', 'https://api.twitter.com/oauth/request_token');
        // адрес аутентификации
        define('AUTHORIZE_URL', 'https://api.twitter.com/oauth/authorize');
        // адрес получения токена доступа
        define('ACCESS_TOKEN_URL', 'https://api.twitter.com/oauth/access_token');
        // адрес API получения информации о пользователе
        define('ACCOUNT_DATA_URL', 'https://api.twitter.com/1.1/users/show.json');

        // колбэк, адрес куда должен будет перенаправлен пользователь, после аутентификации
        define('CALLBACK_URL', 'http://brisq.sinergo.ru/socialData/socialAuthSend?provider=twitter');

        $this->provider = 'twitter';
    }

    /**
     * возвращает массив параметров пользователя из социальной сети
     *
     * @return array
     */
    public function getSocParams()
    {
        $result = array();

        
        $result = $this->userInfo;
        

        return $result;
    }


    /**
     * Authenticate and return bool result of authentication
     *
     * @return bool
     */
    public function authenticate()
    {
        $result = false;

        if (!empty($_GET['oauth_token']) && !empty($_GET['oauth_verifier'])) {
            define('URL_SEPARATOR', '&');
            $oauth_nonce = md5(uniqid(rand(), true));
            $oauth_timestamp = time();

            // получаем oauth_token пришедший после перенаправления от Twitter-а
            $oauth_token = $_GET['oauth_token'];
            // получаем oauth_verifier пришедший после перенаправления от Twitter-а
            $oauth_verifier = $_GET['oauth_verifier'];


            $oauth_base_text = "GET&";
            $oauth_base_text .= urlencode(ACCESS_TOKEN_URL)."&";

            $params = array(
                'oauth_consumer_key=' . CONSUMER_KEY . URL_SEPARATOR,
                'oauth_nonce=' . $oauth_nonce . URL_SEPARATOR,
                'oauth_signature_method=HMAC-SHA1' . URL_SEPARATOR,
                'oauth_token=' . $oauth_token . URL_SEPARATOR,
                'oauth_timestamp=' . $oauth_timestamp . URL_SEPARATOR,
                'oauth_verifier=' . $oauth_verifier . URL_SEPARATOR,
                'oauth_version=1.0'
            );

            $key = CONSUMER_SECRET . URL_SEPARATOR;
            $oauth_base_text = 'GET' . URL_SEPARATOR . urlencode(ACCESS_TOKEN_URL) . URL_SEPARATOR . implode('', array_map('urlencode', $params));
            $oauth_signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

            $params = array(
                'oauth_nonce=' . $oauth_nonce,
                'oauth_signature_method=HMAC-SHA1',
                'oauth_timestamp=' . $oauth_timestamp,
                'oauth_consumer_key=' . CONSUMER_KEY,
                'oauth_token=' . urlencode($oauth_token),
                'oauth_verifier=' . urlencode($oauth_verifier),
                'oauth_signature=' . urlencode($oauth_signature),
                'oauth_version=1.0'
            );
            $url = ACCESS_TOKEN_URL . '?' . implode('&', $params);

            $response = file_get_contents($url);
            parse_str($response, $response);

            $oauth_nonce = md5(uniqid(rand(), true));
            $oauth_timestamp = time();

            $oauth_token = $response['oauth_token'];
            $oauth_token_secret = $response['oauth_token_secret'];
            $screen_name = $response['screen_name'];

            $params = array(
                'oauth_consumer_key=' . CONSUMER_KEY . URL_SEPARATOR,
                'oauth_nonce=' . $oauth_nonce . URL_SEPARATOR,
                'oauth_signature_method=HMAC-SHA1' . URL_SEPARATOR,
                'oauth_timestamp=' . $oauth_timestamp . URL_SEPARATOR,
                'oauth_token=' . $oauth_token . URL_SEPARATOR,
                'oauth_version=1.0' . URL_SEPARATOR,
                'screen_name=' . $screen_name
            );
            $oauth_base_text = 'GET' . URL_SEPARATOR . urlencode(ACCOUNT_DATA_URL) . URL_SEPARATOR . implode('', array_map('urlencode', $params));

            $key = CONSUMER_SECRET . '&' . $oauth_token_secret;
            $signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

            $params = array(
                'oauth_consumer_key=' . CONSUMER_KEY,
                'oauth_nonce=' . $oauth_nonce,
                'oauth_signature=' . urlencode($signature),
                'oauth_signature_method=HMAC-SHA1',
                'oauth_timestamp=' . $oauth_timestamp,
                'oauth_token=' . urlencode($oauth_token),
                'oauth_version=1.0',
                'screen_name=' . $screen_name
            );

            $url = ACCOUNT_DATA_URL . '?' . implode(URL_SEPARATOR, $params);

            $response = file_get_contents($url);

            // преобразуем json в массив
            $user_data = json_decode($response,true);

            if (isset($user_data['id'])) {
                $this->userInfo = $user_data;
                $result = true;
            }
            
        }

        

        return $result;
    }

    /**
     * Prepare params for authentication url
     *
     * @return array
     */
    public function prepareAuthParams()
    {
        define('URL_SEPARATOR', '&');

        // хэш случайной строки
        $oauth_nonce = md5(uniqid(rand(), true));

        // текущее время
        $oauth_timestamp = time();

        // формируем набор параметров
        $params = array(
            'oauth_callback=' . urlencode(CALLBACK_URL) . URL_SEPARATOR,
            'oauth_consumer_key=' . CONSUMER_KEY . URL_SEPARATOR,
            'oauth_nonce=' . $oauth_nonce . URL_SEPARATOR,
            'oauth_signature_method=HMAC-SHA1' . URL_SEPARATOR,
            'oauth_timestamp=' . $oauth_timestamp . URL_SEPARATOR,
            'oauth_version=1.0'
        );

        // склеиваем все параметры, применяя к каждому из них функцию urlencode
        $oauth_base_text = implode('', array_map('urlencode', $params));

        // специальный ключ
        $key = CONSUMER_SECRET . URL_SEPARATOR;

        // формируем общий текст строки
        $oauth_base_text = 'GET' . URL_SEPARATOR . urlencode(REQUEST_TOKEN_URL) . URL_SEPARATOR . $oauth_base_text;

        // хэшируем с помощью алгоритма sha1
        $oauth_signature = base64_encode(hash_hmac('sha1', $oauth_base_text, $key, true));

        // готовим массив параметров
        $params = array(
            URL_SEPARATOR . 'oauth_consumer_key=' . CONSUMER_KEY,
            'oauth_nonce=' . $oauth_nonce,
            'oauth_signature=' . urlencode($oauth_signature),
            'oauth_signature_method=HMAC-SHA1',
            'oauth_timestamp=' . $oauth_timestamp,
            'oauth_version=1.0'
        );

        // склеиваем параметры для формирования url
        $url = REQUEST_TOKEN_URL . '?oauth_callback=' . urlencode(CALLBACK_URL) . implode('&', $params);

        // Отправляем GET запрос по сформированному url
        $response = file_get_contents($url);

        // Парсим ответ
        parse_str($response, $response);

        // записываем ответ в переменные
        $oauth_token = $response['oauth_token'];
        $oauth_token_secret = $response['oauth_token_secret'];


        return array(
            'auth_url'    => AUTHORIZE_URL,
            'auth_params' => array(
                'oauth_token'     => $oauth_token
            )
        );
    }
}