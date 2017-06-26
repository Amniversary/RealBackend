<?php
// +----------------------------------------------------------------------
// | Easemob for PHP [ WE CAN DO IT JUST THINK IT ]
// | 该版本由校内秘密，后台成员小李飞刀同学，经过半天的整理，方便大家使用。校内秘密，是一款校园内的匿名交友APP
// +----------------------------------------------------------------------
// | Copyright (c) 2014 校内秘密  www.xiaoneimimi.com
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: limx <limx@xiaoneimimi.com>
// +----------------------------------------------------------------------
namespace common\components\chat\emchatserver;
use yii\log\Logger;

/**
 * 环信-服务器端REST API
 * @author    limx <limx@xiaoneimimi.com>
 */
class Easemob {
	private $client_id='YXA60BKAgNbgEeW43meNiB7L7w';
	private $client_secret='YXA6hXQejhFjow5xX_HuHrnfU7wOYz4';
	private $org_name='meiyuan';
	private $app_name='meiyuan';
	private $url;
	
	/**
	 * 初始化参数
	 *
	 * @param array $options   
	 * @param $options['client_id']    	
	 * @param $options['client_secret'] 
	 * @param $options['org_name']    'meiyuan'
	 * @param $options['app_name']    'meiyuan'
	 */
/*	public function __construct($options) {
		$this->client_id = isset ( $options ['client_id'] ) ? $options ['client_id'] : '';
		$this->client_secret = isset ( $options ['client_secret'] ) ? $options ['client_secret'] : '';
		$this->org_name = isset ( $options ['org_name'] ) ? $options ['org_name'] : '';
		$this->app_name = isset ( $options ['app_name'] ) ? $options ['app_name'] : '';
		if (! empty ( $this->org_name ) && ! empty ( $this->app_name )) {
			$this->url = 'https://a1.easemob.com/' . $this->org_name . '/' . $this->app_name . '/';
		}
	}*/

    public function __construct()
    {
        if (! empty ( $this->org_name ) && ! empty ( $this->app_name )) {
            $this->url = 'https://a1.easemob.com/' . $this->org_name . '/' . $this->app_name . '/';
        }
    }
	
