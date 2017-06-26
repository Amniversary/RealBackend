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


    /**
     * 接收测试服务器统计的日活/月活数据
     */
    public function actionStatisticapilog()
    {
        @ini_set('memory_limit', '2048M');
        $data = \Yii::$app->request->post();
//        $token_str = \Yii::$app->cache->get($data['rand_str']);
//        if(empty($token_str) || ($token_str !== 1))
//        {
//            \Yii::getLogger()->log('actionStatisticapilog签名 cache 错误'.var_export($data,true),\yii\log\Logger::LEVEL_ERROR);
//            \Yii::getLogger()->log('actionStatisticapilog签名 cache 错误 $token_str'.$token_str,\yii\log\Logger::LEVEL_ERROR);
//            $error_msg = '系统错误';
//            print($error_msg);
//            exit;
//        }
//        $del_token_str = \Yii::$app->cache->delete($data['rand_str']);   //删除缓存
        foreach($data as $key=>$val)
        {
            if(empty($val))
            {
                \Yii::getLogger()->log('actionStatisticapilog签名参数为空'.var_export($data,true),\yii\log\Logger::LEVEL_ERROR);
                $error_msg = '参数错误';
                print($error_msg);
                exit;
            }
        }
        $now_date = date('Y-m-d',strtotime('-1 days'));
        $before_date = date('Y-m-d',strtotime('-2 days'));  //跟踪发现统计时间有差2天
        if(($data['statistic_time_1'] != $now_date) && ($data['statistic_time_2'] != $now_date))
        {
            if(($data['statistic_time_1'] != $before_date) && ($data['statistic_time_2'] != $before_date))
            {
                \Yii::getLogger()->log('actionStatisticapilog签名参数日期不对应'.var_export($data,true),\yii\log\Logger::LEVEL_ERROR);
                $error_msg = '参数错误';
                print($error_msg);
                exit;
            }
        }

        $p_sign = $data['p_sign'];
        unset($data['p_sign']);
        $sign = ApiLogUtil::GetApiLogSign($data);
        $error_msg = 'ok';
        if($p_sign !== $sign)
        {
            \Yii::getLogger()->log('actionStatisticapilog签名错误'.var_export($data,true),\yii\log\Logger::LEVEL_ERROR);
            $error_msg = '签名错误';
            print($error_msg);
            exit;
        }

        $day_params = [
            'statistic_time' => $data['statistic_time_1'],
            'statistic_type' => $data['statistic_type_1'],
            'user_num' => $data['user_num_1']
        ];
        if(!ApiLogUtil::InsertStatisticApiLogActive($day_params))
        {
            $error_msg = '日活写入失败';
            \Yii::getLogger()->log('actionStatisticapilog日活写入失败'.var_export($data,true),\yii\log\Logger::LEVEL_ERROR);
            print($error_msg);
            exit;
        }


        $month_params = [
            'statistic_time' => date('Y-m',strtotime($data['statistic_time_2'])),
            'statistic_type' => $data['statistic_type_2'],
            'user_num' => $data['user_num_2']
        ];
        if(!ApiLogUtil::InsertStatisticApiLogDayActive($month_params))
        {
            $error_msg = '月活写入失败';
            \Yii::getLogger()->log('actionStatisticapilog月活写入失败'.var_export($data,true),\yii\log\Logger::LEVEL_ERROR);
            print($error_msg);
            exit;
        }

//        $day = date('d',strtotime($data['statistic_time_2']));
//        if($day == 1)
//        {
//            if(!ApiLogUtil::TruncateApiLogTable())
//            {
//                \Yii::getLogger()->log($data['statistic_time_2'].'  mb_api_log表数据清空失败',\yii\log\Logger::LEVEL_ERROR);
//            }
//        }

        print($error_msg);
        exit;
    }
}
