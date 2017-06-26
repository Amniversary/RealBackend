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
        if($menus === false)
        {
            $menus = [];
        }
        else
        {
            $menus = json_decode($menus,true);
        }
/*        $innerMenu = [];
        $user_id = \Yii::$app->user->id;
        $menus = \backend\business\UserMenuUtil::GetUserMenu($user_id,0,$innerMenu);*/
        ?>
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' =>$menus,// $menus,
                    /*[
                    ['label' => '红包管理', 'icon' => 'fa fa-file-code-o', 'url' => ['redpacket/index']],
                    ['label' => '审核管理', 'icon' => 'fa  fa-check-square-o', 'url' => ['auditmanage/index']],
                    [
                        'label' => '愿望管理',
                        'icon' => 'fa fa-list',
                        'url' => '#',
                        'items' => [
                            ['label' => '进行中愿望', 'icon' => 'fa fa-circle-o', 'url' => ['wishmanage/index'],],
                            ['label' => '历史愿望', 'icon' => 'fa fa-circle-o', 'url' => ['wishmanage/indexhis'],],
                        ],
                    ],
                    ['label' => '推荐管理', 'icon' => 'fa fa-outdent', 'url' => ['wishrecommend/index']],
                    ['label' => '热词管理', 'icon' => 'fa fa-eye', 'url' => ['hotwords/index']],
                    ['label' => '举报管理', 'icon' => 'fa fa-phone', 'url' => ['reportmanage/index']],
                    ['label' => '轮播图管理', 'icon' => 'fa  fa-file-image-o', 'url' => ['carouselmanage/index']],
                    ['label' => '客户管理', 'icon' => 'fa fa-users', 'url' => ['clientmanage/index']],
                    ['label' => '评论管理', 'icon' => 'fa fa-commenting', 'url' => ['commentmanage/index']],
                    [
                        'label' => '财务管理',
                        'icon' => 'fa fa-money',
                        'url' => '#',
                        'items' => [
                            ['label' => '提现打款管理', 'icon' => 'fa fa-file-code-o', 'url' => ['getcash/index'],],
                            ['label' => '美愿基金借款管理', 'icon' => 'fa fa-file-code-o', 'url' => ['fundborrow/index'],],
                            ['label' => '美愿基金账单管理', 'icon' => 'fa fa-file-code-o', 'url' => ['mybill/index'],],
                        ],
                    ],
                    [
                        'label' => '人员管理',
                        'icon' => 'fa fa-user',
                        'url' => 'usermanage/index',
                    ],
                    [
                        'label' => '系统管理',
                        'icon' => 'fa fa-wrench',
                        'url' => '#',
                        'items' => [
                            ['label' => '签到打赏红包设置', 'icon' => 'fa fa-file-code-o', 'url' => ['system/redpacketset'],],
                            ['label' => '安卓版本管理', 'icon' => 'fa fa-dashboard', 'url' => ['updatemanage/updateandroid'],],
                            ['label' => '菜单管理', 'icon' => 'fa fa-dashboard', 'url' => ['system/menu'],],
                        ],
                    ],
                ]*/
            ]
        ) ?>

    </section>

</aside>
