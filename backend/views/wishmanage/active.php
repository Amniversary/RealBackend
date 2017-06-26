<?php
use yii\bootstrap\Html;
?>
<style>
    .check-title
    {
        font-family: "微软雅黑", arial, sans-serif;
        font-size: 26pt;
        height: 80px;
        line-height:80px;
        text-align: center;
    }
    .check-button-list
    {
        width: 90%;
        text-align: left;
        margin: 0px auto;
    }

    .relate-contain
    {
        margin: 0px 0px 30px 0px;
        text-align: left;
    }
    .bottom-div
    {
        height: 1px;
    }
    .row-item
    {
        margin-top: 15px;
    }
    .item-label
    {
        font-weight: normal;
        padding: 5px;
        width: 250px;
        text-align: right;
        font-size: 20px;
    }
    .item-content
    {
        font-size: 20px;
    }
</style>
<div class="check-title">
    <p><?='设置愿望【'.$wish->wish_name.'】首次打赏活动'?></p>
</div>
<div class="form-group check-button-list">
    <?= Html::button('确认设置' , ['class' =>'btn btn-success check-refuse','id'=>'btn_refuse']).'&nbsp;&nbsp;'.Html::button('返回' , ['data-dismiss'=>'modal','aria-hidden'=>'true','class' =>'btn btn-success'])

    ?>
</div>
<div class="row-item">
    <label class="item-label">首次打赏，被打赏愿望奖励</label><?=Html::dropDownList('packet_for_wish',(isset($we)?$we->packet_for_wish:null),$items,['id'=>'packet_wish','class'=>'item-content'])?>
</div>
    <div class="row-item">
        <label class="item-label">首次打赏，打赏人奖励</label><?=Html::dropDownList('packet_for_wish',(isset($we)?$we->packet_for_reward:null),$items,['id'=>'packet_reward','class'=>'item-content'])?>
    </div>
<div class="relate-contain">
    <input type="hidden" id="has_submit" value="0">
</div>
<div class="bottom-div"></div>
<?php
$js = '
$(".check-refuse").on("click",function(){
        if($("#has_submit").val() == "1")
        {
            return;
        }
        //拒绝审核
        packet_wish = $("#packet_wish").val();
        packet_reward=$("#packet_reward").val();
        $("#has_submit").val("1");
        $url = "/wishmanage/set_active?wish_id='.$wish->wish_id.'";
        dataStr = "packet_wish="+packet_wish+"&packet_reward="+packet_reward;
        SubmitCheck($url,dataStr);
});

function SubmitCheck($url, dataStr)
{
            $.ajax({
        type: "POST",
        url: $url,
        data: dataStr,
        success: function(data)
            {
                $("#has_submit").val("0");
               data = $.parseJSON(data);
                if(data.code == "0")
                {
                    alert("设置成功成功");
                     $("#contact-modal").modal("hide");
                     location.href="/wishmanage/index";
                 }
                 else
                 {
                     alert("设置失败：" + data.msg);
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                $("#has_submit").val("0");
             }
        });
}
$(function(){
$("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
});
';
$this->registerJs($js,\yii\web\View::POS_END);
?>