<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/17
 * Time: 上午2:15
 */

namespace backend\business;


use common\models\AttentionEvent;
use common\models\Authorization;
use common\models\AuthorizationList;
use common\models\BatchAttention;
use common\models\BatchKeywordList;
use common\models\KeywordParams;
use common\models\Keywords;
use common\models\MenuList;
use common\models\SignKeyword;
use yii\base\Exception;
use yii\db\Query;

class KeywordUtil
{

    /**
     * 返回关键字记录
     * @param $key_id
     * @return null|Keywords
     */
    public static function getKeyWord($key_id){
        return Keywords::findOne(['key_id'=>$key_id]);
    }

    /**
     * 返回已选菜单公众号
     * @param $id
     * @return array
     */
    public static function GetCustomAuthById($id){
        $query = (new Query())->from('wc_menu_list')
            ->select(['app_id'])
            ->where(['deploy_id'=>$id])
            ->all();
        $rst = [];
        foreach($query as $item){
            $rst[] = $item['app_id'];
        }
        return $rst;
    }

    /**
     * 根据签到id配置获取已配置关键字id列表
     * @param $sign_id
     * @return array
     */
    public static function GetSignKeyParams($sign_id)
    {
        $query = (new Query())
            ->select(['key_id'])
            ->from('wc_sign_keyword')
            ->where(['sign_id'=>$sign_id])
            ->all();
        $rst = [];
        foreach ($query as $item) {
            $rst[] = $item['key_id'];
        }
       return $rst;
    }

    /**
     * 根据消息id 获取已配置关键字id列表
     * @param $msgId
     * @return array
     */
    public static function GetGlobalMessageKeyParams($msgId)
    {
        $query = (new Query())
            ->select(['key_id'])
            ->from('wc_keyword_params')
            ->where(['msg_id'=>$msgId])
            ->all();
        $rst = [];
        foreach($query as $item) {
            $rst[] = $item['key_id'];
        }
        return $rst;
    }

    public static function GetMessageKeyWord($appId, $msgId)
    {
        $query = (new Query())
            ->select(['key_id'])
            ->from('wc_keyword_params')
            ->where(['app_id'=>$appId, 'msg_id'=>$msgId])
            ->all();
        $rst = [];
        foreach($query as $item) {
            $rst[] = $item['key_id'];
        }
        return $rst;
    }

    /**
     * 获取签到对应配置关键字
     * @return array
     */
    public static function  GetSignKeyWord()
    {
        $article = [];
        $articleList = Keywords::find()
            ->select(['key_id','keyword'])
            ->where(['global'=>3])
            ->all();

        foreach($articleList as $articled){
            $article[$articled['key_id']] = $articled['keyword'];
        }
        $rights = array_chunk($article,30,true);
        return $rights;
    }

    /**
     * 获取所有全局配置关键字
     * @return array
     */
    public static function GetGlobalKeyWord()
    {
        $article = [];
        $articleList = Keywords::find()
            ->select(['key_id','keyword'])
            ->where(['global'=>1])
            ->all();

        foreach($articleList as $articled){
            $article[$articled['key_id']] = $articled['keyword'];
        }
        $rights = array_chunk($article,30,true);
        return $rights;
    }
    /**
     * 获取公众号所有关键字
     * @param $appId
     * @return array
     */
    public static function GetMessageKeyList($appId)
    {
        $article = [];
        $articleList = Keywords::find()
            ->select(['key_id','keyword'])
            ->where(['app_id'=>$appId])
            ->all();

        foreach($articleList as $articled){
            $article[$articled['key_id']] = $articled['keyword'];
        }
        $rights = array_chunk($article,30,true);
        return $rights;
    }
    /**
     * 返回已选择关键字公众号
     * @param $key_id
     * @return array
     */
    public static function GetKeyWordAuthById($key_id){
        $query = (new Query())->from('wc_batch_keyword_list')
            ->select(['app_id'])
            ->where(['key_id'=>$key_id])
            ->all();
        $rst = [];
        foreach($query as $item){
            $rst[] = $item['app_id'];
        }
        return $rst;
    }

    /**
     * 保存公众号关键字配置
     * @param $params
     * @param $app_id
     * @param $msg_id
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SaveKeyWordParams($params, $app_id ,$msg_id, &$error)
    {
        try {
            $trans = \Yii::$app->db->beginTransaction();
            (new KeywordParams())->deleteAll(['app_id'=>$app_id,'msg_id'=>$msg_id]);//TODO: 删除用户原有权限数据
            $sql = '';
            $table = \Yii::$app->db;
            foreach ($params as $parList) {
                $sql .= sprintf('insert into %s_keyword_params (app_id, key_id, msg_id) values(%s,%s,%s);',$table->tablePrefix,$app_id,$parList,$msg_id);
            }
            $rst = $table->createCommand($sql)->execute();
            if( $rst <= 0 ){
                throw new Exception('保存权限数据异常');
            }
            $trans->commit();
        } catch(Exception $e) {
            $trans->rollBack();
            $error = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * 返回所有公众号列表
     * @return array
     */
    public static function GetAuthParams(){
        $article = [];
        $articleList = AuthorizationList::find()
            ->select(['record_id','nick_name'])
            ->orderBy('record_id')
            ->all();

        foreach($articleList as $articled){
            $article[$articled['record_id']] = $articled['nick_name'];
        }
        $rights = array_chunk($article,30,true);

        return $rights;
    }

