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
                <h1 class="header">Checkbox</h1>

                <h2>Default <small>inside a <code>.form-group</code></small></h2>
                <div class="form-group">
                <div class="checkbox">
                    <label>
                    <input type="checkbox"> Notifications
                    </label>
                </div>
                <p class="help-block">Notify me about updates to apps or games that I've downloaded</p>
                </div>
                <div class="form-group">
                <div class="checkbox">
                    <label>
                    <input type="checkbox" checked=""> Auto-updates
                    </label>
                </div>
                <p class="help-block">Auto-update apps over wifi only</p>
                </div>

                <h2>Horizontal form with column label variations</h2>
                <form class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2" for="ch1">Touch sounds</label>
                    <div class="col-sm-10">
                    <div class="checkbox">
                        <label>
                        <input id="ch1" type="checkbox" checked="">
                        </label>
                    </div>
                    <p class="help-block">This shows the generic label variant.</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="ch3">Vibrate on touch</label>
                    <div class="col-sm-10">
                    <div class="checkbox">
                        <label>
                        <input id="ch3" type="checkbox">
                        </label>
                    </div>
                    <p class="help-block">This shows the <code>control-label</code> variant.</p>
                    </div>
                </div>
                </form>

                <h2>Default <small>outside a <code>.form-group</code></small></h2>
                <div class="checkbox">
                <label>
                    <input type="checkbox"> Notifications
                </label>
                </div>
                <p class="help-block">Without a <code>.form-group</code>, <code>.help-block</code> always shows</p>
                <div class="checkbox">
                <label>
                    <input type="checkbox" checked=""> Auto-updates
                </label>
                </div>
                <p class="help-block">Without a <code>.form-group</code>, <code>.help-block</code> sizing is the same as the <code>label</code></p>            
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