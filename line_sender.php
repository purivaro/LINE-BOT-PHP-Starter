<!DOCTYPE html>
<html>
<head>
	<title>โปรแกรมส่งข้อความ Line bot</title>
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
					   <h3 class="panel-title">Puri Line SMS
						<small>ระบบส่งข้อความLine</small></h3>
					</div>
					<div class="panel-body">
						<form action="json_puri_push_msg.php" method="post" id="form_sender">
							<div class="form-group">
								<select id="sendto" name="sendto" class="form-control" required>
									<option value=''>เลือกผู้รับ</option>
									<!--<option value='U02a2cb394330d90571a21b09f2c230ea'>ลพ.ภูริ iPhone</option>
									<option value='Ua2bdf85b0466beeb8c8af8fbccfba5df'>ลพ.ภูริ Android</option>
									<option value='Ub1c272947e6de86751d7142334b88ca1'>เอ็กซ์</option>
									<option value='Uf13b465993502a1956fd25a3c65aa801'>ยุ้ย</option>
									<option value='U27067457ab265d39046bd089d4711d8e'>จี๊ด</option>-->
								</select>
							</div>
							<div class="inputs">
								<div class="form-group label-floating">
									<label for="text_send" class="control-label">พิมพ์ข้อความที่ต้องการส่ง</label>
									<input type="text" class="form-control" id="text_send" name="text_send">
								</div>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-raised btn-success">ส่ง</button>
								<button type="clear" class="btn btn-raised btn-default">clear</button>
							</div>           
						</form>
						<pre id='object'></pre>
						<ul id='list'></ul>						
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
	<script src="https://www.gstatic.com/firebasejs/3.7.0/firebase.js"></script>
	<script src="js/app.js"></script>
</body>
</html>