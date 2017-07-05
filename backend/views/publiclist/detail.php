<?php
use yii\bootstrap\Html;

/**
 *  @var $model common\models\AttentionEvent
 */
?>

<style>
    .modal-content{
        border-radius: 5px;
    }
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
        font-size: 20px;
        text-align: left;
        height: 40px;
        padding-left: 10px;
        line-height: 40px;
        background-color: #acacac;
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
        margin: 10px 0px;
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
    <p>消息详情</p>
</div>
<div class="user-info">
    <p>相关信息</p>
</div>
<div class="relate-contain">
    <input type="hidden" name="check_id" id="check_id" value="<?= $model->record_id ?>">
    <ul class="user-item"><li class="user-item-detail">公众号：<?= $model::getKeyAppId($model->app_id) ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">标题内容：<?= $model->title ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">内容描述：<?= $model->description ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">外链Url：<?= $model->url ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">图片Url：<?= $model->picurl ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">消息内容：<?= $model->content ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">消息类型：<?= $model->getMsgType($model->msg_type) ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">创建时间：<?= $model->create_time ?></li><li class="user-item-detail"></ul>
</div>
<?php
$js = '';
$this->registerJs($js,\yii\web\View::POS_END);
?>