<?php

namespace App\Controller;

use Cake\Controller\Controller;


class NotFoundController extends Controller
{
  public function notFound()
  {
    $this->set('message', '404 Not Found');
  }
}