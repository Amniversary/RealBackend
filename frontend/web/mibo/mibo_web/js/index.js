$(function () {
    //fullPage插件
    $('#fullPage').fullpage({
        'navigation': true,
        onLeave: function(index, nextIndex, direction) {
            var leavingSection = $(this);
            setTimeout(function() { dot( 1 , nextIndex ) },750);
            setTimeout(function() { wor( 1 , nextIndex ) },750);
        }
    });

    // 图片飘入
    function dot(i,nextIndex){
        if ( i <= $('.phone_show'+nextIndex+' .live').length ) {
            $('.phone_show'+nextIndex).animate({
                margin:'0px',
                opacity:'1'
            },1000);
            if ( nextIndex == 2 && i == 5 ) {
                $('.model2-a2').fadeOut(1000).css('z-index','-70');
                $('.model2-a3').fadeOut(1000).css('z-index','-70');
                $('.model2-a4').fadeOut(1000).css('z-index','-70');
            }
            if ( nextIndex == 3 && i > 3 ) {
                /*$('.model3-a4').fadeIn(100).animate({
                    left:'+=50px',
                    opacity:'1'
                },500);

                return;*/

                $('.model'+nextIndex+'-a'+i).fadeIn(100);
                $('.model'+nextIndex+'-a'+i).animate({
                    left:'+=50px',
                    opacity:'1'
                },500);

                ++i;
                return setTimeout(function() {
                    dot( i,nextIndex,stop)
                },500);
            }
            if ( nextIndex == 4 && i == 4 ) {
                $('.model4-a3').fadeOut(1000);
            }
            $('.model'+nextIndex+'-a'+i).fadeIn(1000);
            ++i;
            setTimeout(function() {
                dot(i,nextIndex) 
            },1000);
        }
    }
    //文字飘入
    function wor(i,nextIndex){
        $('.phone_introduce' +nextIndex+' .index_p'+i).fadeIn(500);
        $('.phone_introduce' +nextIndex+' .index_p'+i).animate({
            opacity:'1',
            left:'0'
        },500);
        ++i;
        setTimeout(function() {
            wor(i,nextIndex)
        },500);
    }

    // 首页第一屏banner切换
    var i = 0;
    setInterval(function() {
        if ( i < $(".bg_banner").length-1 ) {
            $('.bg_banner').each(function (index) {
                if(index == i ){
                    $(this).fadeOut(1000);
                    $(".bg_banner").eq(index+1).fadeIn(1000);
                }
            });
            i++
        }else{
            i = 0;
            $('.bg_banner').eq(2).fadeOut(1000);
            $(".bg_banner").eq(0).fadeIn(1000);
        }
    }, 3000);
});