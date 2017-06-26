<style>
    .content-header{
        display: none;
    }
    .content{
        margin-top:0 !important;
        background: #f8f8f8 url(http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/poster/bg.jpg) repeat top left;
    }
    /* 直播间样式  */
    .box{
        width: 16%;
        min-width: 255px;
        display: inline-block;
    }
    .top{
        position: relative;
    }
    .info{
        position: absolute;
        z-index: 99;
        width: 100%;
        top: 3%;
        left: 50%;
        padding-top: 10px;
        transform: translate(-50%,-50%);
        -webkit-transform: translate(-50%,-50%);
        -moz-transform: translate(-50%,-50%);
        -o-transform: translate(-50%,-50%);
        font: "微软雅黑", arial, sans-serif;
        color: #fff;
        white-space:nowrap;
        background-image: linear-gradient(to bottom,  rgba(0,0,0,0.6) 0%, rgba(0, 0, 0, 0) 100%, #FFFFFF 100%);
    }
    .info p{
        display: inline-block;
        width: 49%;
        text-align: center;
        font-size: 10px;
    }
    .bottom{
        position: relative;
        font-size: 16px;
        cursor: pointer;
        color: #fff;
        bottom: 0;
    }
    .seal{
        display: inline-block;
        width: 33%;
        float: left;
        border-right: 1px solid #445b66;
        background-color: #2c3b42;
        text-align: center;
        padding: 10px 0;
        margin: 0;
    }
    .seal:hover{
        background-color: #445b66;
    }
    .Close{
        /*border: 1px solid transparent;*/
        text-align: center;
        background-color: #2c3b42;
        padding: 10px 0;
        margin: 0;
    }
    .Close:hover{
        background-color: #445b66;
    }
    .disable
    {
        display: inline-block;
        width: 33%;
        float: left;
        text-align: center;
        border-right: 1px solid #445b66;
        background-color: #2c3b42;
        padding: 10px 0;
        margin: 0;
    }
    .disable:hover{
        background-color: #445b66;
    }
    /* 蒙版  */
    .mack{
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: #000002;
        opacity: 0.2;
        z-index: 90;
    }
    /* 弹出框样式  */
    .close-pop,.seal-pop,.disable-pop
    {
        position: fixed;
        top: 50%;
        left: 60%;
        transform: translate(-50%,-50%);
        -webkit-transform: translate(-50%,-50%);
        -moz-transform: translate(-50%,-50%);
        -o-transform: translate(-50%,-50%);
        z-index: 100;
        width: 20%;
        color: #000;
        font-size: 16px;
        font-weight: bold;
    }
    .equal
    {
        display: inline-block;
        width: 50%;
        float: left;
        border: 1px solid #e6e6e6;
        padding: 3% 0;
        background-color: #fff;
    }
    .equal:hover
    {
        color:#3c8dbc;
    }
    .close-pop,.seal-pop,.disable-pop p {
        padding: 3% 0;
        margin: 0;
        text-align: center;
        cursor: pointer;
        color: #505050;
    }
    .pop-content{
        padding: 15% 0;
        text-align: center;
        margin: 0;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        background-color: #fff;
    }
    .pop-content input{
        background-color: transparent;
        border: 1px solid #e6e6e6;
        padding: 10px 0;
        outline:none;
    }
    .hide-content{
        display: none;
    }
    .btn
    {
        background-color: #445b66;
        color: #fff;
        margin: 10px;
    }
    .btn-list{
        position: relative;
        display: block;
        padding: 30px 10px;
    }

    /* box */
    .box
    {
        display: none;
    }
</style>

<!-- 蒙版 -->
<div class="mack">
</div>

<?php
/**
 * Created by PhpStorm.
 * User: wld
 * Date: 2017/2/17
 * Time: 11:55
 */
$this->registerJsFile('http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/jwplayer.js');
?>


<div class="btn-list">
<?php
$count = 0;

$length = count($data) ;

$s = 1;

for($i = 1;$i <= $length;$i ++)
{
    if($s == intval($i/6 + 1))
    {
        $btn_class = 'lv-btn'.$s;
?>
        <div class="btn" id=<?php echo $btn_class ?>>直播间监督室<?php echo $s ?></div>
<?php
        $s += 1;
    }
}
?>
</div>

<?php

foreach($data as $v) {
        $count++;
//        if($count >6)
//        {
//            break;
//        }
        $id = "living_stat".$count;
        $box = "box".$count;
?>
<div class="box" id=<?php echo $box ?>>
    <section class="top" id=<?php echo $id ?>>
    </section>
    <section class="info">
        <p>主播昵称:&nbsp <?php echo $v['nick_name'] ?></p>
        <p>主播ID:&nbsp <?php echo $v['client_no'] ?></p>
    </section>
    <section class="bottom">
        <div class="control">
            <p class="seal">
                禁播
            </p>
            <p class="disable">
                封号
            </p>
            <p class="Close">
                关闭
            </p>
        </div>
    </section>
    <div class="hide-content">
        <span id="pull_http_url"><?php echo $v['pull_http_url'] ?></span>
        <span id="pull_rtmp_url"><?php echo $v['pull_rtmp_url'] ?></span>
        <span id="pull_hls_url"><?php echo $v['pull_hls_url'] ?></span>
        <span id="living_id"><?php echo $v['living_id'] ?></span>
    </div>
</div>
<?php } ?>



<!-- 关闭直播间弹出框 -->
<div class="close-pop">
    <p class="pop-content">提示<br/><br/><span style="color: #a0a0a0">请确认关闭直播间</span></p>
    <div class="close-pop-control">
        <p class="close-no equal" id="no">
            取消
        </p>
        <p class="close-yes equal">
            确定关闭
        </p>
    </div>
</div>


<!-- 禁播弹出框 -->
<div class="seal-pop">
    <p class="pop-content">提示<br/><br/><span style="color: #a0a0a0">请确认禁播</span></p>
    <div class="seal-pop-control">
        <p class="seal-no equal" id="no">
            取消
        </p>
        <p class="seal-yes equal">
            确定
        </p>
    </div>
</div>


<!-- 禁号弹出框 -->
<div class="disable-pop">
    <div class="pop-content">
        <input type="text" placeholder="请输入禁号原因"/>
    </div>

    <div class="disable-pop-control">
        <p class="disable-no equal" id="no">
            取消
        </p>
        <p class="disable-yes equal">
            确定
        </p>
    </div>
</div>


<?php
$js = '
    $("#box1,#box2,#box3,#box4,#box5,#box6").css("display","inline-block");
    //点击监督直播间
    $(".btn").each(function(){
        $(this).click(function(){
            $(".box").hide();
            id = $(this).attr("id");
            id = id.substring(6,100);
            max_id = id * 6;
            id = (id - 1) * 6 +1;
            for(;id<=max_id;id++)
            {
                box = "#box"+id;
               // alert(box);
                $(box).css("display","inline-block");
                live(id,box);
            }
        })
    })

    function live(id,box){
        //播放视频设置
        id = "living_stat"+id;
        // console.log(id);

        var pull_http_url = $(box).find("#pull_http_url").text();
        var pull_rtmp_url = $(box).find("#pull_rtmp_url").text();
        var pull_hls_url = $(box).find("#pull_hls_url").text();

        var thePlayer= jwplayer(id).setup({
        flashplayer: "http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/jwplayer.flash.swf",
        width: living_width,
        height: living_height,
        aspectratio: "16:9",
        autostart:true,//自动播放
        stretching:"fill",
        sources: [
                {
                    "file":pull_http_url
                },
                {
                    "file":pull_rtmp_url
                },
                {
                    "file":pull_hls_url
                }
            ]
        });
        thePlayer.onPlaylistItem(function(){//开始播放一个视频时,但总觉得这个方法不稳定
            $(".jw-display-icon-container").hide();
        });
    }

    //将弹出框全部隐藏
    $(".close-pop,.disable-pop,.seal-pop,.mack").hide();

    living_width = $("#living_stat").width() - 5;
    living_height = $(".box").width()*1.775;


    $(".box").each(function(){
        //点击禁播
        $(this).find(".seal").click(function(){
                $(".seal-pop,.mack").show();
                var living_id = $(this).parents(".box").find("#living_id").text();


                //点击禁播按钮-操作直播间，需要得到禁播原因
                $(document).on("click",".seal-yes",function(){
                    var seal_reason =  "";
                    $.ajax({
                        type: "POST",
                        url: "/living/living_operation?living_id="+living_id+"&type=2"+"&seal_reason="+seal_reason,
                        data: "",
                        success: function(data)
                            {
                               data = $.parseJSON(data);
                                 if(data.code == 0)
                                 {
                                    parent.location.reload();
                                 }
                                 else
                                 {
                                     alert("封闭直播失败：" + data.msg);
                                 }
                            },
                        error: function (XMLHttpRequest, textStatus, errorThrown)
                            {
                                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                             }
                        });
                        return false;
                })
        });


         //点击禁号
        $(this).find(".disable").click(function(){
                $(".disable-pop,.mack").show();
                var living_id = $(this).parents(".box").find("#living_id").text();

                //点击禁号按钮-操作直播间，需要得到禁号原因
                $(document).on("click",".disable-yes",function(){
                    var seal_reason =  $(".pop-content input").val();
                    $.ajax({
                        type: "POST",
                        url: "/living/living_operation?living_id="+living_id+"&type=1"+"&seal_reason="+seal_reason,
                        data: "",
                        success: function(data)
                            {
                               data = $.parseJSON(data);
                                 if(data.code == 0)
                                 {
                                    parent.location.reload();
                                 }
                                 else
                                 {
                                     alert("封闭直播失败：" + data.msg);
                                 }
                            },
                        error: function (XMLHttpRequest, textStatus, errorThrown)
                            {
                                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                             }
                        });
                        return false;
                })
        });

        //点击关闭
        $(this).find(".Close").click(function(){
                $(".close-pop,.mack").show();
                var living_id = $(this).parents(".box").find("#living_id").text();

                //点击关闭按钮-操作直播间
                $(document).on("click",".close-yes",function(){
                    $.ajax({
                        type: "POST",
                        url: "/living/closelive?living_id="+living_id,
                        data: "",
                        success: function(data)
                            {
                               data = $.parseJSON(data);
                                 if(data.code == 0)
                                 {
                                    parent.location.reload();
                                 }
                                 else
                                 {
                                     alert("关闭直播失败：" + data.msg);

                                 }
                            },
                        error: function (XMLHttpRequest, textStatus, errorThrown)
                            {
                                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                             }
                        });

                        return false;
                })
        });

        $(document).on("click","#no",function(){
            $(".close-pop,.seal-pop,.disable-pop,.mack").hide();
        })

        //播放视频设置
        var id = $(this).find(".top").attr("id");

        var pull_http_url = $(this).find("#pull_http_url").text();
        var pull_rtmp_url = $(this).find("#pull_rtmp_url").text();
        var pull_hls_url = $(this).find("#pull_hls_url").text();

        // console.log(typeof pull_http_url);
        console.log(pull_http_url);
        //pull_http_url = "http:\/\/pili-live-hdl.mblive.cn\/mibolive\/livingmibooVKOWs4NpOJrZ5UFWThC-ZH7XVS0.flv"
        //                 http:\/\/pili-live-hdl.mblive.cn\/mibolive\/livingmibooVKOWs4NpOJrZ5UFWThC-ZH7XVS0.flv

        var thePlayer= jwplayer(id).setup({
        flashplayer: "http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/jwplayer.flash.swf",
        width: living_width,
        height: living_height,
        aspectratio: "16:9",
        autostart:true,//自动播放
        stretching:"fill",
        sources: [
                {
                    "file":pull_http_url
                },
                {
                    "file":pull_rtmp_url
                },
                {
                    "file":pull_hls_url
                }
            ]

        });

        thePlayer.onPlaylistItem(function(){//开始播放一个视频时,但总觉得这个方法不稳定
            $(".jw-display-icon-container").hide();
        });
    })
';

$this->registerJs($js,\yii\web\View::POS_END);
?>

