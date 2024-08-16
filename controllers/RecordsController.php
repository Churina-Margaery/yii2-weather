<?php

namespace app\controllers;
//namespace app\modules\weather\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Records;

class RecordsController extends Controller
{
    public function actionIndex()
    {
        $data = Records::getAndUpdateWeatherData();
        return $this->render("index", compact("data"));
    }
}
