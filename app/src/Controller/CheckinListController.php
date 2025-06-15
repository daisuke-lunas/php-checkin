<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\FrozenDate;

class CheckinListController extends AppController
{
    public function index()
    {
        $today = date('Y-m-d');
        $checkinsTable = $this->getTableLocator()->get('Checkins');
        $query = $checkinsTable->find()
            ->where([
                'DATE(check_in_at) =' => $today
            ])
            ->order(['check_in_at' => 'DESC']);
        $checkins = $query->all();
        $this->set(compact('checkins', 'today'));
    }
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // 認証チェック
        if ($this->request->getParam('action') === 'index') {
            $session = $this->getRequest()->getSession();
            if (!$session->read('CheckinListAuth')) {
                if ($this->request->is('post')) {
                    $inputUser = $this->request->getData('username');
                    $inputPass = $this->request->getData('password');
                    $envUser = env('CHECKINLIST_USER');
                    $envPass = env('CHECKINLIST_PASS');
                    if ($inputUser === $envUser && $inputPass === $envPass) {
                        $session->write('CheckinListAuth', true);
                        return $this->redirect(['action' => 'index']);
                    } else {
                        $this->set('loginError', 'ユーザー名またはパスワードが違います');
                    }
                }
                $this->render('login');
                return false;
            }
        }
    }
}
