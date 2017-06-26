<?php
////日统计  牛牛游戏玩家胜负场次
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '日统计牛牛游戏玩家胜负场次'],
        'chart' => [
//            'width' => 500,
//            'height' => 300,
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => $game_day_date
        ],
        'yAxis' => [
            'title' => ['text' => '场次/金币']
        ],
        'series' => [$win_day_data,$lose_day_data,$money_day_data],
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);

//周统计  牛牛游戏玩家胜负场次
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '周统计牛牛游戏玩家胜负场次'],
        'chart' => [
//            'width' => 500,
//            'height' => 300,
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => $game_week_date
        ],
        'yAxis' => [
            'title' => ['text' => '场次/金币']
        ],
        'series' => [$win_week_data,$lose_week_data,$money_week_data],
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);


//月统计  牛牛游戏玩家胜负场次
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '月统计牛牛游戏玩家胜负场次'],
        'chart' => [
//            'width' => 500,
//            'height' => 300,
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => $game_month_date
        ],
        'yAxis' => [
            'title' => ['text' => '场次/金币']
        ],
        'series' => [$win_month_data,$lose_month_data,$money_month_data],
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);

?>
