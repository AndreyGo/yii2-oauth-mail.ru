<?php

namespace app\models;

use yii\authclient\OAuth2;

/**
 * Class OAuthMailRu
 * @package app\models
 */
class OAuthMailRu extends OAuth2
{
    /**
     * @var string
     */
    public $authUrl = 'https://connect.mail.ru/oauth/authorize';

    /**
     * @var string
     */
    public $tokenUrl = 'https://connect.mail.ru/oauth/token';

    /**
     * @var string
     */
    public $apiBaseUrl = 'http://www.appsmail.ru/platform/api';

    /**
     * @var string
     */
    public $applicationKey = '';

    /**
     * @return array
     * @throws \yii\base\Exception
     */
    protected function initUserAttributes()
    {
        $result = $this->api('users.getInfo', 'GET');
        return end($result);
    }

    /**
     * @param string $apiSubUrl
     * @param string $method
     * @param array $params
     * @param array $headers
     * @return array
     * @throws \yii\base\Exception
     */
    public function api($apiSubUrl, $method = 'GET', array $params = [], array $headers = [])
    {
        $params['method'] = $apiSubUrl;
        $apiSubUrl = '';
        return parent::api($apiSubUrl, $method, $params, $headers);
    }

    /**
     * @param array $params
     * @return string
     */
    public function buildAuthUrl(array $params = [])
    {
        $defaultParams = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->getReturnUrl(),
            'xoauth_displayname' => \Yii::$app->name,
        ];

        if (!empty($this->scope)) {
            $defaultParams['scope'] = $this->scope;
        }

        return $this->composeUrl($this->authUrl, array_merge($defaultParams, $params));
    }


    /**
     * @param \yii\authclient\OAuthToken $accessToken
     * @param string $url
     * @param string $method
     * @param array $params
     * @param array $headers
     * @return array
     * @throws \yii\authclient\InvalidResponseException
     * @throws \yii\base\Exception
     */
    protected function apiInternal($accessToken, $url, $method, array $params, array $headers)
    {
        $url = $this->apiBaseUrl;

        $params['app_id'] = $this->clientId;
        $params['session_key'] = $accessToken->token;
        $params['format'] = 'json';
        $params['secure'] = 1;

        ksort($params);
        $sign = '';

        foreach($params as $name => $value){
            $sign .= "$name=$value";
        }

        $params['sig'] = md5($sign . $this->clientSecret);

        return $this->sendRequest($method, $url, $params, $headers);
    }

    /**
     * @param string $rawResponse
     * @param string $contentType
     * @return array
     * @throws \yii\base\Exception
     */
    protected function processResponse($rawResponse, $contentType = self::CONTENT_TYPE_AUTO)
    {
        $contentType = self::CONTENT_TYPE_JSON;
        return parent::processResponse($rawResponse, $contentType);
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'mail.ru';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'Mail.ru';
    }
}
