<style>
    /* 背景 */
    .content-header{
        display: none;
    }
    .content{
        margin-top:0 !important;
        background: #f8f8f8 url(http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/poster/bg.jpg) repeat top left;
    }

    /* 表格样式 */
    ul{
        padding: 0;
        margin: 0;
    }
    .top{
        height: 45px;
    }
    .user-form{
        width: 80%;
        margin: 0 auto;
        font-size: 16px;
        border-radius: 5px;
        overflow: hidden;
    }
    .col{
        display: inline-block;
        border: 2px solid #3c8dbc;
        width: 20%;
        text-align: center;
        float: left;
    }
    .bg-black{
        background-color: #3c8dbc !important;
    }
    .ft-white{
        font-size: #fff;
    }

    /* 统计图表样式 */

    #w0,#w1,#w2,#w3
    {
        width: 80%;
        margin: 0 auto;
    }
    .highcharts-container
    {
        padding: 10px 0;
    }
    .select{
        float: right;
        margin: 10px 20px 5px 0;
        font-size: 16px;
        font-family: "微软雅黑", arial, sans-serif;
        border: 2px solid #3c8dbc;
        border-radius: 5px;
    }
    .download{
        float: right;
        font-size: 16px;
        margin: 10px 20px 5px 0;
        background-color: #fff;
        font-family: "微软雅黑", arial, sans-serif;
        border: 2px solid #3c8dbc;
        border-radius: 5px;
        cursor: pointer;
    }
    .download span{
        margin: 10px 20px;
        color:  #000;
    }
</style>

<div class="user-form">
    <div>
        <h2>活跃主播</h2>
        <div class="top">
            <div class="col">
                <div class="user-id bg-black ft-white">今日</div>
                <div class="font-pd"><?= array_sum($ActiveAnchorNumDate['one_house_ActiveAnchorNumDate']['data']) ?></div>
            </div>
            <div class="col">
                <div class="client-no bg-black ft-white">昨日</div>
                <div class="font-pd"><?= array_sum($ActiveAnchorNumDate['yesterday_house_ActiveAnchorNumDate']['data'])  ?></div>
            </div>
            <div class="col">
                <div class="nick-name bg-black ft-white">近三天</div>
                <div class="font-pd"><?= array_sum($ActiveAnchorNumDate['three_ActiveAnchorNumDate']['data'])  ?></div>
            </div>
            <div class="col">
                <div class="nick-name bg-black ft-white">近七天</div>
                <div class="font-pd"><?= array_sum($ActiveAnchorNumDate['seven_ActiveAnchorNumDate']['data'])  ?></div>
            </div>
            <div class="col">
                <div class="nick-name bg-black ft-white">近三十天</div>
                <div class="font-pd"><?= array_sum($ActiveAnchorNumDate['thirty_ActiveAnchorNumDate']['data'])  ?></div>
            </div>
        </div>

        <a class="download">
            <span>保存图片至本地</span>
        </a>

        <select class="select-anchor select">
            <option value="anchor">今日/昨日</option>
            <option value="anchor_three">近三天</option>
            <option value="anchor_seven">近七天</option>
            <option value="anchor_thirty">近三十天</option>
        </select>
    </div>
</div>

<script src="https://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/js/jquery-2.0.3.min.js"></script>

