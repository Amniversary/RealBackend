<style>
    .backend-pic-input
    {
        margin-bottom: 10px;
    }
    .btn{
        color: #fff;
        background-color: #337ab7;
        border-color: #2e6da4;
        display: inline-block;
        padding: 6px 12px;
        margin-bottom: 0;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.42857143;
        text-align: center;
        white-space: nowrap;
        text-decoration: none;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-image: none;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    a.btn:hover,a.btn:visited{
        background-color: #3366b7;
        color: #fff;
    }
    .progress{
        margin-top:2px;
        width: 200px;
        height: 14px;
        margin-bottom: 10px;
        overflow: hidden;
        background-color: #f5f5f5;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
        box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
    }
    .progress-bar{
        background-color: rgb(92, 184, 92);
        background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.14902) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.14902) 50%, rgba(255, 255, 255, 0.14902) 75%, transparent 75%, transparent);
        background-size: 40px 40px;
        box-shadow: rgba(0, 0, 0, 0.14902) 0px -1px 0px 0px inset;
        box-sizing: border-box;
        color: rgb(255, 255, 255);
        display: block;
        float: left;
        font-size: 12px;
        height: 20px;
        line-height: 20px;
        text-align: center;
        transition-delay: 0s;
        transition-duration: 0.6s;
        transition-property: width;
        transition-timing-function: ease;
        width: 266.188px;
    }
</style>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'app_id')->textInput(['readonly'=>true,'style'=>'width:500px;'])->label('app标识') ?>

    <?= $form->field($model, 'module_id')->textInput(['readonly'=>false,'style'=>'width:500px;'])->label('模块id') ?>

    <?= $form->field($model, 'discribtion')->textInput(['readonly'=>false,'style'=>'width:500px;'])->label('描述')?>

    <?= $form->field($model, 'app_version_inner')->textInput(['style'=>'width:500px;'])->label('内部版本号') ?>

    <?= $form->field($model, 'link')->textInput(['style'=>'width:500px;'])->label('更新链接') ?>

    <h5 style="font-weight:bold;">您所选择的文件列表：</h5>
    <div id="ossfile">你的浏览器不支持flash,Silverlight或者HTML5！</div>
    <br/>
    <div id="container">
        <a id="selectfiles" href="javascript:void(0);" class='btn'>选择文件</a>
        <a id="postfiles" href="javascript:void(0);" class='btn'>开始上传</a>
    </div>
    <p id="console"></p>
    <p>&nbsp;</p>

    <?= $form->field($model, 'force_update')->dropDownList(['0'=>'不强制','1'=>'强制'],['style'=>'width:500px;'])->label('是否强制更新') ?>

    <?= $form->field($model, 'update_content')->textarea(['style'=>'width:500px;height:200px;'])->label('更新内容')?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['versionmanage/indexson','app_id'=>$model->app_id]), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJsFile('http://mbpic.mblive.cn/meibo-test/plupload.full.min.js');
$js = '
accessid = "";
accesskey = "";
host = "";
policyBase64 = "";
signature = "";
callbackbody = "";
filename = "";
key = "";
expire = 0;
g_object_name = "";
g_object_name_type = "";
now = timestamp = Date.parse(new Date()) / 1000;

function send_request()
{
    var xmlhttp = null;
    if (window.XMLHttpRequest)
    {
        xmlhttp=new XMLHttpRequest();
    }
    else if (window.ActiveXObject)
    {
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }

    if (xmlhttp!=null)
    {
        serverUrl = "../mypic/get";
        xmlhttp.open( "GET", serverUrl, false );
        xmlhttp.send( null );
        return xmlhttp.responseText
    }
    else
    {
        alert("Your browser does not support XMLHTTP.");
    }
}

function check_object_radio() {
    g_object_name_type = "local_name";
}

function get_signature()
{
    //可以判断当前expire是否超过了当前时间,如果超过了当前时间,就重新取一下.3s 做为缓冲
    now = timestamp = Date.parse(new Date()) / 1000;
    if (expire < now + 3)
    {
        body = send_request();
        var obj = eval ("(" + body + ")");
        host = obj["host"];
        policyBase64 = obj["policy"];
        accessid = obj["accessid"];
        signature = obj["signature"];
        expire = parseInt(obj["expire"]);
        callbackbody = obj["callback"] ;
        key = obj["dir"];
        return true;
    }
    return false;
}

