<?php

namespace common\components\rongcloudsdk\methods;

use \Exception;

class Wordfilter extends AbstractMessage
{

	private $SendRequest;
	
	public function __construct($SendRequest) {
       		$this->SendRequest = $SendRequest;
    }

    
    /**
	 * 添加敏感词方法（设置敏感词后，App 中用户不会收到含有敏感词的消息内容，默认最多设置 50 个敏感词。） 
	 * 
	 * @param  word:敏感词，最长不超过 32 个字符。（必传）
	 *
	 * @return false|array
	 **/
	public function add($word) {
    	try{
			if (empty($word))
				throw new Exception('Paramer "word" is required');
				
	
    		$params = array (
    		'word' => $word
    		);
    		
    		$ret = $this->SendRequest->curl('/wordfilter/add.json',$params,'urlencoded','im','POST');
            return $this->valid($ret);

        }catch (Exception $e) {
            $this->setErrorMessage($e);
        }
   }
    
    /**
	 * 查询敏感词列表方法 
	 * 
	 *
	 * @return false|array
	 **/
	public function getList() {
    	try{
	
    		$params = array (
    		);
    		
    		$ret = $this->SendRequest->curl('/wordfilter/list.json',$params,'urlencoded','im','POST');
    		if(empty($ret))
    			throw new Exception('bad request');
            return $this->valid($ret);

        }catch (Exception $e) {
            $this->setErrorMessage($e);
        }
   }
    
    /**
	 * 移除敏感词方法（从敏感词列表中，移除某一敏感词。） 
	 * 
	 * @param  word:敏感词，最长不超过 32 个字符。（必传）
	 *
	 * @return false|array
	 **/
	public function delete($word) {
    	try{
			if (empty($word))
				throw new Exception('Paramer "word" is required');
				
	
    		$params = array (
    		'word' => $word
    		);
    		
    		$ret = $this->SendRequest->curl('/wordfilter/delete.json',$params,'urlencoded','im','POST');
            return $this->valid($ret);

        }catch (Exception $e) {
            $this->setErrorMessage($e);
        }
   }
    
}
?>