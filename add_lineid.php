<!DOCTYPE html>
<html>
<head>
    <title>โปรแกรมเพิ่ม Contact Line bot</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel='stylesheet'>
    <link href="node_modules/bootstrap-material-design/dist/css/bootstrap-material-design.min.css" rel='stylesheet'>
    <link href="node_modules/bootstrap-material-design/dist/css/ripples.min.css" rel='stylesheet'>
    <link href="node_modules/toastr/build/toastr.min.css" rel='stylesheet'>
</head>
<body>
    <div class="container">
        <div class="row" style='margin-top:20px;'>
            <div class="col-xs-4 col-xs-offset-4 col-sm-4 col-sm-offset-4 col-md-2  col-md-offset-5 text-center">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/LINE_logo.svg/1024px-LINE_logo.svg.png" class="img-responsive">
            </div>
        </div>
        <div class="row" style='margin-top:10px;'>
            <div class="col-sm-12">
                <div class="panel panel-success">
                    <div class="panel-heading">
                       <h3 class="panel-title">Puri Add Contact Line
                        <small>โปรแกรมเพิ่ม Contact</small></h3>
                    </div>
                    <div class="panel-body">
                        <form action="https://api.mlab.com/api/1/databases/puridb/collections/col_line_id?apiKey=JrLs9PiSVp8OfgZn_jbSdKCvO01BIbxx" method="post" id="form_sender">
                            <div class="inputs">
                                <div class="form-group label-floating">
                                    <label for="line_id" class="control-label">Line ID</label>
                                    <input type="text" class="form-control" id="line_id" name="line_id">
                                </div>
                            </div>
                            <div class="inputs">
                                <div class="form-group label-floating">
                                    <label for="nickname" class="control-label">ชื่อเล่น</label>
                                    <input type="text" class="form-control" id="nickname" name="nickname">
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-raised btn-success">ส่ง</button>
                                <button type="clear" class="btn btn-raised btn-default">clear</button>
                            </div>           
                        </form> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="node_modules/bootstrap-material-design/dist/js/material.min.js"></script>
    <script src="node_modules/bootstrap-material-design/dist/js/ripples.min.js"></script>
    <script src="node_modules/toastr/build/toastr.min.js"></script>
    <script>
    $(document).ready(function(){
        $.material.init();


        $("body").on("submit","#form_sender",function(e){
            e.preventDefault();
            var this_ = $(this);
            var url = this_.attr('action');
            var line_id = this_.find("#line_id").val();         
            var nickname = this_.find("#nickname").val();         
            var data = JSON.stringify({line_id:line_id,nickname:nickname});
            $.ajax({
                type: 'POST',
                url: url,
                data: data, 
                contentType: "application/json",
                dataType: 'json',
                success: function(response) { 
                    console.log(response); 
                }
            });

        });
    });
    </script>
</body>
</html>