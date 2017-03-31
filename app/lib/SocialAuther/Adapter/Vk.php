<?php

namespace SocialAuther\Adapter;

class Vk extends AbstractAdapter
{
    public function __construct($config)
    {
        parent::__construct($config);

        $this->socialFieldsMap = array(
            'socialId'   => 'uid',
            'email'      => 'email',
            'avatar'     => 'photo_big',
            'birthday'   => 'bdate'
        );

        $this->provider = 'vk';
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
     * возвращает массив параметров доступа для социальной сети
     * @return array
     */
    public function getAccessParams()
    {
        $result = array();


        $result = $this->accessInfo;


        return $result;
    }

    /**
     * Get user name or null if it is not set
     *
     * @return string|null
     */
    public function getName()
    {
        $result = null;

        if (isset($this->userInfo['first_name']) && isset($this->userInfo['last_name'])) {
            $result = $this->userInfo['first_name'] . ' ' . $this->userInfo['last_name'];
        } elseif (isset($this->userInfo['first_name']) && !isset($this->userInfo['last_name'])) {
            $result = $this->userInfo['first_name'];
        } elseif (!isset($this->userInfo['first_name']) && isset($this->userInfo['last_name'])) {
            $result = $this->userInfo['last_name'];
        }

        return $result;
    }

    /**
     * Get user social id or null if it is not set
     *
     * @return string|null
     */
    public function getSocialPage()
    {
        $result = null;

        if (isset($this->userInfo['screen_name'])) {
            $result = 'http://vk.com/' . $this->userInfo['screen_name'];
        }

        return $result;
    }

    /**
     * Get user sex or null if it is not set
     *
     * @return string|null
     */
    public function getSex()
    {
        $result = null;
        if (isset($this->userInfo['sex'])) {
            $result = $this->userInfo['sex'] == 1 ? 'female' : 'male';
        }

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

        if (isset($_GET['code'])) {
            $params = array(
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $_GET['code'],
                'redirect_uri' => $this->redirectUri
            );

            $tokenInfo = $this->get('https://oauth.vk.com/access_token', $params);
            if (isset($tokenInfo['access_token'])) {

                //запоминаем параметры доступа
                $this->accessInfo = $tokenInfo;

                $params = array(
                    'user_ids'     => $tokenInfo['user_id'],
                    'fields'       => 'uid,sex,bdate,city,country,photo_50,photo_100,photo_200_orig,photo_200,photo_400_orig,photo_max,photo_max_orig,photo_id,online,online_mobile,domain,has_mobile,contacts,connections,site,education,universities,schools,can_post,can_see_all_posts,can_see_audio,can_write_private_message,status,last_seen,common_count,relation,relatives,counters,screen_name,maiden_name,timezone,occupation,activities,interests,music,movies,tv,books,games,about,quotes,personal,friends_status',
                    'name_case'    => 'Nom',
                    'v'            => '5.34',
                    'access_token' => $tokenInfo['access_token']
                );

                $userInfo = $this->get('https://api.vk.com/method/users.get', $params);
                if (isset($userInfo['response'][0]['id'])) {
                    $this->userInfo = $userInfo['response'][0];
                    $result = true;
                }
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
        return array(
            'auth_url'    => 'https://oauth.vk.com/authorize',
            'auth_params' => array(
                'client_id'     => $this->clientId,
                'scope'         => 'friends,photos,groups,email,notifications',
                'redirect_uri'  => $this->redirectUri,
                'response_type' => 'code'
            )
        );
    }
}