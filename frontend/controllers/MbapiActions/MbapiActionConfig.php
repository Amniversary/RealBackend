<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/12
 * Time: 16:13
 */

return [
    'doaction'=>'frontend\controllers\MbapiActions\DoAction',
    'checkserver'=>'frontend\controllers\MbapiActions\ServerChceckAction',
    'upload_file'=>'frontend\controllers\MbapiActions\UploadFileAction',
    'create_api' => 'frontend\controllers\MbapiActions\CreateApiLogSQL',
    'statistic_active_start' => 'frontend\controllers\MbapiActions\StatisticApiLogActive',
    'statistic_active' => 'frontend\controllers\MbapiActions\SetStatisticApiLogActive',
];