<?php
namespace frontend\business\RongCloud;


trait MessageTrait
{

    /**
     * @var \common\components\rongcloudsdk\methods\Message $messageManager
     */
    private $messageManager = null;

    /**
     * @return \common\components\rongcloudsdk\methods\Message
     */
    public function getMessageManager()
    {
        if (empty($this->messageManager)) {
            $this->messageManager = \Yii::$app->im->Message();
        }
        return $this->messageManager;
    }

    /**
     * @param string $content
     * @param array|null $extra
     * @param int $tag
     * @return string
     */
    public function filteContent($content, $extra, $tag, $user = null)
    {
        if (is_string($content)) {
            $content = [
                'content' => $content,
            ];
        }
        if (!empty($user)) {
            $content['user'] = $user;
        }
        $content['extra'] = empty($extra) ? '' : $extra;
        $content['type'] = $tag;
        return json_encode($content);
    }
} 