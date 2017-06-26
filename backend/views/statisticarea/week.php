<?php

////周统计  城市直播
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '周统计城市直播'],
        'chart' => [
//            'width' => 500,
            'height' => 500,
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => $area_week_date//日统计类别
        ],
        'yAxis' => [
            'title' => ['text' => '次数']
        ],
        'series' => $new_week_data,
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);

?>