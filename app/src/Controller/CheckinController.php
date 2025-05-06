<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Client;

class CheckinController extends AppController
{
  public function index()
  {
      $session = $this->getRequest()->getSession();
      $user = $session->read('User');

      if ($user) {
          // 認証済 → saveCheckin へ送信
          $http = new Client();
          $response = $http->post('https://'.env('MY_DOMAIN').'/saveCheckin', [
              'id_token' => $session->read('IdToken')
          ]);
          $body = $response->getJson();

          if ($response->isOk() && isset($body['userName'])) {
              $this->set('message', "{$body['userName']}さん、いらっしゃいませ");
          } else {
              $this->set('message', "ログインエラー: " . $body['error']);
          }
      } else {
          // LINEログインボタン表示
          $lineClientId = env('LINE_CHANNEL_ID');
          $redirectUri = urlencode('https://'.env('MY_DOMAIN').'/authorize');
          $state = bin2hex(random_bytes(16));
          $session->write('OAuthState', $state);
          $lineLoginUrl = "https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id={$lineClientId}&redirect_uri={$redirectUri}&state={$state}&scope=openid%20profile";
          $this->set('loginUrl', $lineLoginUrl);
      }
  }
}