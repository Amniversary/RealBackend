<?php
namespace common\components\rongcloudsdk\methods;

use \Exception;

abstract class AbstractMessage
{

    protected $errorMessage;

    protected $errorCode = 0;

    // 返回结果正确的code
    const SUCCESS_CODE = 200;

    /**
     * 设置错误
     * @param string|Exception $message
     */
    public function setErrorMessage($message)
    {
        if ($message instanceof \Exception) {
            $this->errorCode    = $message->getCode();
            $this->errorMessage = $message->getMessage();
        } else {
            $this->errorMessage = $message;
        }
    }

    /**
     * 获取错误提示
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * 获取错误代码
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * 验证返回结果
     * @param string $result
     * @return false|array
     */
    public function valid($result)
    {
        if (empty($result)) {
            $this->errorMessage = 'curl错误返回为空';
            return false;
        }

        $result = json_decode($result, true);
        // 如果返回的CODE不等于200，则判断为错误
        if ($result['code'] != self::SUCCESS_CODE) {
            $this->errorCode = $result['code'];
            $this->errorMessage = isset($result['errorMessage'])
                ? $result['errorMessage'] : 'error message is empty';
            return false;
        }
        $this->errorCode = $result['code'];
        return $result;
    }
} 