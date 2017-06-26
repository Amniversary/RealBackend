<?php
/* @var $this yii\web\View */
?>
<h1>fuck/test</h1>

<p>
    You may change the content of this page by modifying
    the file <code><?= __FILE__; ?></code>.
</p>
<?php
echo '<label>Check Issue Date</label>';
echo \kartik\date\DatePicker::widget([
    'type'=>\kartik\date\DatePicker::TYPE_RANGE|\kartik\date\DatePicker::TYPE_INPUT,
    'language' => 'zh-CN',
    'name' => 'check_issue_date',
    'name2' => 'check_issue_date',
    'value' => date('Y-m-d'),
    'value2'=>date('Y-m-d'),
    'separator'=>false,
    'convertFormat'=>true,
    'options' => ['placeholder' => 'Select issue date ...'],
    'pluginOptions' => [
        'format' => 'yyyy-MM-dd',
        'todayHighlight' => true,
        'autoclose'=>true
    ],
    'pickerButton'=>false,
    'layout'=>'{input1}到{input1}'
]);