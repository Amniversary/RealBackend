<?php

////日统计  城市直播
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => '日统计城市直播'],
        'chart' => [
//            'width' => 500,
//            'height' => 400,
            'style' => [
                'float' => 'left',
            ],
        ],
        'xAxis' => [
            'categories' => $area_day_date//日统计类别
        ],
        'yAxis' => [
            'title' => ['text' => '次数']
        ],
        'series' => $new_day_data,
        'credits'=>[
            'enabled'=>false
        ],
    ]
]);

?>