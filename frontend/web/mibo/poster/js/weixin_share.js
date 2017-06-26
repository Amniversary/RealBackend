function weixin_share(){
	var share = {};
	$.ajax({
		url: '/mbliving/getwechatshare',
		type: 'post',
		data: {
			url: window.location.href
		},
		async: false,
		success: function(data){
			//alert(data);
			var data = data&&JSON.parse(data);
			share.sign = data.sign;
			share.content = data.content;
			share.link = data.link;
			share.pic = data.pic;
			share.title = data.title;
			
		}
	})
    wx.config({
        debug: false,
        appId: "wx19f6ec4aec39c380",
        timestamp: 1455695941,
        nonceStr: "meiyuanduo2jd2oDGFERETRE",
        signature: share.sign,
        jsApiList: [
	        "onMenuShareTimeline",
	        "onMenuShareAppMessage",
	        "onMenuShareQQ",
	        "onMenuShareWeibo",
	        "onMenuShareQZone",
	        "openLocation",
	        "getLocation"
	    ]
    });
    wx.ready(function(){
    	console.log("ready");
        //getLocation();
        //alert('ready');
        //alert(JSON.stringify(share));
        wx.onMenuShareTimeline({
            title: share.title, // 分享标题
            link: share.link, // 分享链接
            imgUrl: share.pic, // 分享图标
            success: function () {
                //alert("分享朋友圈ok");
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
                //alert("取消分享朋友圈ok");
            }
        });
        wx.onMenuShareAppMessage({
            title: share.title, // 分享标题
            desc: share.content, // 分享描述
            link: share.link, // 分享链接
            imgUrl: share.pic, // 分享图标
            type: "", // 分享类型,music、video或link，不填默认为link
            dataUrl: "", // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户确认分享后执行的回调函数
                //alert("tttttt");
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
                //alert("ffffffff");
            }
        });
        wx.onMenuShareQQ({
            title: share.title, // 分享标题
            desc: share.content, // 分享描述
            link: share.link, // 分享链接
            imgUrl: share.pic, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    });
    wx.error(function(res){
    	//alert(res.errMsg);
        artDialog.tips(res.errMsg);
        //config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
    });

}
weixin_share();