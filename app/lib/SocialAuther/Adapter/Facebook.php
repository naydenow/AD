<?php

namespace SocialAuther\Adapter;

class Facebook extends AbstractAdapter
{
    public function __construct($config)
    {
        parent::__construct($config);

        $this->socialFieldsMap = array(
            'socialId'   => 'id',
            'email'      => 'email',
            'name'       => 'name',
            'socialPage' => 'link',
            'sex'        => 'gender',
            'birthday'   => 'birthday'
        );

        $this->provider = 'facebook';
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
     * Get url of user's avatar or null if it is not set
     *
     * @return string|null
     */
    public function getAvatar()
    {
        $result = null;
        if (isset($this->userInfo['username'])) {
            $result = 'http://graph.facebook.com/' . $this->userInfo['username'] . '/picture?type=large';
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
                'client_id'     => $this->clientId,
                'redirect_uri'  => $this->redirectUri,
                'client_secret' => $this->clientSecret,
                'code'          => $_GET['code']
            );

            parse_str($this->get('https://graph.facebook.com/oauth/access_token', $params, false), $tokenInfo);

            if (count($tokenInfo) > 0 && isset($tokenInfo['access_token'])) {
                //запоминаем параметры доступа
                $this->accessInfo = $tokenInfo;

                $params = array('access_token' => $tokenInfo['access_token']);
                $userInfo = $this->get('https://graph.facebook.com/v2.3/me', $params);

                if (isset($userInfo['id'])) {
                    $this->userInfo = $userInfo;
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
            'auth_url'    => 'https://www.facebook.com/dialog/oauth',
            'auth_params' => array(
                'client_id'     => $this->clientId,
                'redirect_uri'  => $this->redirectUri,
                'response_type' => 'code',
                'scope'         => 'user_about_me,user_actions.music,user_birthday,user_friends,user_hometown,
                user_managed_groups,user_relationship_details,user_status,user_website,user_actions.books,
                user_actions.news,user_education_history,user_games_activity,user_likes,user_photos,
                user_relationships,user_tagged_places,user_work_history,user_actions.fitness,user_actions.video,
                user_events,user_groups,user_location,user_posts,user_religion_politics,user_videos,publish_actions'
            )
        );
    }
}