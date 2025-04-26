<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Text; // UUID生成のため
use SebastianBergmann\Environment\Console;

class CheckinController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function index()
    {
        $this->set('message', 'ログインしてください');
    }

    public function saveCheckin()
    {
        $this->request->allowMethod(['post']);
        $data = $this->request->getData();

        $username = $data['username'];
        $this->log($username, 'debug');
        $password = $data['password'];
        $this->log($password, 'debug');

        // ユーザー認証ロジック
        $userTable = $this->fetchTable('Users');
        $query = $userTable->find()
            ->where(['username' => $username]);
        // debug((string)$query->sql());
        // debug($query->getValueBinder()->bindings());
        $user = $query->first();

        if ($user) { //&& password_verify($password, $user->password)
            $checkin = $this->Checkin->newEmptyEntity();
            $checkin->id = Text::uuid();
            $checkin->customer_id = $user->id;
            $checkin->customer_name = $user->display_name;
            $checkin->type = 'in'; // 仮のタイプ。状況に応じて変更
            $checkin->check_in_at = date('Y-m-d H:i:s');

            if ($this->Checkin->save($checkin)) {
                $response = [
                    'auth' => 'OK',
                    'display_name' => $user->display_name
                ];
            } else {
                $response = ['auth' => 'NG'];
            }
        } else {
            $response = ['auth' => 'NG'];
        }

        $this->set(compact('response'));
        $this->viewBuilder()->setOption('serialize', 'response');
        $this->viewBuilder()->setClassName('Json');
    }
}