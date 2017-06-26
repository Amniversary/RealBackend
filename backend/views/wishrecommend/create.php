<style>
    .check-title
    {
        font-family: "微软雅黑", arial, sans-serif;
        font-size: 26pt;
        height: 80px;
        line-height:80px;
        text-align: center;
    }
</style>
<div class="check-title">
    <p>设置推荐愿望</p>
</div>
<input type="hidden" id="sub_page_height" value="">
<iframe id="search_wish_data" width="100%" frameborder="0px" src="/wishrecommend/getsearchdata"></iframe>
<?php
$js = '
function close_modal()
{
    $("#contact-modal").modal("hide");
}

function reflash_data()
{
     $("#contact-modal").modal("hide");
     $("#wish_recommend_list").yiiGridView("applyFilter");
}
$(function(){
$("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
});
';
$this->registerJs($js,\yii\web\View::POS_END);
?>
