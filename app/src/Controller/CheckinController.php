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
        $this->set('showLogout', true);
        $session->destroy();
        $user = null;
      }

      if ($user) {
          $id_token = $session->read('IdToken');
          $http = new Client();
          $this->set('showLogout', true);

          $response = $http->post('https://'.env('MY_DOMAIN').'/saveCheckin', [
              'id_token' => $id_token
          ]);
          // user_type=="staff"ならstaff専用テンプレートを表示
          if (isset($user['user_type']) && $user['user_type'] === 'staff') {
              // 今月のin/out一覧を取得
              $checkinsTable = $this->getTableLocator()->get('Checkins');
              $userId = $user['ext_id'] ?? null;
              $month = date('Y-m');
              $records = [];
              if ($userId) {
                  $usersTable = $this->getTableLocator()->get('Users');
                  $userEntity = $usersTable->find()->where(['ext_id' => $userId])->first();
                  if ($userEntity) {
                      $records = $checkinsTable->find()
                          ->where([
                              'user_id' => $userEntity->id,
                              'DATE_FORMAT(check_in_at, "%Y-%m") =' => $month
                          ])
                          ->order(['check_in_at' => 'ASC'])
                          ->all();
                  }
              }
              $this->set('checkinRecords', $records);
              $this->render('staff');
              return;
          }
          // ...既存の一般ユーザー処理...
          $body = $response->getJson();
          if ($response->isOk() && isset($body['userName'])) {
              $this->set('message', "{$body['userName']}さん、いらっしゃいませ");
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
          if (isset($body['monthlyCount']) && (int)$body['monthlyCount'] >= 0) {
              $this->set('monthlyCountMessage', "今月のご来店回数：" . (int)$body['monthlyCount'] . "回");
          }

          // 先月の来店回数・割引表示
          $userId = $user['ext_id'] ?? null;
          if ($userId) {
              $summaryTable = $this->getTableLocator()->get('CheckinUserMonthlySummary');
              $lastMonth = date('Ym', strtotime('-1 month'));
              $summary = $summaryTable->find()
                  ->where([
                      'yyyymm' => $lastMonth,
                      'user_ext_id' => $userId,
                      'type' => 'in',
                  ])->first();
              if ($summary) {
                  $lastCount = (int)$summary->total_count;
                  $msg = "先月のご来店回数：{$lastCount}回";
                  if ($lastCount >= 5) {
                      $msg .= "<br>今月は200円引きです";
                  } elseif ($lastCount >= 2) {
                      $msg .= "<br>今月は100円引きです";
                  } 
                  $this->set('lastMonthMessage', $msg);
              }
          }
      } else {
          // LINEログインボタン表示
          $lineClientId = env('LINE_CHANNEL_ID');
          $redirectUri = urlencode('https://'.env('MY_DOMAIN').'/authorize');
          $state = bin2hex(random_bytes(16));
          $session->write('OAuthState', $state);
          $lineLoginUrl = "https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id={$lineClientId}&redirect_uri={$redirectUri}&state={$state}&scope=openid%20profile";
          $this->set('loginUrl', $lineLoginUrl);

          // Googleログインボタン表示
          $googleClientId = env('GOOGLE_CLIENT_ID');
          $googleRedirectUri = urlencode('https://' . env('MY_DOMAIN') . '/googleAuthorize');
          $googleState = bin2hex(random_bytes(16));
          $session->write('GoogleOAuthState', $googleState);
          $googleLoginUrl = "https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id={$googleClientId}&redirect_uri={$googleRedirectUri}&state={$googleState}&scope=openid%20profile";
          $this->set('googleLoginUrl', $googleLoginUrl);
      }
  }

  public function logout()
  {
      $session = $this->getRequest()->getSession();
      $session->destroy();
      return $this->redirect('/checkin');
  }
}