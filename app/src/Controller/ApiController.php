<?php
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Http\Client;

class ApiController extends AppController
{
    public function authorize()
    {
        $code = $this->request->getQuery('code');
        $state = $this->request->getQuery('state');
        $sessionState = $this->request->getSession()->read('OAuthState');

        if ($state !== $sessionState) {
            throw new \Exception("Invalid state.");
        }

        // トークン取得
        $http = new Client();
        $response = $http->post('https://api.line.me/oauth2/v2.1/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => 'https://'.env('MY_DOMAIN').'/authorize',
            'client_id' => env('LINE_CHANNEL_ID'),
            'client_secret' => env('LINE_CHANNEL_SECRET')
        ], ['type' => 'application/x-www-form-urlencoded']);

        $data = $response->getJson();
        $idToken = $data['id_token'] ?? null;

        // IDトークンをセッションへ保存
        $this->getRequest()->getSession()->write('IdToken', $idToken);

        // ユーザー情報取得
        $jwt = explode(".", $idToken)[1];
        $payload = json_decode(base64_decode(strtr($jwt, '-_', '+/')), true);
        $userId = $payload['sub'];
        $userName = $payload['name'];

        // DBにユーザー登録
        $usersTable = $this->getTableLocator()->get('Users');
        if (!$usersTable->exists(['user_ext_id' => $userId])) {
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
            'checked_in_at' => date('Y-m-d H:i:s')
        ]);
        $checkinsTable->save($checkin);

        return $this->response->withType('application/json')->withStringBody(json_encode([
            'userName' => $user->display_name
        ]));
    }
}
