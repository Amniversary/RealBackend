<ul class="user-item"><li class="user-item-detail">本次借款金额：<?=$data['cur_borrow_money']?>￥</li><li class="user-item-detail">借款期数：<?=$data['by_stages_count']?></li><li class="user-item-detail">每期还款金额：<?=$data['stage_money']?></li></ul>
<ul class="user-item"><li class="user-item-detail"><?=\yii\bootstrap\Html::a('借款协议',$data['protocal_url'],['target'=>'_blank'])?></li></ul>
