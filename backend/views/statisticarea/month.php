<?php

////月统计  城市直播
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '月统计城市直播'],
        'chart' => [
//            'width' => 500,
//            'height' => 400,
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => $area_month_date//日统计类别
        ],
        'yAxis' => [
            'title' => ['text' => '次数']
        ],
        'series' => $new_month_data,
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);

?>