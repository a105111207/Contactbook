<!DOCTYPE html>
<html lang="en">
<head>
 	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
 	<title>忘記密碼</title>
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link href="../css/bootstrap.css" rel="stylesheet">
  	<link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<body class="align-center" id="rebody">
	<h2>聯絡簿系統-找回密碼</h2>　
    <form method="post" action="./MySQLaction.php">
		<h3 id="h3">請輸入帳號:</h3>　
		<input class="retextarea" type="test" name="id" placeholder="*必要輸入欄位"><br />
		<h3 id="h3">請輸入信箱:　</h3>
		<input class="retextarea" type="test" name="email" placeholder="*必要輸入欄位"><br />
		<br />
		
		<input type="submit" name="findpassword" value="確認"><br />
		
	</form>
	<!-- jQuery (ncol-lg-3 col-md-3 col-sm-6 col-xs-12for Bootstrap's JavaScript plugins) -->
	<script src="../js/jquery-1.11.3.min.js"></script>

	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="../js/bootstrap.js"></script>
</body>
</html>