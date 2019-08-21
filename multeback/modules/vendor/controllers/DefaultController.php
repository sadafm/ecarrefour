<?php

namespace multeback\modules\vendor\controllers;

use multebox\Controller;

/**
 * Default controller for the `vendor` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
