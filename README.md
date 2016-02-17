# yii2-oauth-mail.ru

OAuth 2.0 client for Mail.ru
http://api.mail.ru/docs/

Add to your config in component section

```php
'authClientCollection' => [
      'class' => 'yii\authclient\Collection',
      'clients' => [
          'mailru' => [
              'class' => 'app\models\OAuthMailRu',
              'applicationKey' => 'app key',
              'clientId' => 'app id',
              'clientSecret' => 'app secret',
          ]
      ],
  ],
```

and use (in callback redirect page)

```php
$collection = \Yii::$app->authClientCollection;
$code = \Yii::$app->request->get('code',null);
$mailru = $collection->clients['mailru'];

if($code !== null){
  // Fetch access token
  $mailru->fetchAccessToken($code);
  
  // Get current user fields
  $fields = $social->getUserAttributes();
  
  // Use API (Get friends list for example)
  // $mailru->api('friends.get','GET')
  // More details on http://api.mail.ru/docs/
  
  \Yii::$app->controller->redirect([\Yii::$app->defaultRoute]);
  return ;
}

$url = $mailru->buildAuthUrl();
\Yii::$app->controller->redirect($url);
```
