<?php
\common\assets\ArtDialogAsset::register($this);
use \yii\helpers\Html;
?>
<style>
    body
    {
        background-color: #f4f4f4;
        font-family: "微软雅黑", arial, sans-serif;
    }
    a
    {
        text-decoration: none;
    }
    .header
    {
        height: 60px;
        background-color: #ff5757;
        color: #fff;
        font-size: 18px;
        text-align: center;
        line-height: 60px;
    }
    .header span
    {
        margin-right: 15px;
    }
    .header a
    {
        color: #fff;
        float: left;
        margin-left: 15px;
        height: 60px;
    }
    .contacts
    {
        color: #5D5D5D;
        margin: 5px 0px 0px 15px;
    }
    .check-item
    {
        float: right;
        width: 20px;
        height: 20px;
    }
    .address-item
    {
        font-size: 16px;
        border-top: 1px solid #ebebeb;
        background-color: #fff;
        height: 55px;
    }
    .address-list
    {
        color: #686868;
        font-size: 12px;
        padding: 3px 0px 5px 0px;
    }
    .often-labal
    {
        height: 35px;
        margin-left: 15px;
        color: #686868;
        line-height: 35px;
    }
    .check-item-checed
    {
        background-image: url("http://image.matewish.cn/frontmywish/address_default.png");
        background-size: cover;
        margin-right: 20px;
        margin-top: 10px;
    }
    .footer
    {
        position: fixed;
        bottom: 0px;
        width: 100%;
        border-top: 1px solid #ebebeb;
        background-color: #fff;
        height: 70px;
        text-align: center;
        line-height: 3.5em;
        font-size: 20px;
    }
    .footer a
    {
        color: #ff5757;

    }
    .footer img
    {
        width: 35px;
        vertical-align: middle;
        margin-right: 8px;
    }

</style>
<div class="header">
    <?= '';//Html::a('<','#') ?>
    <span>收货地址</span>
</div>
<div class="often-labal">
    <label>常用地址</label>
</div>
<?php foreach($alist as $a): ?>
    <div class="address-item" address_id="<?=$a->user_address_id?>" >
        <div class="contacts">
            <span><?=$a->contract_user?></span>&nbsp;&nbsp;&nbsp;
            <span><?=$a->contract_call?></span>
            <div class="check-item<?=($a->is_default === 1?' check-item-checed':'')?>">&nbsp;</div>
            <div class="address-list">
                <span><?=($a->is_default == '1'?'[默认]':'')?><?=($a->province === $a->city?($a->city.$a->area.$a->address):($a->province.$a->city.$a->area.$a->address))?></span>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<div class="footer">

        <a href="/mywish/add_address?token=<?=$token?>"><img src="http://image.matewish.cn/frontmywish/add_address.png"><span style="vertical-align: middle">新增收货地址</span></a>
</div>
<?php
$js='
$(".address-item").on("click",function(){
    $(".check-item").each(function(){
        $(this).removeClass("check-item-checed");
    });
    $(this).find(".check-item").addClass("check-item-checed");
    art.dialog.data("address_id",$(this).attr("address_id"));
    //alert(art.dialog.data("address_id"));
    art.dialog.close();
});
';
$this->registerJs($js);