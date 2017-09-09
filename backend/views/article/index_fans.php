<style>
    .content-header {
        position: relative;
        padding: 1px 15px 0 15px;
    }
    .content {
        padding: 0 15px 0 15px;
    }
    .user-form {
        /*width: 80%;*/
        margin: 0 auto;
        font-size: 16px;
        border-radius: 5px;
        overflow: hidden;
    }
    .col {
        display: inline-block;
        border: 1px solid #cee2ee;
        width: 15%;
        text-align: center;
        float: left;
    }
    .bg-blue {
        background-color: #3c8dbc !important;
    }
    .bg-black {
        background-color: #434348 !important;
    }
    .bg-green {
        background-color: #90ed7d !important;
    }
    .highcharts-container {
        padding: 10px 0;
    }
    .select {
        float: right;
        margin: 10px 20px 5px 0;
        font-family: "微软雅黑", arial, sans-serif;
        border: 1px solid #3c8dbc;
        border-radius: 5px;
    }
</style>
<?php
/**
 * 粉丝折线图统计
 * @var $authList
 * @var $app_id
 * @var $ToDay
 * @var $Yesterday
 * @var $WeekNum
 * @var $FourTeen
 * @var $Thirty
 */
?>
<div class="user-form" style="height:1100px;font-size:14px;">
    <div>
        <h3><?= $authList[$app_id] ?></h3>
        <div class="top">
            <div class="col">
                <div class="bg-blue">今天</div>
                <div><?= array_sum($ToDay['net_user']['data']) ?></div>
                <div class="bg-black">今天</div>
                <div><?= array_sum($ToDay['new_user']['data']) ?></div>
                <div class="bg-green">今天</div>
                <div><?= array_sum($ToDay['cancel_user']['data']) ?></div>
            </div>
            <div class="col">
                <div class="bg-blue">昨天</div>
                <div><?= array_sum($Yesterday['net_user']['data']) ?></div>
                <div class="bg-black">昨天</div>
                <div><?= array_sum($Yesterday['new_user']['data']) ?></div>
                <div class="bg-green">昨天</div>
                <div><?= array_sum($Yesterday['cancel_user']['data']) ?></div>
            </div>
            <div class="col">
                <div class="bg-blue">七天</div>
                <div><?= array_sum($WeekNum['net_user']['data']) ?></div>
                <div class="bg-black">七天</div>
                <div><?= array_sum($WeekNum['new_user']['data']) ?></div>
                <div class="bg-green">七天</div>
                <div><?= array_sum($WeekNum['cancel_user']['data']) ?></div>
            </div>
            <div class="col">
                <div class="bg-blue">十四天</div>
                <div><?= array_sum($FourTeen['net_user']['data']) ?></div>
                <div class="bg-black">十四天</div>
                <div><?= array_sum($FourTeen['new_user']['data']) ?></div>
                <div class="bg-green">十四天</div>
                <div><?= array_sum($FourTeen['cancel_user']['data']) ?></div>
            </div>
            <div class="col">
                <div class="bg-blue">三十天</div>
                <div><?= array_sum($Thirty['net_user']['data']) ?></div>
                <div class="bg-black">三十天</div>
                <div><?= array_sum($Thirty['new_user']['data']) ?></div>
                <div class="bg-green">三十天</div>
                <div><?= array_sum($Thirty['cancel_user']['data']) ?></div>
            </div>
        </div>


        <?= \yii\helpers\Html::dropDownList('auth-list', [$app_id], $authList, ['class' => 'auth-list select']) ?>
        <select class="select-change select">
            <option value="1">今天 / 昨天</option>
            <option value="7">近七天</option>
            <option value="14">近十四天</option>
            <option value="30">近三十天</option>
        </select>
    </div>
    <?php
    /**
     * 粉丝统计 今天
     */
    echo \miloschuman\highcharts\Highcharts::widget([
        'options' => [
            'title' => ['text' => date('Y-m-d') . '今日24小时粉丝统计'],
            'yAxis' => [
                'allowDecimals' => false,
                'title' => ['text' => '日期 / 粉丝数']
            ],
            'xAxis' => [
                'categories' => $ToDay['date']
            ],
            'series' => [$ToDay['net_user'], $ToDay['new_user'], $ToDay['cancel_user']],
            'credits' => ['enabled' => false],
        ]]);

    /**
     * 粉丝统计 昨天
     */
    echo \miloschuman\highcharts\Highcharts::widget([
        'options' => [
            'title' => ['text' => date('Y-m-d', strtotime('-1 day')) . '昨日24小时粉丝统计'],
            'yAxis' => [
                'allowDecimals' => false,
                'title' => ['text' => '日期 / 粉丝数']
            ],
            'xAxis' => [
                'categories' => $Yesterday['date']
            ],
            'series' => [$Yesterday['net_user'], $Yesterday['new_user'], $Yesterday['cancel_user']],
            'credits' => ['enabled' => false],
        ]]);

    /**
     * 粉丝统计 近七天
     */
    echo \miloschuman\highcharts\Highcharts::widget([
        'options' => [
            'title' => ['text' => '近七天粉丝统计'],
            'yAxis' => [
                'allowDecimals' => false,
                'title' => ['text' => '日期 / 粉丝数']
            ],
            'xAxis' => [
                'categories' => $WeekNum['date']
            ],
            'series' => [$WeekNum['net_user'], $WeekNum['new_user'], $WeekNum['cancel_user']],
            'credits' => ['enabled' => false],
        ]]);

    /**
     * 粉丝累计统计 近七天
     */
    echo \miloschuman\highcharts\Highcharts::widget([
        'options' => [
            'title' => ['text' => '近七天累计粉丝统计'],
            'yAxis' => [
                'allowDecimals' => false,
                'title' => ['text' => '日期 / 粉丝数']
            ],
            'xAxis' => [
                'categories' => $WeekNum['date']
            ],
            'series' => [$WeekNum['total_user']],
            'credits' => ['enabled' => false],
        ]]);
    /**
     * 粉丝统计-近十四天
     */
    echo \miloschuman\highcharts\Highcharts::widget([
        'options' => [
            'title' => ['text' => '近十四天粉丝统计'],

            'xAxis' => [
                'categories' => $FourTeen['date']
            ],
            'yAxis' => [
                'title' => ['text' => '日期 / 粉丝数']
            ],
            'series' => [$FourTeen['net_user'], $FourTeen['cancel_user'], $FourTeen['new_user']],
            'credits' => [
                'enabled' => false
            ],
        ]
    ]);

    /**
     * 粉丝累计统计-近十四天
     */
    echo \miloschuman\highcharts\Highcharts::widget([
        'options' => [
            'title' => ['text' => '近十四天粉丝统计'],
            'xAxis' => [
                'categories' => $FourTeen['date']
            ],
            'yAxis' => [
                'title' => ['text' => '日期 / 粉丝数']
            ],
            'series' => [$FourTeen['total_user']],
            'credits' => [
                'enabled' => false
            ],
        ]
    ]);

    /**
     * 粉丝统计-近三十天
     */
    echo \miloschuman\highcharts\Highcharts::widget([
        'options' => [
            'title' => ['text' => '近三十天粉丝统计'],
            'xAxis' => [
                'categories' => $Thirty['date']
            ],
            'yAxis' => [
                'title' => ['text' => '日期 / 粉丝数']
            ],
            'series' => [$Thirty['net_user'], $Thirty['cancel_user'], $Thirty['new_user']],
            'credits' => [
                'enabled' => false
            ],
        ]
    ]);

    /**
     * 粉丝累计统计-近三十天
     */
    echo \miloschuman\highcharts\Highcharts::widget([
        'options' => [
            'title' => ['text' => '近三十天粉丝统计'],
            'xAxis' => [
                'categories' => $Thirty['date']
            ],
            'yAxis' => [
                'title' => ['text' => '日期 / 粉丝数']
            ],
            'series' => [$Thirty['total_user']],
            'credits' => [
                'enabled' => false
            ],
        ]
    ]);
    ?>
</div>
<?php
$js = '
window.onload = function () {
        $("#w0, #w1").show();
        $("#w2, #w3, #w4, #w5, #w6, #w7").hide();
    }
    $(".select-change").change(function () {
        switch ($(".select-change option:selected").val()) {
            case "1":
                $("#w0,#w1").show();
                $("#w2,#w3,#w4,#w5,#w6, #w7").hide();
                break;
            case "7":
                $("#w2,#w3").show();
                $("#w0,#w1,#w4,#w5,#w6, #w7").hide();
                break;
            case "14":
                $("#w4,#w5").show();
                $("#w0,#w1,#w2,#w3,#w6, #w7").hide();
                break;
            case "30":
                $("#w6,#w7").show();
                $("#w0,#w1,#w2,#w3,#w4, #w5").hide();
                break;
        }
    });
    $(".auth-list").change(function () {
        $vue = $(".auth-list option:selected").val();
        location = "/article/index_fans?app_id=" + $vue;
    });

';
$this->registerJs($js, \yii\web\View::POS_END);


