<style>
    .user-update
    {
        width: 600px;
    }
</style>
<div class="user-update">
    <?= $this->render('_form', [
        'model' => $model,
        'activity_type' => $activity_type
    ]) ?>

</div>
