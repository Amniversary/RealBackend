<?php
namespace console\controllers;

use udokmeci\yii2beanstalk\BeanstalkController;

class WorkerController extends BeanstalkController
{
  // Those are the default values you can override

      const DELAY_PRIORITY = "1000"; //Default priority
      const DELAY_TIME = 5; //Default delay time

      // Used for Decaying. When DELAY_MAX reached job is deleted or delayed with
      const DELAY_MAX = 3;

    public function listenTubes(){
      return require(__DIR__ . '/BeanstalkActions/ListenTubesConfig.php');
    //    return ["tube",'test2','test3'];
    }

    public function init()
    {
        $this->beanstalk = \Yii::$app->beanstalk;
        parent::init();
    }
  /**
    *
    * @param Pheanstalk\Job $job
    * @return string  self::BURY
    *                 self::RELEASE
    *                 self::DELAY
    *                 self::DELETE
    *                 self::NO_ACTION
    *                 self::DECAY
    *  
    */

    public function actions()
    {
        return require(__DIR__ . '/BeanstalkActions/WorkerConfig.php');
    }


//    public function actionTest2($job){
//        $sentData = $job->getData();
//        try {
//           // something useful here
//          $test = var_export($sentData);
//          $everthingIsAllRight = true;
//
//           if($everthingIsAllRight == true){
//                fwrite(STDOUT, Console::ansiFormat("{$test} ---test2--  Everything is allright"."\n", [Console::FG_GREEN]));
//                //Delete the job from beanstalkd
//                return self::DELETE;
//           }
//
//           $everthingWillBeAllRight = false;
//           if($everthingWillBeAllRight == true){
//                fwrite(STDOUT, Console::ansiFormat("- Everything will be allright"."\n", [Console::FG_GREEN]));
//                //Delay the for later try
//                //You may prefer decay to avoid endless loop
//                return self::DELAY;
//           }
//
//           $IWantSomethingCustom = false;
//           if($IWantSomethingCustom==true){
//                Yii::$app->beanstalk->release($job);
//                return self::NO_ACTION;
//           }
//
//           fwrite(STDOUT, Console::ansiFormat("- Not everything is allright!!!"."\n", [Console::FG_GREEN]));
//           //Decay the job to try DELAY_MAX times.
//           return self::DECAY;
//
//           // if you return anything else job is burried.
//        } catch (\Exception $e) {
//            //If there is anything to do.
//            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
//            // you can also bury jobs to examine later
//            return self::BURY;
//        }
//    }
}