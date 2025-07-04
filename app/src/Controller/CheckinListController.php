<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\FrozenDate;

class CheckinListController extends AppController
{
    public function index()
    {
        $today = date('Y-m-d');
        $lastMonth = date('Ym', strtotime('-1 month'));
        $checkinsTable = $this->getTableLocator()->get('Checkins');
        $query = $checkinsTable->find()
            ->contain([
                'CheckinUserMonthlySummary' => function($q) use ($lastMonth) {
                    return $q->where([
                        'CheckinUserMonthlySummary.yyyymm' => $lastMonth,
                        'CheckinUserMonthlySummary.type' => 'in',
                    ]);
                }
            ])
            ->where([
                'DATE(Checkins.check_in_at) =' => $today
            ])
            ->order(['Checkins.check_in_at' => 'DESC']);
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
