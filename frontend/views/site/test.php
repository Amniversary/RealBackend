<form method="post">
<?php
\common\assets\ArtDialogAsset::register($this);
use \pjkui\kindeditor\KindEditor;
echo KindEditor::widget([
    'name'=>'kingedit_test',
    'id'=>'kingedit_test',
    'clientOptions' =>
        [
            //editor size
            'height' => '500',
            //custom menu
            'items' => [
                'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste',
                'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
                'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
                'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
                'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
                'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'multiimage',
                'flash', 'media', 'insertfile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak',
                'anchor', 'link', 'unlink', '|', 'about'
            ]
        ]
    ]);
?>

<input type="text" id="test" value="111111">
<botton id="tbtest">测试</botton>&nbsp;<button id="close_modal">关闭</button>
    <input type="submit" value="提交">
<div id="editor_content">
    <?=$cnt?>
</div>
</form>
<?php
echo \miloschuman\highcharts\Highcharts::widget([
    'options' => [
        'title' => ['text' => 'Fruit Consumption'],
        'xAxis' => [
            'categories' => ['Apples', 'Bananas', 'Oranges']
        ],
        'yAxis' => [
            'title' => ['text' => 'Fruit eaten']
        ],
        'series' => [
            ['name' => 'Jane', 'data' => [2016, 2015, 2012]],
            ['name' => 'John', 'data' => [2013, 2011, 2014]],
            ['name' => 'Hone', 'data' => [2016,2018,2019]]
        ]
    ]
]);
//http://v.qq.com/page/r/0/d/r0173ucwh0d.html
$js='
var K= null;
$("#tbtest").click(function(){
   console.log($("#w0").html());
});
$("#close_modal").click(function(){
    window.parent.Close();
});
';
$this->registerJs($js);