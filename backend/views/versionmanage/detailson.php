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
</style>
<div class="check-title">
    <p><?='未审核详情'?></p>
</div>
<div class="relate-contain">
    <ul class="user-item"><li class="user-item-detail">app标识：<?= $model->app_id ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">模块id：<?= $model->module_id ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">描述：<?= $model->discribtion ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">内部版本号：<?= $model->app_version_inner ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">更新链接：<?= $model->link ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">是否强制更新：<?= ($model->force_update==0?'否':'是') ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">是否审核：<?= ($model->status==1?'审核中':'已审核') ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">更新内容：<?= $model->update_content ?></li><li class="user-item-detail"></ul>
</div>
<?php
$this->registerJs($js,\yii\web\View::POS_END);
?>

