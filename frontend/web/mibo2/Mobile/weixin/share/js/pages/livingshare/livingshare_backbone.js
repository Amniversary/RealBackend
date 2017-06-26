window.onload = function(){
    //主播的模板
    var Anchor = Backbone.Model.extend({
        defaults:{
            rwPhoto_main_pic:"aaaa",
            state_flag:"",
            photo_pic:"",
            name_nick_name:"",
            sex_sex:"",
            grade_level_no:"",
            age_age:"",
            id_client_no:"",
            //label 主播的个性标签
            label_sign_name:"",
            floow_attention:"",
            //rtmp地址
            pull_rtmp_url:"",
            //hls地址
            pull_hls_url:""
        }
    });

    //其他主播的模板
    var OtherAnchor = Backbone.Model.extend({
        defaults:{
            image:""
        }
    });

    //推荐主播的集合
    var AnchorC = Backbone.Collection.extend({
        model:Anchor,
        url:"/mbliving/livingshare"
    });

    //其他主播的集合
    var OtherAnchorC = Backbone.Collection.extend({
        model:OtherAnchor,
        url:"/mbliving/livingshare"
    });

    var anchor = new AnchorC();

    var otherAnchor = new OtherAnchorC();

    //推荐主播
    var V1 = Backbone.View.extend({
        template:_.template($("#anchor-template").html()),
        render:function() {
            Model = this.model;
            if (Model.sex == "男") {
                Model.sex = "http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/man.png";
            } else if (Model.sex == "女") {
                Model.sex = "http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/female.png";
            }
            var grade;
            if (Model.level_no > 0 && Model.level_no < 13) {
                grade = "http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/grade1.png";
                $(".grade-rq").css("background-color", "red");
            }
            if (Model.level_no >= 13 && Model.level_no < 20) {
                grade = "http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/grade2.png";
            }
            if (Model.level_no >= 20 && Model.level_no < 25) {
                grade = "http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/grade3.png";
            }
            if (Model.level_no >= 25 && Model.level_no < 30) {
                grade = "http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/grade4.png";
            }
            if (Model.level_no >= 30 && Model.level_no < 40) {
                grade = "http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/grade5.png";
            }
            if (Model.level_no >= 40 && Model.level_no < 50) {
                grade = "http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/grade6.png";
            }
            if (Model.level_no >= 50 && Model.level_no < 80) {
                grade = "http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/grade7.png";
            }
            if (Model.level_no >= 80) {
                grade = "http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/grade8.png";
            }
            templates = this.template(Model);
            templates = templates.replace("@grade", grade);
            this.$el.html(templates);
            width = $(".top").width();

            window_height = $(window).height();
            $(this.el).find(".obj-pic").css("width", window_height + "px");
            $(this.el).find(".obj-pic").css("height", window_height + "px");
            $(this.el).find(".anchor").css("height", window_height + "px");
            $(this.el).find(".mask").css("width", width + "px");
            $(".enter-app").css("width", width + "px");
            $(".enter-app-mask").css("width", width + "px");

            window.onresize = function() {
                $(this.el).find(".obj-pic").css("width", window_height + "px");
                $(this.el).find(".obj-pic").css("height", window_height + "px");
                $(this.el).find(".anchor").css("height", window_height + "px");
                $(this.el).find(".mask").css("width", width + "px");
                $(".enter-app").css("width", width + "px");
                $(".enter-app-mask").css("width", width + "px");
            };
            if (Model.level_no > 0 && Model.level_no < 13) {
                $(this.el).find(".grade-rq").css("background-color", "#99cc33");
            }
            if (Model.level_no >= 13 && Model.level_no < 20) {
                $(this.el).find(".grade-rq").css("background-color", "#ff9900");
            }
            if (Model.level_no >= 20 && Model.level_no < 25) {
                $(this.el).find(".grade-rq").css("background-color", "#ffcc00");
            }
            if (Model.level_no >= 25 && Model.level_no < 30) {
                $(this.el).find(".grade-rq").css("background-color", "#cc6699");
            }
            if (Model.level_no >= 30 && Model.level_no < 40) {
                $(this.el).find(".grade-rq").css("background-color", "#3366cc");
                $(this.el).find(".grade-1").css("color", "#ffb62b");
            }
            if (Model.level_no >= 40 && Model.level_no < 50) {
                $(this.el).find(".grade-rq").css("background-color", "#009966");
                $(this.el).find(".grade-1").css("color", "#f28200");
            }
            if (Model.level_no >= 50 && Model.level_no < 80) {
                $(this.el).find(".grade-rq").css("background-color", "#990033");
                $(this.el).find(".grade-1").css("color", "#ad8000");
            }
            if (Model.level_no >= 80) {
                $(this.el).find(".grade-rq").css("background-color", "#2a2a2a");
                $(this.el).find(".grade-1").css("color", "#f5d96c");
            }
            if( Model.level_no  > 99)
            {
                $(this.el).find(".top-content .info .grade-rq").css("padding-right","8px");
            }
            return this;
            }
    });

    //其他主播
    var V2 = Backbone.View.extend({
        template:_.template($("#OtherAnchor-template").html()),
        render:function() {
            Model = {
                list:this.model
            };
            templates = this.template(Model);
            this.$el.html(templates);
            return this;
        }
    });

    var Vi = Backbone.View.extend({
        el:$("body"),
        initialize:function() {
            this.listenTo(anchor, "add", this.addOne);
            anchor.fetch({
                method:"post",
                data:{
                    rand_str:rand_str_1,
                    time:time_1,
                    unique_no:unique_no_1,
                    p_sign:p_sign_1,
                    living_id:living_id_1
                },
                success:function(model, data) {
                    console.log("___________"+data.living_info_one.client_id);

                     var user_id_2 = data.living_info_one.client_id;

                     //判断安卓和ios
                    var u = navigator.userAgent;
                    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
                    var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端

                    var link = "http://a.mlinks.cc/Aa1A?living_id="+living_id_1+"&user_id="+user_id_2;
                 
                    // if(isiOS)
                    // {
                    //     if(app_id_1 == 'com.mb.MBLiving')
                    //     {
                    //         link = "http://a.mlinks.cc/Aaib?living_id="+living_id_1+"&user_id="+user_id_2;
                    //     }
                    //     else if(app_id_1 == 'com.mb.mibotv')
                    //     {
                    //         link = "http://a.mlinks.cc/AaiI?living_id="+living_id_1+"&user_id="+user_id_2;
                    //     }
                    //     else if(app_id_1 == 'com.mibo.mibo')
                    //     {
                    //         link = "http://a.mlinks.cc/Aai0?living_id="+living_id_1+"&user_id="+user_id_2;
                    //     }
                    //     else{
                    //         link = "http://a.mlinks.cc/Aaib?living_id="+living_id_1+"&user_id="+user_id_2;
                    //     }
                    // }

                      //alert(link);
		      
		      console.log(link);

                    $('body').on('click','#btnOpenApps',function(){
                             window.location.href = "http://a.app.qq.com/o/simple.jsp?pkgname=com.mb.mibo";
                     })
                
                    new Mlink({
                        mlink : link,
                        button : document.querySelector("a#btnOpenApp"),
                        autoLaunchApp : false,
                    });
                    $(".loadgif").hide();
                }
            });
        },
        events:{
            "click .floow":"floowChange"
        },
        addOne:function(Anchor) {
            Anchor1 = Anchor.get("living_info_one");
            view1 = new V1({
                model:Anchor1
            });
            this.$(".top").append(view1.render().el);
        initLivingShow();
            OtherAnchor = Anchor.get("living_info_list");
            view2 = new V2({
                model:OtherAnchor
            });
            this.$(".oanchor").append(view2.render().el);
        },
        floowChange:function() {
            if ($(".floow").text() == "关注") {
                $(".floow").text("已关注");
                $(".floow").css("border", "2px solid #626161");
                $(".floow").css("color", "#626161");
            }
        }
    });

    var mystr = this.location.href;
    var rand_str_1 = lookup("rand_str");
    var time_1 = lookup("time");
    var unique_no_1 = lookup("unique_no");
    var p_sign_1 = lookup("p_sign");
    var living_id_1 = lookup("living_id");
    var user_id_1 = lookup("user_id");

    var app11 = new Vi();

    function lookup(x) {
        var index1 = mystr.indexOf(x);
        var index = x.length;
        var index_1 = mystr.indexOf("&", index1);
        if (index_1 == -1) {
            index_1 = mystr.length;
        }
        var index1s = mystr.substring(index1 + index + 1, index_1);
        return index1s;
    }
}