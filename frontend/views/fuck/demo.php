<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/11
 * Time: 下午2:15
 */
?>

<script src="http://libs.baidu.com/jquery/2.1.4/jquery.min.js"></script>
<input type="file" name="file" id="video-file">
<!--<audio src="http://7xld1x.com1.z0.glb.clouddn.com/1221.mp3" controls="controls"></audio>-->


<script>
    $(function() {
        $(document).on("change", '#video-file', function () {
            var fromData = new FormData();
            $.each($(this)[0].files, function(i, file) {
                console.log(file);
                fromData.append("file", file);
            });
            $.ajax({
                url: './demoa',
                type:"POST",
                data:fromData,
                cache: false,
                processData: false,
                contentType: false,
                success:function(data)
                {
                    console.log(data);
                    if(data.code == "0")
                    {
                        alert(1);
                    }
                    else
                    {
                        alert(data.msg);
                    }

                },
                error: function (XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                }
            })
        });
    })
</script>
<?php
