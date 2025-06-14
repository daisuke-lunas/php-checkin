<?php
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Http\Client;
use Psr\Log\LogLevel;

class ApiController extends AppController
{
    // LINE コールバック 受け口API
    public function authorize()
    {
        $code = $this->request->getQuery('code');
        $state = $this->request->getQuery('state');
        $sessionState = $this->request->getSession()->read('OAuthState');

        if ($state !== $sessionState) {
            throw new \Exception("Invalid session state.");
        }

        // エラー判定
        $errorCode = $this->request->getQuery('error');
        if($errorCode) {
            return $this->redirect('/checkin?error=1');
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
        $this->getRequest()->getSession()->write('User', [
            'ext_id' => $userId,
            'username' => $userName
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
            'userName' => $user->display_name
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
