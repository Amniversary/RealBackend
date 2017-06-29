<?php
namespace backend\controllers;

use backend\business\UserMenuUtil;
use common\components\WeiXinUtil;
use frontend\business\ApiLogUtil;
use frontend\business\ShareUtil;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\LoginForm;
use yii\filters\VerbFilter;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public $enableCsrfValidation = false;
    public $menu=null;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error','testok1','noprivilige','statisticapilog'],
                        'allow' => true
                    ],
                    [
                        'actions' => ['logout', 'index'],
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
        ];
    }

    public function actionTestok1()
    {
        $innerMenu = [];
        $menu = UserMenuUtil::GetUserMenu(1,0,$innerMenu);
        var_dump($menu);
        var_dump($innerMenu);
        exit;
        $this->layout = 'main_empty_adminlte';
        $url = \Yii::$app->request->getAbsoluteUrl();
        $sign = WeiXinUtil::GetShareSign($url);
        $shareInfo = null;
        if(!ShareUtil::GetShareInfoForWish('3',$shareInfo,$error))
        {
            var_dump($error);
            exit;
        }
        $shareInfo['content'] =str_replace("\r\n",'',$shareInfo['content']);
        $shareInfo['content'] =str_replace("\n",'',$shareInfo['content']);
        //\Yii::$app->getView()->registerJsFile('http://res.wx.qq.com/open/js/jweixin-1.0.0.js');
        return $this->render('testok1',[
            'sign'=>$sign,
            'share'=>$shareInfo
        ]);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        /*if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }*/
        /*if(!DeviceUtil::IsGoogleBrowse())
        {
            header("Location:http://www.cnbeta.com/articles/476185.htm");
            exit;
        }*/
        /*print_r(strpos($_SERVER['HTTP_USER_AGENT'],'Chrome'));
        echo "<br />";
        print_r($_SERVER['HTTP_USER_AGENT']);*/
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('index');//$this->goBack('index');//
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionNoprivilige()
    {
        return $this->render('no_privilige');
    }
}
