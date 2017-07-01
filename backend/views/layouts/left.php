<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?=$pic?>" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= \Yii::$app->user->identity->username; ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <?php
        $key = 'user_menu_'.strval(\Yii::$app->user->id);
        $menus = \Yii::$app->cache->get($key);
        if($menus === false) {
            $menus = [];
        } else {
            $menus = json_decode($menus,true);
        }
        ?>
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' =>$menus,// $menus
            ]
        ) ?>

    </section>
</aside>
