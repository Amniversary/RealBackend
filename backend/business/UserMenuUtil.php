<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/19
 * Time: 17:29
 */

namespace backend\business;


use common\models\User;
use yii\db\Query;
use common\models\UserMenu;
use common\models\Menu;
use yii\log\Logger;
use yii\base\Exception;

class UserMenuUtil
{
    /**
     * 获取用户菜单，并返回内部权限数组
     * @param $user_id
     * @param $rootId
     * @param array $menu 菜单
     * @param array $innerMenu  内部权限
     * @return array
     */
    public static function GetUserMenu($user_id,$rootId,&$innerMenu=[])
    {
        $menu=[];
        if(!is_array($innerMenu)) {
            $innerMenu = [];
        }
        $query = new Query();
        $menuList = $query->from(UserMenu::tableName().' um')->innerJoin(Menu::tableName().' mu','um.menu_id = mu.menu_id')
            ->select(['mu.menu_id','mu.title','mu.icon','mu.url','visible'])
            ->where(['um.user_id'=>$user_id,'mu.status'=>1,'mu.parent_id'=>$rootId])
            ->orderBy('mu.parent_id asc,mu.order_no asc')
            ->all();
        foreach($menuList as $m)
        {
            $menu_id = $m['menu_id'];
            if($m['visible'] == '1')//0 不显示菜单 没有子菜单
            {
                $menu[$menu_id]= [
                    'label'=>$m['title'],
                    'icon'=>$m['icon'],
                    'url'=>((strpos($m['url'],'*') === false) ?[$m['url']]:'#'),
                    'items'=>[]
                ];
                $menu[$menu_id]['items'] = self::GetUserMenu($user_id,$m['menu_id'],$innerMenu);
            }
            $innerMenu[]=$m['url'];
        }
        return $menu;
    }

    /**
     * 获取用户已有权限
     * @param $user_id
     * @return array
     */
    public static function GetUserMenuByUserID($user_id)
    {
        $selection = [];
        $rightList = UserMenu::find()
            ->select(['menu_id'])
            ->where(['user_id'=>$user_id])
            ->all();

        foreach ($rightList as $selected) {
            $selection[] = $selected['menu_id'];
        }
        return $selection;
    }

    /**
     * 获取权限列表
     * @return array
     */
    public static function GetUserMenuTitle()
    {
        $article = [];
        $articleList = Menu::find()
            ->select(['menu_id','title'])
            ->all();

        foreach($articleList as $articled){
            $article[$articled['menu_id']] = $articled['title'];
        }

        $rights = array_chunk($article,30,true);

        return $rights;
    }

    /**
     * 保存数据
     * @param $model
     * @param $error
     * @return bool
     */
    public static function isSave($model,&$error)
    {
        if(!$model instanceof UserMenu){
            $error = '不是权限信息记录';
            return false;
        }
        if(!$model->save()){
            $error = '保存权限信息失败';
            \Yii::error($error . '_' .var_export($model->getErrors(),true));
            return false;
        }
        return true;
    }

    /**
     * 保存用户权限操作
     * @param $params //TODO： 操作权限id
     * @param $error //TODO： 操作用户id
     * @return bool
     */
    public static function SaveUserMenus($params,$user_id,&$error)
    {
        try {
            $trans = \Yii::$app->db->beginTransaction();
            (new UserMenu())->deleteAll(['user_id'=>$user_id]);//TODO: 删除用户原有权限数据
            $sql = '';
            $table = \Yii::$app->db;
            foreach ($params as $parList) {
                $sql .= sprintf('insert into %s_user_menu (user_id,menu_id) values(%s,%s);',$table->tablePrefix,$user_id,$parList);
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