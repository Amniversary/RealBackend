<style>
    .user-create
    {
        width: 600px;
    }
</style>
<div class="user-create">
    <?= $this->render('_form_son', [
        'model' => $model,
        'family_id' => $model->family_id,
        'page' => $page
    ]) ?>

</div>