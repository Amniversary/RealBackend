<style>
    .ke-container-example1 {
        display: block;
        border: 1px solid #CCC;
        background-color: #FFF;
        overflow: hidden;
    }
    .ke-container-example1 .ke-toolbar {
        border-bottom: 1px solid #CCC;
        background-color: #FFF;
        padding: 2px 5px;
        overflow: hidden;
    }
</style>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use \pjkui\kindeditor\KindEditor;
echo KindEditor::widget([
    'clientOptions' => [
        //编辑区域大小
        'height' => '20px',
        'width' =>'20px',
        'uploadJson' => '/advertorial/kupload?action=uploadJson',
        //定制菜单
        'items' => [
            'source',  'preview', '|', 'justifyleft', 'justifycenter', 'justifyright',
             'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
            'italic', 'underline', 'removeformat', '|', 'image', 'multiimage',
            'hr', 'emoticons', 'baidumap', 'link'
        ],
    ]]);
?>




