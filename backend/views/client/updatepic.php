<style>
    .user-update
    {
        width: 600px;
    }
</style>
<div class="user-update">
    <?= $this->render('_form_pic', [
        'model' => $model,
        'params'=>$params
    ]) ?>

</div>
