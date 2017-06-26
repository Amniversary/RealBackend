<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 13:44
 */

return [
    'livingtime'=>[
        'class'=> 'backend\controllers\DataStatisticActions\LivingTimeAction',
    ],
    'masterprofit'=>[
        'class'=> 'backend\controllers\DataStatisticActions\MasterProfitAction',
    ],
    'living_statistic_time'=>[
        'class'=> 'backend\controllers\DataStatisticActions\DataLivingTimeAction',
    ],
    'set_living_time'=>[
        'class'=> 'backend\controllers\DataStatisticActions\SetLivingTimeAction',
    ],
    'set_valid_date'=>[
        'class'=> 'backend\controllers\DataStatisticActions\SetValidDateAction',
    ],
    'livingmaster_share' =>[
        'class'=> 'backend\controllers\DataStatisticActions\LivingmasterShareAction',
    ],
    'sharesource' =>[
        'class'=> 'backend\controllers\DataStatisticActions\SharesourceAction',
    ],
    'living_time_detail'=>[
        'class'=> 'backend\controllers\DataStatisticActions\DataLivingTimeDetailAction',
    ],
    'statistic_family'=>[
        'class'=> 'backend\controllers\DataStatisticActions\StatisticFamilyTicketAction',
    ],
    'statistic_balance'=>[
        'class'=> 'backend\controllers\DataStatisticActions\StatisticBalanceAction',
    ],

];