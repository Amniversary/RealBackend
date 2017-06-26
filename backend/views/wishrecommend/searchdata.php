<?php
use yii\bootstrap\Html;
use kartik\grid\GridView;
?>
<style>
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
</style>
<div class="form-group check-button-list">
    <?= Html::button('确定' , ['class' =>'btn btn-success check-pass','id'=>'btn_pass']).'&nbsp;&nbsp;'.Html::button('返回' , ['id'=>'back_index','class' =>'btn btn-success'])
    ?>
</div>
<div class="user-info">
    <p>愿望列表信息</p>
</div>
<?php
$data_columns = [
    [
        //'label'=>'愿望id',
        //'attribute'=>'wish_id',
        'class'=>'\yii\grid\CheckboxColumn',
        'checkboxOptions'=>function($model)
        {
            return [
                'value'=>$model['wish_id'],
            ];
        },
        'name'=>'wish_id',
    ],
    [
        'label'=>'愿望标题',
        'attribute'=>'wish_name',
    ],
    [
        'label'=>'发布人',
        'attribute'=>'nick_name',
    ],
    [
        'label'=>'发布人电话',
        'attribute'=>'phone_no',
    ],
    [
        'label'=>'发布时间',
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
];
?>
<div class="check-refuse-contain">
    <?= GridView::widget([
        'id'=>'wish_search_list',
        'dataProvider'=>$dataProvider,
        'filterModel' => $searchModel,
        'columns'=>$data_columns,
        'layout'=>'{items}{pager}'
    ]);
    ?>
</div>
<?php
$js = '
$(".check-pass").on("click",function(){
    var keys = $("#wish_search_list").yiiGridView("getSelectedRows");
    //通过审核
    if(keys.length <= 0)
    {
        alert("请选择愿望");
        return;
    }
    if(confirm("确定设置成推荐愿望吗？"))
    {
        if($("#has_submit").val() == "1")
        {
            return;
        }
        $("#has_submit").val("1");
        $url = "/wishrecommend/create";
        dataStr = "";
        for(i=0; i < keys.length;i++)
        {
            dataStr +="&WishId["+i.toString()+"]="+keys[i].toString();
        }
        if(dataStr.length > 0)
        {
            dataStr=dataStr.substring(1);
        }
        SubmitCheck($url,dataStr);
    }
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
                    alert("数据提交成功");
                    window.parent.reflash_data();
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
$("#back_index").on("click",function(){
    window.parent.close_modal();
});
$(function(){
    height = $("#sub_page_height",parent.document).val();
    if(height == "")
    {
        height = $(document).height();
        if(height < 420)
        {
            height = 424;
        }
    }
    $("#search_wish_data",parent.document).height(height);
    $("#sub_page_height",parent.document).val(height);
});
';
$this->registerJs($js,\yii\web\View::POS_END);
?>
