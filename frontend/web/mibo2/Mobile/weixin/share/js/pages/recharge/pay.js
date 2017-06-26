var bill_no = null; //账单编号
        //调用微信JS api 支付
        function jsApiCall(payParamStr,recharge_type)
        {
            //alert('jsApiCall');
            pay_param = $.parseJSON(payParamStr);
            WeixinJSBridge.invoke(
                "getBrandWCPayRequest",
                pay_param,
                function(res)
                {
                    //支付失败处理
                    if(res.err_msg != "get_brand_wcpay_request:ok" )
                    {
                        if(bill_no != null)
                        {
                            payFlag = false;
                            //取消支付
                            $.ajax({
                            type: "POST",
                            url: "/mbliving/cancelpay?token='.$token.'",
                            data: {
                                "bill_no":bill_no,
                                "goods_type":recharge_type
                            },
                            success: function(data)
                                {
                                    //alert(data);
                                    data = $.parseJSON(data);
                                    if(data.code == "0")
                                    {
                                        //location = '$("#back_url").attr("href")';
                                     }
                                     else
                                     {
                                        bill_no = null;
                                        popInfo("取消支付异常:" + data.msg,1);
                                     }
                                },
                            error: function (XMLHttpRequest, textStatus, errorThrown)
                                {
                                    bill_no = null;
                                    popInfo("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status,1);
                                 }
                            });
                        }

                    }
                    else
                    {
                        location = 'recharge_success.html?bill_no='+bill_no+'&type='+recharge_type;
                    }
                    //$("#issubmit").val("0");
                    //alert(res.err_code+res.err_desc+res.err_msg);
                }
            );
        }

		function callpay(payParamStr,recharge_type)
        {
            //alert('callplay');
            if (typeof WeixinJSBridge == undefined){
                if( document.addEventListener ){
                    document.addEventListener("WeixinJSBridgeReady", jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent("WeixinJSBridgeReady", jsApiCall);
                    document.attachEvent("onWeixinJSBridgeReady", jsApiCall);
                }
                payFlag = false;
            }else{
                jsApiCall(payParamStr,recharge_type);
            }
        }
		


        		
        function startWxPay(good_id,unique_no,recharge_type)
        {
	        //alert('pay商品id：'+good_id+',unique_no:'+unique_no);
            $.ajax({
            type: "POST",
            url: "/mbliving/otherpay",
            data: {
	    	  goods_id: good_id,
    		  unique_no: unique_no,
                       goods_type:recharge_type
    	    },
            success: function(data)
                {
                    //alert(data);
                    data = $.parseJSON(data);
                    if(data.code == "0")
                    {
                        bill_no = data.bill_no;
                         //发起支付
                         callpay(data.msg,recharge_type);
                     }
                     else
                     {
                         bill_no = null;
                         popInfo("支付异常:" + data.msg,1);
                         payFlag = false;
                     }
                },
            error: function (XMLHttpRequest, textStatus, errorThrown)
                {
                    bill_no = null;
                    popInfo("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status,1);
                    payFlag = false;
                 }
            });
        }
