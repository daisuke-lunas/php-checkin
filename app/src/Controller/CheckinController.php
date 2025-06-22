<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Client;

class CheckinController extends AppController
{
  public function index()
  {
      $error = $this->getRequest()->getQuery('error');
      $session = $this->getRequest()->getSession();
      $user = $session->read('User');
      if ($error) {
        $this->set('message', "ログイン中にエラーが発生し中断しました。お手数ですが、もう一度ログインをお願いします。");
        $session->destroy();
        $user = null;
      }

      if ($user) {
          // 認証済 → saveCheckin へ送信
          $id_token = $session->read('IdToken');
          $http = new Client();
          $response = $http->post('https://'.env('MY_DOMAIN').'/saveCheckin', [
              'id_token' => $id_token
          ]);
          $body = $response->getJson();
          if ($response->isOk() && isset($body['userName'])) {
              $this->set('message', "{$body['userName']}さん、いらっしゃいませ");
              $this->set('showLogout', true);
          } else {
              $errorMsg = "";
              $statusCode = $response->getStatusCode();
              if ($statusCode !== 200) {
                $errorMsg .= "ログインエラー: " . $statusCode;
              }
              if (!empty($body['error'])) {
                  $errorMsg .= ' - ' . $body['error'];
              }
              if (!empty($body['details'])) {
                  $errorMsg .= ' [' . json_encode($body['details'], JSON_UNESCAPED_UNICODE) . ']';
              }
              $this->set('message', $errorMsg);
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

  public function logout()
  {
      $session = $this->getRequest()->getSession();
      $session->destroy();
      return $this->redirect('/checkin');
  }
}