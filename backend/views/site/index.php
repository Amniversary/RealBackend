<?php

/* @var $this yii\web\View */
/* @var $model */
$this->title = 'Real数据平台';
 \common\assets\ArtDialogAsset::register($this);
?>
<style>
    .content-header{position: relative;  padding: 1px 15px 0 15px;}
    /*#w0{ width:31%; display: inline-block;margin-right: 2% ;margin-bottom:10px;}*/
    /*#w1{ width:31%; display: inline-block;margin-right: 2% ;margin-bottom:10px;}*/
    /*#w2{ width:31%; display: inline-block;margin-right: 2% ;margin-bottom:10px;}*/
    /*#w3{ width:31%; display: inline-block;margin-right: 2% ;margin-bottom:10px;}*/
    /*#w4{ width:31%; display: inline-block;margin-right: 2% ;margin-bottom:10px;}*/
    /*#w5{ width:31%; display: inline-block;margin-right: 2% ;margin-bottom:10px;}*/
</style>

<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?= $model['new_user'] ?></h3>
                <p>今日新增粉丝</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?= $model['net_user'] ?></h3>
                <p>今日净增粉丝</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3><?= $model['count'] ?></h3>

                <p>粉丝总数</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3><?= $model['cumulate_user']?></h3>

                <p>累计粉丝总数</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
<h1 style="text-align: center">Real数据平台</h1>
<?php

//日统计表
/*echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '日统计表'],
        'chart' => [
            'height' => 300,
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => ''//日统计类别
        ],
        'yAxis' => [
            'title' => ['text' => '金额/人数/次数']
        ],
        'series' => [
            ['name' => '充值总金额', 'data' => ''],
            ['name' => '提现总金额', 'data' => ''],
            ['name' => '直播次数', 'data' => ''],
            ['name' => '直播人数', 'data' => ''],
            ['name' => '打赏次数', 'data' => ''],
            ['name' => '打赏人数', 'data' =>''],
//            ['name' => '日活', 'data' => $day_num['user_day']],
            ['name' => '日注册人数', 'data' => ''],
        ],
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);/*


//周统计表
/*echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '周统计表'],
        'chart' => [
            'height' => 300,
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => $week_date//日统计类别
        ],
        'yAxis' => [
            'title' => ['text' => '金额/人数/次数']
        ],
        'series' => [
            ['name' => '充值总金额', 'data' => $week_num['recharge_other_week']],
            ['name' => '提现总金额', 'data' => $week_num['cach_other_week']],
            ['name' => '直播次数', 'data' => $week_num['living_other_week']],
            ['name' => '直播人数', 'data' => $week_num['living_person_week']],
            ['name' => '打赏次数', 'data' => $week_num['reward_other_week']],
            ['name' => '打赏人数', 'data' => $week_num['reward_person_other_week']],
            ['name' => '周注册人数', 'data' => $week_num['reg_user_week']],
        ],
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);



//月统计表
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '月统计表'],
        'chart' => [
            'height' => 300,
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => $month_date//日统计类别
        ],
        'yAxis' => [
            'title' => ['text' => '金额/人数/次数']
        ],
        'series' => [
            ['name' => '充值总金额', 'data' => $month_num['recharge_other_month']],
            ['name' => '提现总金额', 'data' => $month_num['cach_other_month']],
            ['name' => '直播次数', 'data' => $month_num['living_other_month']],
            ['name' => '直播人数', 'data' => $month_num['living_person_month']],
            ['name' => '打赏次数', 'data' => $month_num['reward_other_month']],
            ['name' => '打赏人数', 'data' => $month_num['reward_person_other_month']],
//            ['name' => '月活', 'data' => $month_num['user_month']],
            ['name' => '月注册人数', 'data' => $month_num['reg_user_month']],
        ],
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);


////日统计男性用户
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '日统计男性用户'],
        'chart' => [
            'height' => 300,
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => $day_men_date//日统计类别
        ],
        'yAxis' => [
            'title' => ['text' => '人数']
        ],
        'series' => [
            ['name' => '16-20岁', 'data' => $day_men_num['age_16_20']],
            ['name' => '20-25岁', 'data' => $day_men_num['age_20_25']],
            ['name' => '25-30岁', 'data' => $day_men_num['age_25_30']],
            ['name' => '30-35岁', 'data' => $day_men_num['age_30_35']],
            ['name' => '大于35岁', 'data' => $day_men_num['age_35']],
        ],
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);

////日统计女性用户
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '日统计女性用户'],
        'chart' => [
            'height' => 300,
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => $day_women_date//日统计类别
        ],
        'yAxis' => [
            'title' => ['text' => '金额/人数/次数']
        ],
        'series' => [
            ['name' => '16-20岁', 'data' => $day_women_num['age_16_20']],
            ['name' => '20-25岁', 'data' => $day_women_num['age_20_25']],
            ['name' => '25-30岁', 'data' => $day_women_num['age_25_30']],
            ['name' => '30-35岁', 'data' => $day_women_num['age_30_35']],
            ['name' => '大于35岁', 'data' => $day_women_num['age_35']],
        ],
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);


////日统计  用户等级段
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '日统计用户等级段'],
        'chart' => [
            'height' => 300,
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => $day_level_date//日统计类别
        ],
        'yAxis' => [
            'title' => ['text' => '人数']
        ],
        'series' => [
            ['name' => '1-13级', 'data' => $level_month_num['level_1_13']],
            ['name' => '14-20级', 'data' => $level_month_num['level_14_20']],
            ['name' => '21-25级', 'data' => $level_month_num['level_21_25']],
            ['name' => '26-30级', 'data' => $level_month_num['level_26_30']],
            ['name' => '31-40级', 'data' => $level_month_num['level_31_40']],
            ['name' => '41-50级', 'data' => $level_month_num['level_41_50']],
            ['name' => '51-80级', 'data' => $level_month_num['level_51_80']],
            ['name' => '80级以上', 'data' => $level_month_num['level_80']],

        ],
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);*/
?>


