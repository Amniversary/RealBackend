<ul class="user-item"><li class="user-item-detail">愿望标题：<?=$data['wish_name']?></li><li class="user-item-detail">发起人：<?=$data['publish_user_name']?></li><li class="user-item-detail">愿望类型：<?=$data['wish_type']?></li></ul>
<ul class="user-item"><li class="user-item-detail">愿望总额：<?=$data['wish_money']?>￥</li><li class="user-item-detail">已筹金额：<?=$data['finish_wish_money']?>￥</li><li class="user-item-detail">剩余金额：<?=$data['left_wish_money']?>￥</li></ul>
<ul class="user-item"><li class="user-item-detail">本次借款金额：<?=$data['cur_borrow_money']?>￥</li><li class="user-item-detail">借款期数：<?=$data['by_stages_count']?></li><li class="user-item-detail"><?=\yii\bootstrap\Html::a('借款协议',$data['protocal_url'],['target'=>'_blank'])?></li></ul>