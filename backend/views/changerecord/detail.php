<style>
    .user-create
    {
        width: 100%;
    }
</style>
<div class="user-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>