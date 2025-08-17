<?php
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Http\Client;
use Psr\Log\LogLevel;
use Google\Client as GoogleClient;

class ApiController extends AppController
{
    // LINE コールバック 受け口API
    public function authorize()
    {
        $code = $this->request->getQuery('code');
        $state = $this->request->getQuery('state');
        $sessionState = $this->request->getSession()->read('OAuthState');

        if ($state !== $sessionState) {
            $this->log('LINE: session is different: '. $state . " ? " . $sessionState, LogLevel::ERROR);
            return $this->redirect('/checkin?error=1');
        }

        // エラー判定
        $errorCode = $this->request->getQuery('error');
        if($errorCode) {
            return $this->redirect('/checkin?error='.$errorCode);
        }

        // トークン取得
        $http = new Client();
        $response = $http->post('https://api.line.me/oauth2/v2.1/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => 'https://'.env('MY_DOMAIN').'/authorize',
            'client_id' => env('LINE_CHANNEL_ID'),
            'client_secret' => env('LINE_CHANNEL_SECRET')
        ], [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]
        ]);

        if($response->getStatusCode() !== 200) {
          $this->log('LINE token response status is not 200: ' . $response->getStatusCode(), LogLevel::ERROR);
          $this->log('LINE token response error: ' . $response->getStringBody(), LogLevel::ERROR);
        }

        $data = $response->getJson();
        if ($data === null) {
            $this->log('LINE token response is not valid JSON: ' . $response->getStringBody(), LogLevel::ERROR);
        }
        $idToken = $data['id_token'] ?? null;

        if(!$idToken) {
          // キャンセルなどされたケース
          $this->log('idToken is null: '.$code, LogLevel::INFO);
          return $this->redirect('/checkin');
        }

        // IDトークンをセッションへ保存
        $this->getRequest()->getSession()->write('IdToken', $idToken);

        // ユーザー情報取得
        $jwt = explode(".", $idToken)[1];
        $payload = json_decode(base64_decode(strtr($jwt, '-_', '+/')), true);
        $userId = $payload['sub'];
        $userName = $payload['name'];

        // DBにユーザー登録
        $usersTable = $this->getTableLocator()->get('Users');
        if (!$usersTable->exists(['ext_id' => $userId])) {
            $user = $usersTable->newEntity([
                'ext_type' => "LINE",
                'ext_id' => $userId,
                'username' => $userName,
                'display_name' => $userName
            ]);
            $usersTable->save($user);
        }

        // セッション保存
        // user_typeをDBから取得してセッションに保存
        $userEntity = $usersTable->find()->where(['ext_id' => $userId])->first();
        $userType = $userEntity && !empty($userEntity->user_type) ? $userEntity->user_type : null;
        $this->getRequest()->getSession()->write('User', [
            'ext_id' => $userId,
            'username' => $userName,
            'user_type' => $userType,
        ]);

        return $this->redirect('/checkin');
    }

    // Googleコールバック
    public function googleAuthorize()
    {
      $code = $this->request->getQuery('code');
      $state = $this->request->getQuery('state');
      $sessionState = $this->request->getSession()->read('GoogleOAuthState');

      if ($state !== $sessionState) {
        $this->log('Google session is different: '. $state . " ? " . $sessionState, LogLevel::ERROR);
        return $this->redirect('/checkin?error=1');
      }
      $this->log('Google API accessed', LogLevel::INFO);

      // エラー判定
      $errorCode = $this->request->getQuery('error');
      if ($errorCode) {
        $this->log('Google returns error: '.$errorCode, LogLevel::ERROR);
        return $this->redirect(`/checkin?error={$errorCode}`);
      }
      // Google認証APIでトークン取得
      $client = new GoogleClient();
      $client->setAuthConfig(ROOT . '/config/client_secret.json');
      $client->setRedirectUri('https://' . env('MY_DOMAIN') . '/googleAuthorize');
      $client->addScope('openid profile');

      try {
        $token = $client->fetchAccessTokenWithAuthCode($code);
      } catch (\Exception $e) {
        $this->log('Google token fetch error: ' . $e->getMessage(), LogLevel::ERROR);
        return $this->redirect('/checkin?error=1');
      }

      if (isset($token['error'])) {
        $this->log('Google token response error: ' . json_encode($token), LogLevel::ERROR);
        return $this->redirect('/checkin?error=1');
      }

      $idToken = $token['id_token'] ?? null;
      if (!$idToken) {
        $this->log('Google idToken is null', LogLevel::INFO);
        return $this->redirect('/checkin');
      }

      // IDトークンをセッションへ保存
      $this->getRequest()->getSession()->write('IdToken', $idToken);

      // ユーザー情報取得
      $jwt = explode(".", $idToken)[1];
      $payload = json_decode(base64_decode(strtr($jwt, '-_', '+/')), true);
      $userId = $payload['sub'] ?? null;
      $userName = $payload['name'] ?? ($payload['email'] ?? '');

      if (!$userId) {
        $this->log('Google userId is null', LogLevel::ERROR);
        return $this->redirect('/checkin?error=1');
      }

      // DBにユーザー登録
      $usersTable = $this->getTableLocator()->get('Users');
      if (!$usersTable->exists(['ext_id' => $userId])) {
        $user = $usersTable->newEntity([
          'ext_type' => "GOOGLE",
          'ext_id' => $userId,
          'username' => $userName,
          'display_name' => $userName
        ]);
        $usersTable->save($user);
      }

      // セッション保存
      $userEntity = $usersTable->find()->where(['ext_id' => $userId])->first();
      $userType = $userEntity && !empty($userEntity->user_type) ? $userEntity->user_type : null;
      $this->getRequest()->getSession()->write('User', [
        'ext_id' => $userId,
        'username' => $userName,
        'user_type' => $userType,
      ]);

      return $this->redirect('/checkin');
    }

    

    public function saveCheckin()
    {
        $this->request->allowMethod(['post']);
        $idToken = $this->request->getData('id_token');

        if (!$idToken) {
            return $this->response->withType('application/json')->withStringBody(json_encode([
                'error' => 'トークンがありません'
            ]));
        }

        // ユーザー情報取得
        $jwt = explode(".", $idToken)[1];
        $payload = json_decode(base64_decode(strtr($jwt, '-_', '+/')), true);
        $userId = $payload['sub'];

        $usersTable = $this->getTableLocator()->get('Users');
        $user = $usersTable->find()->where(['ext_id' => $userId])->first();

        if (!$user) {
            return $this->response->withType('application/json')->withStringBody(json_encode([
                'error' => 'ユーザーが見つかりません'
            ]));
        }
        // 今日すでにチェックインしているか確認
        $today = date('Y-m-d');
        $existingCheckin = $this->getTableLocator()->get('Checkins')
            ->find()
            ->where([
          'user_id' => $user->id,
          'type' => 'in',
          'DATE(check_in_at) =' => $today
            ])
            ->first();

        // 今月のチェックイン数
        $month = date('Y-m');
        $monthlyCount = $this->getTableLocator()->get('Checkins')
          ->find()
          ->where([
            'user_id' => $user->id,
            'DATE_FORMAT(check_in_at, "%Y-%m") =' => $month
          ])
          ->count();

        if ($existingCheckin) {
            return $this->response->withType('application/json')->withStringBody(json_encode([
          'error' => $user->display_name . 'さんは、本日すでにログインしています',
          'monthlyCount' => $monthlyCount
            ]));
        }
        // チェックイン登録
        $checkinsTable = $this->getTableLocator()->get('Checkins');
        $checkin = $checkinsTable->newEntity([
            'user_id' => $user->id,
            'user_ext_id' => $user->ext_id,
            'user_name' => $user->display_name,
            'type' => 'in',
            'check_in_at' => date('Y-m-d H:i:s')
        ]);

        if (!$checkinsTable->save($checkin)) {
            $errors = $checkin->getErrors();
            $this->log('Checkin save errors: ' . print_r($errors, true), LogLevel::ERROR);
            return $this->response->withType('application/json')->withStringBody(json_encode([
                'error' => 'チェックインの保存に失敗しました',
                'details' => $errors
            ]));
        }

        return $this->response->withType('application/json')->withStringBody(json_encode([
            'userName' => $user->display_name,
            'monthlyCount' => $monthlyCount
        ]));
    }

    public function checkout()
    {
      $this->request->allowMethod(['post']);
      // staff.phpのfetchはCSRFトークンのみ、id_token等は送らない
      $session = $this->getRequest()->getSession();
      $user = $session->read('User');
      if (!$user || empty($user['ext_id'])) {
        return $this->response->withType('application/json')->withStringBody(json_encode([
          'error' => '認証情報がありません'
        ]));
      }

      $usersTable = $this->getTableLocator()->get('Users');
      $userEntity = $usersTable->find()->where(['ext_id' => $user['ext_id']])->first();
      if (!$userEntity) {
        return $this->response->withType('application/json')->withStringBody(json_encode([
          'error' => 'ユーザーが見つかりません'
        ]));
      }

      // 今日すでに退勤済みか確認
      $today = date('Y-m-d');
      $existingCheckout = $this->getTableLocator()->get('Checkins')
        ->find()
        ->where([
          'user_id' => $userEntity->id,
          'DATE(check_in_at) =' => $today,
          'type' => 'out',
        ])
        ->first();
      if ($existingCheckout) {
        return $this->response->withType('application/json')->withStringBody(json_encode([
          'error' => '本日すでに退勤済みです'
        ]));
      }

      // 退勤登録
      $checkinsTable = $this->getTableLocator()->get('Checkins');
      $checkin = $checkinsTable->newEntity([
        'user_id' => $userEntity->id,
        'user_ext_id' => $userEntity->ext_id,
        'user_name' => $userEntity->display_name,
        'type' => 'out',
        'check_in_at' => date('Y-m-d H:i:s')
      ]);
      if (!$checkinsTable->save($checkin)) {
        $errors = $checkin->getErrors();
        return $this->response->withType('application/json')->withStringBody(json_encode([
          'error' => '退勤の保存に失敗しました',
          'details' => $errors
        ]));
      }

      return $this->response->withType('application/json')->withStringBody(json_encode([
        'success' => true
      ]));
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        if ($this->components()->has('FormProtection')) {
            $this->FormProtection->setConfig('unlockedActions', ['saveCheckin']);
        }
    }
}
