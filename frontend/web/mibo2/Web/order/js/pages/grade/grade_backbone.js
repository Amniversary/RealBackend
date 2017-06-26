window.onload= function(){
    var level = 1;
    var pct = 1;
    var color1;
    var color2;
    var levelNunmer;
    var M = Backbone.Model.extend({});
    var C = Backbone.Collection.extend({
        model:M,
        url:"/mbliving/userliving"
    });

    var Ep = new C();
    var V = Backbone.View.extend({
        template:_.template($("#my-Level").html()),
        render:function() {
            Model = this.model;
            exp = Model.experience;
            level = Model.level_id;
            expA = Model.up_level;
            expUp = Model.differ;
            x = expA - expUp;
            //百分比
            pct = x / expA;
            pct = Math.round(pct * 100);
            //******************
            templates = this.template(Model);
            templates = templates.replace("@levelNunmer", levelNunmer);
            templates = templates.replace("@%", pct+"%");
            this.$el.html(templates);
            if (level > 80) {
                $(this.el).find(".prompt").addClass("none");
            }
            $(this.el).find(".inner").css("width", pct + "%");
            $(this.el).find(".counter").css("left", pct + "%");
            return this;
        }
    });

    var Vi = Backbone.View.extend({
        el:$("body"),
        initialize:function() {
            this.listenTo(Ep, "add", this.addOne);
            Ep.fetch({
                method:"get",
                data:{
                    user_id:user_id_1,
                    time:time_1,
                    unique_no:unique_no_1,
                    sign:sign_1,
                    rand_str:rand_str_1
                },
                success:function() {
                     //  app_id_1  1171658345  是密播特别版
                    if(app_id_1 == 1171658345)
                    {   
                        $('.wrap-top').css('background-color','#fc5176');
                        $('.experience-info li:last-child').css('background-color','#fc5176');
                        $('.experience-article .load-bar .inner').css('background','-webkit-gradient(linear,0 100%,35% 100%,from(#E05D89),to(#EF0650))');
                        $('.experience-article .load-bar .inner').css('background','linear-gradient(to right,#E05D89, #EF0650)');
                        $('.experience-article .load-bar').css('border','4px solid #D6A4B9');
                        $('.wrap').css('color','#fc5176');
                        $('.experience-article .counter img').attr('src','http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/grade_counter_1.png');
                    }
                }
            });
        },
        addOne:function(EXP) {
            var error = EXP.get("code");
            if (error == "0") {
                EXP1 = EXP.get("msg");
                view = new V({
                    model:EXP1
                });
                this.$(".wrap-top").append(view.render().el);
            } else {
                alert(EXP.get("msg"));
            }
        }
    });

    var mystr = this.location.href;
    var user_id_1 = lookup("user_id");
    var time_1 = lookup("time");
    var unique_no_1 = lookup("unique_no");
    var app_id_1 = lookup("app_id");
    var sign_1 = lookup("sign");
    var rand_str_1 = lookup("rand_str");
    var myLevel = new Vi();

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