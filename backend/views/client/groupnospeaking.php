<div style="width: 500px;">
    <div class="panel panel-default">
        <div class="panel-heading"><h4>组群禁言查询</h4></div>
        <div class="panel-body">
            <form>
                <div class="form-group">
                    <label for="client_no">客户蜜播ID</label>
                    <input type="text" class="form-control" id="client_no">
                </div>
                <div class="form-group">
                    <label for="living_client_no">主播蜜播ID</label>
                    <input type="text" class="form-control" id="living_client_no">
                </div>
                <button type="button" class="btn btn-primary" id="search">查找</button>
            </form>
        </div>
    </div>
</div>

<div id="myModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">组群禁言</h4>
            </div>
            <div class="modal-body">
                <form id="modal_form" class="form-horizontal">
                    <div class="form-group">
                        <p class="col-sm-offset-3 text-warning">0 代表不禁言，4294967295 代表永久禁言</p>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="shutted_until">禁言时间</label>
                        <div class="col-sm-9">
                            <input type="text" id="shutted_until" class="form-control" name="shutted_until">
                        </div>
                    </div>
                    <input type="hidden" id="h_client_id" name="client_id" value="">
                    <input type="hidden" id="h_other_id" name="other_id" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="save">保存</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    window.onload = function() {
        $("#search").on("click", function() {
            $.getJSON('/client/group_nospeaking', {
                'method': 'query',
                'client_no': $('#client_no').val(),
                'living_client_no': $('#living_client_no').val()
            }).done(function(result) {
                if (result.status == 0) {
                    $('#h_client_id').val(result.client_id);
                    $('#h_other_id').val(result.other_id);
                    $('#shutted_until').val(result.shutted_until);
                    $('#myModal').modal('show');
                } else {
                    alert(result.msg);
                }
            });
        });
        $("#save").on("click", function() {
            $.getJSON('/client/group_nospeaking', {
                'method': 'save',
                'client_id': $('#h_client_id').val(),
                'other_id': $('#h_other_id').val(),
                'shutted_until': $('#shutted_until').val()
            }).done(function(result) {
                if (result.status == 0) {
                    $('#myModal').modal('hide');
                } else {
                    alert(result.msg);
                }
            });
        });
    };
</script>
