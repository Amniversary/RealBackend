$(function(){
	$('.recharge_list').click(function(){
		$(this).addClass('on');
		$(this).siblings().removeClass('on');
	});

	$('input[name=pay_way]').click(function(){
		if ( $(this).prop('checked') ) {
			$(this).parent('label').siblings().removeClass('on');
			$(this).parent('label').addClass('on');
		};
	});

	$('.btn_pay').click(function(){
		console.log($('input:radio[name="pay_way"]:checked').val())
	})
})