	/**
	 * 授权注册模式 || 批量注册
	 *
	 * @param $options['username'] 用户名        	
	 * @param $options['password'] 密码
	 *        	批量注册传二维数组
	 */
	public function accreditRegister($options) {
		$url = $this->url . "users";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, $options, $header );
        /*{
                "action" : "post",
                "application" : "d0128080-d6e0-11e5-b8de-678d881ecbef",
                "path" : "/users",
                "uri" : "https://a1.easemob.com/meiyuan/meiyuan/users",
                "entities" :[
                                {
                                    "uuid" : "e9413e2a-e428-11e5-8220-87fa3c41b9fb",
                                    "type" : "user",
                                    "created" : 1457330006786,
                                    "modified" : 1457330006786,
                                    "username" : "4",
                                    "activated" : true
                                }
                            ],
                "timestamp" : 1457330006783,
                "duration" : 40,
                "organization" : "meiyuan",
                "applicationName" : "meiyuan"
        }
        */
		return $result;
	}

    /**
     * 批量注册用户
     * @param $options 二维数组
     * [
     *      [
     *          'username'=>'',
     *          'password'=>'',
     *      ],
     *      [
     *          'username'=>'',
     *          'password'=>'',
     *      ],
     * ]
     * @return array|mixed|string
     */
    public function accreditBatchRegister($options) {
        $url = $this->url . "users";
        $access_token = $this->getToken ();
        $header [] = 'Authorization: Bearer ' . $access_token;
        $result = $this->postCurl ( $url, $options, $header );
        return $result;
    }

	/**
	 * 获取指定用户详情
	 *
	 * @param $username 用户名        	
	 */
	public function userDetails($username) {
		$url = $this->url . "users/" . $username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = 'GET' );
		return $result;
	}
	
	/**
	 * 重置用户密码
	 *
	 * @param $options['username'] 用户名        	
	 * @param $options['password'] 密码        	
	 * @param $options['newpassword'] 新密码        	
	 */
	public function editPassword($options) {
		$url = $this->url . "users/" . $options ['username'] . "/password";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, $options, $header, $type = 'PUT');
		return $result;
	}

    /**
     * 修改用户昵称
     * @param $username
     * @param $nickname
     */
    public function ModifyUserName($username,$nickname)
    {
        $options=['nickname'=>$nickname];
        $url = $this->url . "users/" . $username ;
        $access_token = $this->getToken ();
        $header [] = 'Authorization: Bearer ' . $access_token;
        $result = $this->postCurl ( $url, $options, $header, $type = 'PUT');
        return $result;
    }

	/**
	 * 删除用户
	 *
	 * @param $username 用户名        	
	 */
	public function deleteUser($username) {
		$url = $this->url . "users/" . $username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = 'DELETE' );
        return $result;
	}
	
	/**
	 * 批量删除用户
	 * 描述：删除某个app下指定数量的环信账号。上述url可一次删除300个用户,数值可以修改 建议这个数值在100-500之间，不要过大
	 *
	 * @param $limit="300" 默认为300条        	
	 * @param $ql 删除条件
	 *        	如ql=order+by+created+desc 按照创建时间来排序(降序)
	 */
	public function batchDeleteUser($limit = "300", $ql = '') {
		$url = $this->url . "users?limit=" . $limit;
		if (! empty ( $ql )) {
			$url = $this->url . "users?ql=" . $ql . "&limit=" . $limit;
		}
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = 'DELETE' );
	}
	
	/**
	 * 给一个用户添加一个好友
	 *
	 * @param
	 *        	$owner_username
	 * @param
	 *        	$friend_username
	 */
	public function addFriend($owner_username, $friend_username) {
		$url = $this->url . "users/" . $owner_username . "/contacts/users/" . $friend_username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header );
        return $result;
	}
	/**
	 * 删除好友
	 *
	 * @param
	 *        	$owner_username
	 * @param
	 *        	$friend_username
	 */
	public function deleteFriend($owner_username, $friend_username) {
		$url = $this->url . "users/" . $owner_username . "/contacts/users/" . $friend_username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "DELETE" );
        return $result;
	}
	/**
	 * 查看用户的好友
	 *
	 * @param
	 *        	$owner_username
	 */
	public function showFriend($owner_username) {
		$url = $this->url . "users/" . $owner_username . "/contacts/users/";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
        return $result;
	}
	// +----------------------------------------------------------------------
	// | 聊天相关的方法
	// +----------------------------------------------------------------------
	/**
	 * 查看用户是否在线
	 *
	 * @param
	 *        	$username
	 */
	public function isOnline($username) {
		$url = $this->url . "users/" . $username . "/status";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}
	/**
	 * 发送消息
	 *
	 * @param string $from_user
	 *        	发送方用户名
	 * @param array $username
	 *        	array('1','2')
	 * @param string $target_type
	 *        	默认为：users 描述：给一个或者多个用户(users)或者群组发送消息(chatgroups)
	 * @param string $content        	
	 * @param array $ext
	 *        	自定义参数
	 */
	function yy_hxSend($from_user = "admin", $username, $content, $target_type = "users", $ext) {
		$option ['target_type'] = $target_type;
		$option ['target'] = $username;
		$params ['type'] = "txt";
		$params ['msg'] = $content;
		$option ['msg'] = $params;
		$option ['from'] = $from_user;
		$option ['ext'] = $ext;
		$url = $this->url . "messages";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, $option, $header );
		return $result;
	}
	/**
	 * 获取app中所有的群组
	 */
	public function getGroupsList() {
		$url = $this->url . "chatgroups";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}
	/**
	 * 创建群组
	 *
	 * @param $option['groupname'] //群组名称,
	 *        	此属性为必须的
	 * @param $option['desc'] //群组描述,
	 *        	此属性为必须的
	 * @param $option['public'] //是否是公开群,
	 *        	此属性为必须的 true or false
	 * @param $option['approval'] //加入公开群是否需要批准,
	 *        	没有这个属性的话默认是true, 此属性为可选的
	 * @param $option['owner'] //群组的管理员,
	 *        	此属性为必须的
	 * @param $option['members'] //群组成员,此属性为可选的        	
	 */
	public function createGroups($option) {
		$url = $this->url . "chatgroups";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, $option, $header );
		return $result;
	}
	/**
	 * 获取群组详情
	 *
	 * @param
	 *        	$group_id
	 */
	public function getGroupsDetails($group_id) {
		$url = $this->url . "chatgroups/" . $group_id;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}

    /**
     * 修改群信息
     * @param $group_id
     * @param $group_name
     * @param null $desc
     * @param null $maxusers
     * @return array|mixed|string
     */
    public function ModifyGroupInfo($group_id,$group_name,$desc = null,$maxusers=null)
    {
        $options = [
            'groupname'=>$group_name,
        ];
        if(!empty($desc))
        {
            $options['description'] = str_replace(' ','+',$desc);//$desc
        }
        if(!empty($maxusers) && intval($maxusers) > 0)
        {
            $options['maxusers'] = intval($maxusers);
        }
        $url = $this->url . "chatgroups/" . $group_id;
        $access_token = $this->getToken ();
        $header [] = 'Authorization: Bearer ' . $access_token;
        $result = $this->postCurl ( $url, $options, $header, $type = "GET" );
        return $result;
    }

	/**
	 * 删除群组
	 *
	 * @param
	 *        	$group_id
	 */
	public function deleteGroups($group_id) {
		$url = $this->url . "chatgroups/" . $group_id;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "DELETE" );
		return $result;
	}
	/**
	 * 获取群组成员
	 *
	 * @param
	 *        	$group_id
	 */
	public function groupsUser($group_id) {
		$url = $this->url . "chatgroups/" . $group_id . "/users";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}


	/**
	 * 群组添加成员
	 *
	 * @param
	 *        	$group_id
	 * @param
	 *        	$username
	 */
	public function addGroupsUser($group_id, $username) {
		$url = $this->url . "chatgroups/" . $group_id . "/users/" . $username;
        //\Yii::getLogger()->log('add_group_member_url:'.$url,Logger::LEVEL_ERROR);
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "POST" );
		return $result;
	}
    //批量加入
    ///{org_name}/{app_name}/chatgroups/{group_id}/users/{username}
    /**
     * 群组添加成员
     *
     * @param
     *        	$group_id
     * @param array
     *        	$usernameList 用户名数组  {“usernames”:[“username1”,”username2”]}’
     */
    public function batchAddGroupsUser($group_id, $usernameList) {
        $url = $this->url . "chatgroups/" . $group_id . "/users";
        $access_token = $this->getToken ();
        $header [] = 'Authorization: Bearer ' . $access_token;
        $result = $this->postCurl ( $url, $usernameList, $header);
        return $result;
    }

	/**
	 * 群组删除成员
	 *
	 * @param
	 *        	$group_id
	 * @param
	 *        	$username
	 */
	public function delGroupsUser($group_id, $username) {
		$url = $this->url . "chatgroups/" . $group_id . "/users/" . $username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "DELETE" );
		return $result;
	}
	/**
	 * 聊天消息记录
	 *
	 * @param $ql 查询条件如：$ql
	 *        	= "select+*+where+from='" . $uid . "'+or+to='". $uid ."'+order+by+timestamp+desc&limit=" . $limit . $cursor;
	 *        	默认为order by timestamp desc
	 * @param $cursor 分页参数
	 *        	默认为空
	 * @param $limit 条数
	 *        	默认20
	 */
	public function chatRecord($ql = '', $cursor = '', $limit = 20) {
		$ql = ! empty ( $ql ) ? "ql=" . $ql : "order+by+timestamp+desc";
		$cursor = ! empty ( $cursor ) ? "&cursor=" . $cursor : '';
		$url = $this->url . "chatmessages?" . $ql . "&limit=" . $limit . $cursor;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET " );
		return $result;
	}
	/**
	 * 获取Token
	 */
	public function getToken() {
		$option ['grant_type'] = "client_credentials";
		$option ['client_id'] = $this->client_id;
		$option ['client_secret'] = $this->client_secret;
		$url = $this->url . "token";
		$fp = @fopen ( "easemob.txt", 'r' );
		if ($fp) {
			$arr = unserialize ( fgets ( $fp ) );
			if ($arr ['expires_in'] < time ()) {
				$result = $this->postCurl ( $url, $option, $head = 0 );
				$result = json_decode($result,true);
                //var_dump($result);
				$result ['expires_in'] = $result ['expires_in'] + time ();
				@fwrite ( $fp, serialize ( $result ) );
                fclose ( $fp );
				return $result ['access_token'];
			}
            fclose ( $fp );
			return $arr ['access_token'];
		}
		$result = $this->postCurl ( $url, $option, $head = 0 );
		$result = json_decode($result,true);
        var_dump($result);
		$result ['expires_in'] = $result ['expires_in'] + time ();
		$fp = @fopen ( "easemob.txt", 'w' );
		@fwrite ( $fp, serialize ( $result ) );
        fclose ( $fp );
		return $result ['access_token'];
	}
	
	/**
	 * CURL Post
	 */
	private function postCurl($url, $option, $header = 0, $type = 'POST') {
		$curl = curl_init (); // 启动一个CURL会话
		curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE ); // 对认证证书来源的检查
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE ); // 从证书中检查SSL加密算法是否存在
		curl_setopt ( $curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
		if (! empty ( $option )) {
			$options = json_encode ( $option );
			curl_setopt ( $curl, CURLOPT_POSTFIELDS, $options ); // Post提交的数据包
		}
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 ); // 设置超时限制防止死循环
        if($header)
        {
            curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
        }
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
		curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, $type );
        $status = 503;
        $times = 0;
        while($status == 503)
        {
            $result = curl_exec ( $curl ); // 执行操作
            $res = json_decode($result,true);
            if(empty($res) || !is_array($res))
            {
                $res = [];
            }
            $res ['status'] = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
            if($res['status'] == 503)
            {
                if($times > 2)
                {
                    return $res;
                }
                \Yii::getLogger()->log('环信请求次数太频繁，2秒后执行',Logger::LEVEL_ERROR);
                sleep(2);//请求太多，稍后处理
                $times ++;
                continue;
            }
            $status = 200;
        }

		curl_close ( $curl ); // 关闭CURL会话
		return json_encode($res);
	}
}
