var name_input = document.getElementById('name_input');
var user_id_div = document.getElementById('user_id')
var enter_button = document.getElementById('enter_button');
var divFlower = document.getElementsByClassName('flower');
var flower1 = document.getElementById('flower1');
var textFlower = document.getElementsByClassName('text');
var user_id = null;
var flower_number = null;
var payment = null;

function height() {
	for(var i = 0; i < divFlower.length; i++) {
		divFlower[i].style.height = 0.625 * divFlower[i].offsetWidth + 'px';
	}
}

function fontSize() {
	for(var i = 0; i < textFlower.length; i++) {
		textFlower[i].style.fontSize = Math.round(0.1 * flower1.offsetWidth) + 'px';
	}
}
height();
fontSize();

//输入蜜播ID
$('#enter_button').click(function() {
	if(!name_input.value) {
		alert('请输入蜜播ID');
	} else {
		$('#user').addClass('display_none');
		$('#user_sign').removeClass('display_none');
		user_id_div.innerHTML = name_input.value;
		user_id = user_id_div.innerHTML;
		
	}
});

//切换蜜播ID
$('#switch_button').click(function() {
	$('#user').removeClass('display_none');
	$('#user_sign').addClass('display_none');
	name_input.value = null;
	user_id_div.innerHTML = null;
	user_id = null;
});

//选择支付方式
$('#wechat').click(function() {
	$("#select_wechat").addClass('select_ing');
	$("#select_alipay").removeClass('select_ing');
	payment = 1;
	console.log(payment);
});

$('#alipay').click(function() {
	$("#select_wechat").removeClass('select_ing');
	$("#select_alipay").addClass('select_ing');
	payment = 2;
	console.log(payment);
});

//选择鲜花数量
$('.flower').click(function() {
	flower_number = $(this).attr('number');
	$('.flower').removeClass('flower_click');
	$(this).addClass('flower_click');
	console.log(flower_number);
});

//点击确认支付
$('#enter').click(function() {
	if(!user_id) {
		alert('请输入蜜播ID！')
	} else if(!flower_number) {
		alert('请选择鲜花数量！')
	} else if(!payment) {
		alert('请选择支付方式！')
	} else {
		
	}
})