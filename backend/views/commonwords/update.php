<style>
    .user-create
    {
        width: 600px;
    }
</style>
<div class="user-update">
    <?= $this->render('_form', [
        'model' => $model,
        'type' => $type,
    ]) ?>

</div>
