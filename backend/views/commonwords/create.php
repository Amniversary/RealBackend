<style>
    .user-create
    {
        width: 600px;
    }
</style>

<div class="user-create">
    <?= $this->render('_form', [
        'model' => $model,
        'type' => $type
    ]) ?>
</div>
