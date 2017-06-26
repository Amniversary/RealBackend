<style type="text/css">
    * {
        font-family: "微软雅黑";
    }
    .form-inline > div {
        margin-left: 20px;
    }
    #throw_title,
    #throw_body {
        background-color: #fbfbfb;
        padding: 25px;
        border: 1px solid #ddd;
        border-radius: 9px;
    }
    #throw_body {
        margin-top: 20px;
        min-height: 350px;
    }
    #throw_main tr {
        height: 50px;
    }
    #throw_main td {
        vertical-align: middle;
    }
</style>
<div id="throw_title">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label for="pay_type">支付类型</label>
            <select class="form-control" id="pay_type" name="pay_type">
                <option value="3">支付宝</option>
                <option value="6">苹果</option>
            </select>
        </div>
        <div class="form-group">
            <label for="limit">处理数量</label>
            <input type="text" class="form-control" id="limit" name="limit" value="20">
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" id="handle" name="handle">自动处理
            </label>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-primary" id="start">开始</button>
        </div>
    </form>
</div>

<div id="throw_body">
    <table class="table table-striped">
        <thead>
            <tr>
                <th id="throw_total"></th>
                <th>订单号</th>
                <th>下单时间</th>
                <th>客户ID</th>
                <th>商品名称</th>
                <th>商品价格</th>
                <th>提示</th>
                <th>其它</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="throw_main"></tbody>
    </table>
</div>

<iframe id="iframe" width="0" height="0"></iframe>

<?php

$js = <<<EOF
    var msgHandle = false;
    var msgLimit  = 0;
    var msgUrl    = '/checkmoneygoods/pay_validate';
    var msgs = [
        '<span style="color: #cd0a0a">账单记录不存在</span>',
        '<span style="color: #080">已支付未收到鲜花</span>',
        '<span style="color: #cd0a0a">未知错误</span>',
        '<span style="color: #cd0a0a">账单记录不存在</span>',
        '<span style="color: #cd0a0a">账单交易失败</span>',
        '<span style="color: #cd0a0a">第三方订单号已存在</span>',
    ];

    $(document).ready(function() {
        $('#start').on('click', function() {
            $('#throw_main').empty();
            msgHandle = $('#handle').is(':checked');
            msgLimit  = $('#limit').val();
            var pay_type = $('#pay_type').val();
            $('#iframe').attr('src', msgUrl + '?limit=' + msgLimit + '&pay_type=' + pay_type);
        });
    });

    function longping(data) {
        var ul = $('#throw_main');
        var li = $('<tr>');
        var html = '<td>'+data.index+'</td>'
                 + '<td>'+data.pay_bill+'</td>'
                 + '<td>'+data.create_time+'</td>'
                 + '<td>'+data.user_id+'</td>'
                 + '<td>'+data.goods_name+'</td>'
                 + '<td>'+data.pay_money+'</td>'
                 + '<td>'+msgs[data.msg]+'</td>'
                 + '<td>'+data.other_msg+'</td>'
                 + '<td><a class="btn btn-default btn-sm pay-do"'
                 + ' data-loading-text="Loading..." autocomplete="off"'
                 + ' data-id="'+data.recharge_id + '"'
                 + ' data-pay_type="'+data.pay_type+'">校验</a></td>';
        li.html(html);
        ul.append(li);
        if (msgHandle) {
            li.find('.pay-do').trigger('click');
        }
    };

    function total(total) {
        var th = $('#throw_total');
        th.text('#共' + total + '条');
    };

    $(document).on('click', '.pay-do', function() {
        var t = $(this);
        var id = t.data('id');
        var pay_type = t.data('pay_type');
        var _btn = t.button('loading')
        $.post('/checkmoneygoods/check_recharge_recode', {pay_type: pay_type, recharge_id: id}, function(result) {
            _btn.button('reset');
            _btn.parent().html('<span>校验完成</span>　　' + result.msg);
        }, 'json');
    });
EOF;

$this->registerJs($js, \yii\web\View::POS_END);