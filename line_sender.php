<!DOCTYPE html>
<html>
<head>
    <title>โปรแกรมส่งข้อความ Line bot</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel='stylesheet'>
    <link href="node_modules/bootstrap/dist/css/bootstrap-theme.min.css" rel='stylesheet'>
    <link href="node_modules/bootstrap-material-design/dist/css/bootstrap-material-design.min.css" rel='stylesheet'>
    <link href="node_modules/bootstrap-material-design/dist/css/ripples.min.css" rel='stylesheet'>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="header">ระบบส่งข้อความ
                    <small>Puri line bot</small>
                </h1>
                <div class="form-group">
                  <select id="sendto" name="sendto" multiple class="form-control">
                    <option>ลพ.ภูริ iPhone</option>
                    <option>ลพ.ภูริ Android</option>                  
                    <option>ไก่</option>
                    <option>เอ็กซ์</option>
                  </select>
                </div>
                <div class="inputs">
                    <div class="form-group label-floating">
                        <label for="text_send" class="control-label">ข้อความที่ต้องการส่ง</label>
                        <input type="text" class="form-control" id="text_send" name="text_send">
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/boostrap.min.js"></script>
    <script src="node_modules/bootstrap-material-design/dist/js/material.min.js"></script>
    <script src="node_modules/bootstrap-material-design/dist/js/ripples.min.js"></script>
    <script>
    $(document).ready(function(){
        $.material.init();
    });
    </script>
</body>
</html>