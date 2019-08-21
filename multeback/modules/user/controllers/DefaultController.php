<?php

namespace multeback\modules\user\controllers;

use multebox\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
