<style>
    .user-update
    {
        width: 600px;
    }
</style>
<div class="user-update">
    <?= $this->render('_model', [
        'model' => $model,
        'client_no' => $client_no,
    ]) ?>

</div>