<script>
    var svgXml = '';
    var ss = '';
    $(".select-anchor").change(function(){
        switch($(".select-anchor option:selected").val())
        {
            case 'anchor':
                $('#w0').show();
                $('#w1,#w2,#w3').hide();
                svgXml = $('#highcharts-0').html();
                ss = 1;
                break;
            case 'anchor_three':
                $('#w1').show();
                $('#w0,#w2,#w3').hide();
                svgXml = $('#highcharts-4').html();
                ss = 2;
                break;
            case 'anchor_seven':
                $('#w2').show();
                $('#w1,#w0,#w3').hide();
                svgXml = $('#highcharts-8').html();
                ss = 3;
                break;
            case 'anchor_thirty':
                $('#w3').show();
                $('#w1,#w2,#w0').hide();
                svgXml = $('#highcharts-12').html();
                ss = 4;
                break;
        }
    });

    window.onload = function(){
        $('#w0').show();
        $('#w1,#w2,#w3').hide();
    };

    //获取日期时间
    var date = new Date();
    var month = date.getMonth() + 1;
    var houseDate = date.getFullYear()+'-'+month+'-'+date.getDate();

    $('body').on('click','.download',function(){

        //初始化时如果没有得到SVG地址，默认获取第一张的地址，顺带解决异步造成图像显示不完全的问题
        if(svgXml == '')
        {
            svgXml = $('#highcharts-0').html();
            ss = 1;
        }

        //设置保存图片
        var image = new Image();
        image.src = 'data:image/svg+xml;base64,' + window.btoa(unescape(encodeURIComponent(svgXml))); //给图片对象写入base64编码的svg流

        console.log(image.src);
        var canvas = document.createElement('canvas');  //准备空画布
        canvas.width = $('#highcharts-0 svg').width();
        canvas.height = $('#highcharts-0 svg').height();

        var context = canvas.getContext('2d');  //取得画布的2d绘图上下文
        context.drawImage(image, 0, 0);

        console.log(canvas.toDataURL('image/png'));

        var download = '';

        switch(ss)
        {
            case 1:
                download = houseDate;
                break;
            case 2:
                download = getBeforeDate(2)+'到'+houseDate;
                break;
            case 3:
                download = getBeforeDate(6)+'到'+houseDate;
                break;
            case 4:
                download = getBeforeDate(29)+'到'+houseDate;
                break;
        }

        $('.download').attr('href',canvas.toDataURL('image/png'));
        $('.download').attr('download',"活跃主播数据"+download);
    });


    //获取日期
    function getBeforeDate(n){
        var n = n;
        var d = new Date();
        var year = d.getFullYear();
        var mon=d.getMonth()+1;
        var day=d.getDate();
        if(day <= n){
            if(mon>1) {
                mon=mon-1;
            }
            else {
                year = year-1;
                mon = 12;
            }
        }
        d.setDate(d.getDate()-n);
        year = d.getFullYear();
        mon=d.getMonth()+1;
        day=d.getDate();
        s = year+"-"+(mon<10?('0'+mon):mon)+"-"+(day<10?('0'+day):day);
        return s;
    }
</script>


<?php

/**
 * 活跃主播-小时
 */
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '活跃主播'],
        'chart' => [
            'class' => 'wld',
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => ['0时','1时','2时','3时','4时','5时','6时','7时','8时','9时','10时','11时','12时','13时','14时','15时','16时','17时','18时','19时','20时','21时','22时','23时']
        ],
        'yAxis' => [
            'title' => ['text' => '小时/人数']
        ],
        'series' => [$ActiveAnchorNumDate['one_house_ActiveAnchorNumDate'],$ActiveAnchorNumDate['yesterday_house_ActiveAnchorNumDate']],
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);

/**
 * 活跃主播-近三天
 */
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '活跃主播'],
        'chart' => [
//            'width' => 500,
//            'height' => 300,
            'class' => 'wld',
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => ['前天','昨天','今天']
        ],
        'yAxis' => [
            'title' => ['text' => '天/人数']
        ],
        'series' => [
            $ActiveAnchorNumDate['three_ActiveAnchorNumDate']
        ],
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);

/**
 * 活跃主播-近七天
 */
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '活跃主播'],
        'chart' => [
//            'width' => 500,
//            'height' => 300,
            'class' => 'wld',
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => ['前6天','前5天','前4天','前3天','前天','昨天','今天']
        ],
        'yAxis' => [
            'title' => ['text' => '天/人数']
        ],
        'series' => [
            $ActiveAnchorNumDate['seven_ActiveAnchorNumDate']
        ],
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);

/**
 * 活跃主播-近三十天
 */
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '活跃主播'],
        'chart' => [
//            'width' => 500,
//            'height' => 300,
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => ['29天','28天','27天','26天','25天','前24天','前23天','前22天','前21天','前20天','前19天','前18天','前17天','前16天','前15天','前14天','前13天','前12天','前11天','前10天','前9天','前8天','前7天','前6天','前5天','前4天','前3天','前天','昨天','今天']
        ],
        'yAxis' => [
            'title' => ['text' => '天/人数']
        ],
        'series' => [
            $ActiveAnchorNumDate['thirty_ActiveAnchorNumDate']
        ],
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);
?>