function random_string(len) {
    len = len || 32;
    var chars = "ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678";
    var maxPos = chars.length;
    var pwd = "";
    for (i = 0; i < len; i++) {
        pwd += chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}

function get_suffix(filename) {
    pos = filename.lastIndexOf('.');
    suffix = "";
    if (pos != -1) {
        suffix = filename.substring(pos)
    }
    return suffix;
}

function calculate_object_name(filename)
{
    if (g_object_name_type == "local_name")
    {
        g_object_name += "${filename}"
    }
    else if (g_object_name_type == "random_name")
    {
        suffix = get_suffix(filename);
        g_object_name = key + random_string(10) + suffix
    }
    return "";
}

function get_uploaded_object_name(filename)
{
    if (g_object_name_type == "local_name")
    {
        tmp_name = g_object_name;
        tmp_name = tmp_name.replace("${filename}", filename);
        return tmp_name;
    }
    else if(g_object_name_type == "random_name")
    {
        return g_object_name;
    }
}

function set_upload_param(up, filename, ret)
{
    if (ret == false)
    {
        ret = get_signature()
    }
    g_object_name = key;
    if (filename != "") {
        suffix = get_suffix(filename);
        calculate_object_name(filename)
    }
    new_multipart_params = {
        "key" : g_object_name,
        "policy": policyBase64,
        "OSSAccessKeyId": accessid,
        "success_action_status" : "200", //让服务端返回200,不然，默认会返回204
        "callback" : callbackbody,
        "signature": signature
    };

    up.setOption({
        "url": host,
        "multipart_params": new_multipart_params
    });

    up.start();
}

var uploader = new plupload.Uploader({
	runtimes : "html5,flash,silverlight,html4",
	browse_button : "selectfiles",
	//container: document.getElementById("container"),
	//flash_swf_url : "lib/plupload-2.1.2/js/Moxie.swf",
	//silverlight_xap_url : "lib/plupload-2.1.2/js/Moxie.xap",
    url : "http://oss.aliyuncs.com",

    filters: {
    mime_types : [ //只允许上传apk文件
        //{ title : "Image files", extensions : "jpg,gif,png,bmp,ico" },
        { title : "Apk files", extensions : "apk" }
        ],
        max_file_size : "40mb", //最大只能上传10mb的文件
        prevent_duplicates : true //不允许选取重复文件
    },
    multi_selection : false,
	init: {
    PostInit: function() {
        document.getElementById("ossfile").innerHTML = "";
        document.getElementById("postfiles").onclick = function() {
            set_upload_param(uploader, "", false);
            return false;
        };
    },

    FilesAdded: function(up, files) {
        if(this.files.length >= 2)
        {
            this.splice(0,1);
        }
        plupload.each(files, function(file) {
            document.getElementById("ossfile").innerHTML= "";
            document.getElementById("ossfile").innerHTML += "<div id=" + file.id + ">"+ file.name +"<b></b>"
                +"<div class=\"progress\"><div class=\"progress-bar\" style=\"width: 0%\"></div></div></div>";
        });
    },

    BeforeUpload: function(up, file) {
        check_object_radio();
        set_upload_param(up, file.name, true);
    },

    UploadProgress: function(up, file) {
        var d = document.getElementById(file.id);
        d.getElementsByTagName("b")[0].innerHTML = "<span>" + file.percent + "%</span>";
        var prog = d.getElementsByTagName("div")[0];
        var progBar = prog.getElementsByTagName("div")[0]
            progBar.style.width= 2*file.percent+"px";
            progBar.setAttribute("aria-valuenow", file.percent);
        },

    FileUploaded: function(up, file, info) {
        if (info.status == 200)
        {
            document.getElementById("multiupdatecontent-link").value = "http://mbpic.mblive.cn/"+ get_uploaded_object_name(file.name);
        }
        else
        {
            document.getElementById("multiupdatecontent-link").value = info.response;
        }
    },

    Error: function(up, err) {
        if (err.code == -600) {
            document.getElementById("console").appendChild(document.createTextNode("\n选择的文件太大了,可以根据应用情况，在upload.js 设置一下上传的最大大小"));
        }
        else if (err.code == -601) {
            document.getElementById("console").appendChild(document.createTextNode("\n选择的文件后缀不对,可以根据应用情况，在upload.js进行设置可允许的上传文件类型"));
        }
        else if (err.code == -602) {
            document.getElementById("console").appendChild(document.createTextNode("\n这个文件已经上传过一遍了"));
        }
        else {
            document.getElementById("console").appendChild(document.createTextNode("\nError xml:" + err.response));
        }
    }
}
});

uploader.init();
';
$this->registerJs($js,\yii\web\View::POS_END);