    /**
     * 返回所有认证公众号列表
     * @return array
     */
    public static function GetAuthParamsByApprove(){
        $article = [];
        $articleList = AuthorizationList::find()
            ->select(['record_id','nick_name'])
            ->where('verify_type_info in (0,3,4,5)')
            ->orderBy('record_id')
            ->all();

        foreach($articleList as $articled){
            $article[$articled['record_id']] = $articled['nick_name'];
        }
        $rights = array_chunk($article,25,true);

        return $rights;
    }

    /**
     * 保存公众号配置
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SaveAuthParams($params,$key_id,&$error){
        try {
            $trans = \Yii::$app->db->beginTransaction();
            (new BatchKeywordList())->deleteAll(['key_id'=>$key_id]);//TODO: 删除用户原有权限数据
            $sql = '';
            $table = \Yii::$app->db;
            foreach ($params as $parList) {
                $sql .= sprintf('insert into %s_batch_keyword_list (key_id,app_id) values(%s,%s);',$table->tablePrefix,$key_id,$parList);
            }
            $rst = $table->createCommand($sql)->execute();
            if( $rst <= 0 ){
                throw new Exception('保存权限数据异常');
            }
            $trans->commit();
        } catch(Exception $e) {
            $trans->rollBack();
            $error = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * 返回已选择关注回复公众号
     * @param $msg_id
     * @return array
     */
    public static function GetAttentionAuthById($msg_id){
        $query = (new Query())->from('wc_batch_attention')
            ->select(['app_id'])
            ->where(['msg_id'=>$msg_id])
            ->all();
        $rst = [];
        foreach($query as $item){
            $rst[] = $item['app_id'];
        }
        return $rst;
    }


    /**
     * 保存公众号配置
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SaveAttentionAuthParams($params,$msg_id,&$error){
        try {
            $trans = \Yii::$app->db->beginTransaction();
            (new BatchAttention())->deleteAll(['msg_id'=>$msg_id]);//TODO: 删除用户原有权限数据
            $sql = '';
            $table = \Yii::$app->db;
            foreach ($params as $parList) {
                $sql .= sprintf('insert into %s_batch_attention (msg_id,app_id) values(%s,%s);',$table->tablePrefix,$msg_id,$parList);
            }
            $rst = $table->createCommand($sql)->execute();
            if( $rst <= 0 ){
                throw new Exception('保存权限数据异常');
            }
            $trans->commit();
        } catch(Exception $e) {
            $trans->rollBack();
            $error = $e->getMessage();
            return false;
        }
        return true;
    }



    /**
     * 保存批量自定义公众号配置
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SaveMenuAuthParams($params,$id,&$error){
        $query = AuthorizerUtil::getGlobalMenuList($id);
        $error = '';
        if(!$query) {
            $error = '菜单列表为空, 请先设置菜单'."\n";
            return false;
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            (new MenuList())->deleteAll(['deploy_id'=>$id]);//TODO: 删除用户原有权限数据
            $sql = '';
            $table = \Yii::$app->db;
            foreach ($params as $parList) {
                $auth = AuthorizerUtil::getAuthByOne($parList);
                $call_back = WeChatUserUtil::setMenuList($query,$auth->authorizer_access_token,$back_error);
                if(!$call_back) {
                   $error .= $back_error."\n";
                    continue;
                }
                if($call_back['errcode'] != 0 || !$call_back) {
                    $error .= '设置失败:'.$auth->nick_name.' ID :'.$auth->record_id.' Code: '.$call_back['errcode'] . ' '. $call_back['errmsg']."\n";
                    continue;
                }
                $sql .= sprintf('insert into %s_menu_list (app_id,deploy_id) values(%s,%s);',$table->tablePrefix,$parList,$id);
            }
            $rst = $table->createCommand($sql)->execute();
            if( $rst <= 0 ){
                $error .= '保存权限数据异常';
                return false;
            }
            $trans->commit();
        } catch(Exception $e) {
            $trans->rollBack();
            $error = $e->getMessage();
            return false;
        }
        return true;
    }

    public static function SaveGlobalKeyWordParams($params ,$msg_id, &$error)
    {
        try {
            $trans = \Yii::$app->db->beginTransaction();
            (new KeywordParams())->deleteAll(['msg_id'=>$msg_id]);//TODO: 删除用户原有权限数据
            $sql = '';
            $table = \Yii::$app->db;
            foreach ($params as $parList) {
                $sql .= sprintf('insert into %s_keyword_params ( key_id, msg_id) values(%s,%s);',$table->tablePrefix,$parList,$msg_id);
            }
            $rst = $table->createCommand($sql)->execute();
            if( $rst <= 0 ){
                throw new Exception('保存权限数据异常');
            }
            $trans->commit();
        } catch(Exception $e) {
            $trans->rollBack();
            $error = $e->getMessage();
            return false;
        }
        return true;
    }


    public static function SaveSignKeyWordParams($params, $id, &$error)
    {
        try {
            $trans = \Yii::$app->db->beginTransaction();
            (new SignKeyword())->deleteAll(['sign_id'=>$id]);//TODO: 删除用户原有权限数据
            $sql = '';
            $table = \Yii::$app->db;
            foreach ($params as $parList) {
                $sql .= sprintf('insert into %s_sign_keyword (key_id, sign_id) values(%s,%s);',$table->tablePrefix,$parList,$id);
            }
            $rst = $table->createCommand($sql)->execute();
            if( $rst <= 0 ){
                throw new Exception('保存权限数据异常');
            }
            $trans->commit();
        } catch(Exception $e) {
            $trans->rollBack();
            $error = $e->getMessage();
            return false;
        }
        return true;
    }

}