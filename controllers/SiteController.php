<?php

namespace app\controllers;

use app\helpers\NurCommentParser;
use app\helpers\NurNewsParser;
use app\helpers\TengriParser;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $allComments = [];
        $newsAr = null;
        if(Yii::$app->request->get()) {
            $newsAr = NurNewsParser::getNewsUrls(Yii::$app->request->get('news'));
        }
        else {
            $newsAr = NurNewsParser::getNewsUrls(1);
        }


        foreach ($newsAr as $news){
            $nurCommentParser = new NurCommentParser($news);
            $allComments = array_merge($allComments,$nurCommentParser->getComments());
        }

        $comments = new ArrayDataProvider([
            'allModels' => $allComments,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['author','body','created_at','negative_rate', 'positive_rate','news_title'],
            ],
        ]);

        return $this->render('about',['comments' => $comments, 'allComments' => $allComments]);
    }

}
