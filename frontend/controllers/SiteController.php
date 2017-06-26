<?php
namespace frontend\controllers;

use backend\components\ExitUtil;
use common\components\QrCodeUtil;
use dosamigos\qrcode\QrCode;
use frontend\business\AddressUtil;
use frontend\business\ClientInfoUtil;
use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\db\Query;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
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
            'Kupload' => [
                'class' => 'pjkui\kindeditor\KindEditorAction',
            ]
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        echo '<h1>OK is yii</h1>';
        exit;
        //http://demo.xuexinbao.cn/site/index
        $outFile ='';
        $imagefile = 'http://demo.xuexinbao.cn/mywish/showwishview?wish_id=346229&rand_num=346073&time=1456364698&sign=00665000e646648567746908e0a97cd0c50dc261&width=1920';
        $url = QrCodeUtil::GetQrCodeUrl($imagefile,$error,3);
        if($url === false)
        {
            ExitUtil::ExitWithMessage($error);
        }
       return $this->render('index',
           [
               'img_url'=>$url,
               'addresslist'=>$addressList,
               'str'=>$str
           ]
           );
    }

    public function actionTest()
    {
        exit('test');
        $html = $_POST['w0'];
        return $this->render('test',['cnt'=>$html]);
    }

    public function actionQrcodeimg($url,$time,$rand_str,$sign)
    {
        return QrCode::png($url);
    }

    public function actionSb()
    {
        $query = (new Query())
            ->select(['bl.living_master_id as user_id','nick_name','bc.pic','bl.status'])
            ->from('mb_activity_people ap')
            ->innerJoin('mb_activity_info ai','ap.activity_id = ai.activity_id')
            ->innerJoin('mb_client bc','bc.client_id = ap.living_master_id')
            ->innerJoin('mb_living bl','ap.living_master_id = bl.living_master_id')
            ->where(['ai.activity_id'=>1])
            ->all();
        $user_id = 3;
        foreach($query as $test)
        {
            $s = array_search($test,$query);
            $test['is_attention'] = ClientInfoUtil::IsAttention($user_id,$test['user_id']);
            $query[$s] = $test;
        }

        print_r("<pre>");
        print_r($query);


    }


    public function actionAa(){
        $query = (new Query())
            ->select(['bl.living_master_d as anchor_id','bc.nick_name','bc.pic','bl.status','ma.user_id'])
            ->from('mb_activity_people ap')
            ->innerJoin('mb_activity_info ai','ap.activity_id = ai.activity_id')
            ->innerJoin('mb_client bc','bc.client_id = ap.living_master_id')
            ->innerJoin('mb_living bl','ap.living_master_id = bl.living_master_id')
            ->leftJoin('mb_attention ma','ma.user_id = ap.living_master_id and ma.friend_user_id =:user_id',[':user_id' => 2])
            ->where(['ai.activity_id'=>1])
            ->all();
        print_r("<pre>");
        print_r($query);
    }

}
