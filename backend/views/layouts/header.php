<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
?>
<style>
    .auth-image{
        float: left;
    }
    .user-header>p {
        font-size: 14px !important;
        font-weight: bold !important;
    }
    #head-ul{
        color: #FFF;
        font-weight: bold;
        font-size: 14px;
        list-style: none;
        text-align: initial;
    }
    #head-ul > li {
        display: list-item;
        padding-left: 32%;
    }
</style>
<header class="main-header">

    <?= Html::a('<span class="logo-mini">APP</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <?php if (!empty($authInfo)) { ?>
                <!-- 公众号头部 -->
                <?php $authData = \backend\business\DailyStatisticUsersUtil::GetDailyFansNum($authInfo['record_id']);  ?>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= empty($authInfo['head_img']) ? 'http://oss.aliyuncs.com/meiyuan/wish_type/default.png':$authInfo['head_img']; ?>" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?= $authInfo['nick_name']; ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header" style="height: auto">
                            <img src="<?= empty($authInfo['head_img']) ? 'http://oss.aliyuncs.com/meiyuan/wish_type/default.png': $authInfo['head_img']; ?>" class="auth-image" alt="User Image"/>
                            <h4 style="color: #FFF"><?= $authInfo['nick_name'] ?></h4>
                            <ul id="head-ul">
                                <li>公众号类型 : <?= \common\models\AuthorizationList::getServiceTypeInfo($authInfo['service_type_info']) ?></li>
                                <li>认证类型 : <?= \common\models\AuthorizationList::getVerifyTypeInfo($authInfo['verify_type_info']) ?></li>
                                <li>新增人数 : <?= $authData['new_user'] ?></li>
                                <li>净增人数 : <?= $authData['net_user'] ?></li>
                                <li>总粉丝数 : <?= $authData['count_user'] ?></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <?php } ?>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= $pic ?>" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?= \Yii::$app->user->identity->username; ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= $pic ?>" class="img-circle" alt="User Image"/>
                            <p>
                                <?= date('Y-m-d') ?>
                                <h4 style="color: #FFF">Real数据平台</h4>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <!--<li class="user-body">
                            <div class="col-xs-4 text-center">
                                <a href="#">Followers</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="#">Sales</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="#">Friends</a>
                            </div>
                        </li>-->
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="#" class="btn btn-default btn-flat">关闭</a>
                            </div>
                            <div class="pull-right">
                                <?= Html::a('退出', ['/site/logout'], ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']) ?>
                            </div>
                        </li>
                    </ul>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                <!--<li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>-->
            </ul>
        </div>
    </nav>
</header>
