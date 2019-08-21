<?php

namespace multeback\modules\support\controllers;

use multebox\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
