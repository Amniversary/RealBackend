<?php
use yii\bootstrap\Html;
?>
    <style>
        .check-title
        {
            font-family: "微软雅黑", arial, sans-serif;
            font-size: 24pt;
            height: 80px;
            line-height:80px;
            text-align: center;
        }
        .check-button-list
        {
            text-align: left;
            margin: 0px 30px;
        }
        p{
            margin: 0px;
        }
        .user-info
        {
            font-family: "微软雅黑", arial, sans-serif;
            font-size: 22px;
            text-align: left;
            height: 50px;
            padding-left: 10px;
            line-height: 50px;
            background-color: #acacac;
            margin: 10px 0px;
        }
        .user-contain
        {
            margin: 0px;
        }
        .user-item
        {
            list-style:none;
            margin: 0px;
            padding: 0px;
            text-align: left;
        }
        .user-item-detail
        {
            display: inline-block;
            font-size: 12pt;
            margin: 5px 10px;
            width: 50%;
        }
        .check-refuse-contain
        {
            margin: 0px;
        }
        .relate-contain
        {
            margin: 0px;
            text-align: left;
        }
        .refused-reason
        {
            display: block;
            width: 30%;
            height: 68px;
            padding: 6px 12px;
            font-size: 14px;
            color: #555;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            margin: 0px 20px;
        }
        .textarea
        {
            width: 94%;
        }
        .check_id_card_img
        {
            width: 400px;height: 300px;
        }
    </style>
    <div class="check-title">
        <p><?='已审核详情'?></p>
    </div>

    <div class="user-info">
        <p><?='拒绝理由'?></p>
    </div>
    <div class="relate-contain">
        <input type="hidden" id="has_submit" value="0">
        <p><?= $model['refused_reason'] ?></p>
    </div>
    <div class="user-info">
        <p>相关信息</p>
    </div>
    <div class="relate-contain">
        <input type="hidden" name="check_id" id="check_id" value="<?= $model['approve_id'] ?>">
        <ul class="user-item"><li class="user-item-detail">用户昵称：<?= $model['create_user_name'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">真实姓名：<?= $model['actual_name'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">手机号：<?= $model['phone_num'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">身份证号：<?= $model['id_card'] ?></li><li class="user-item-detail"></ul>
    </div>
<?php
$this->registerJs($js,\yii\web\View::POS_END);
?>