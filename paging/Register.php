<!doctype=html>
<html lang="en">
<head>
 	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
 	<title>聯絡簿註冊</title>
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link href="../css/bootstrap.css" rel="stylesheet">
  	<link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<body class="align-center" id="rebody">
	
    <form method="post" action="./MySQLaction.php">
		<h2>聯絡簿註冊</h2>
		<h3 id="h3"></h3>身分確認:  
		<input type="radio" class="" name="identity" value="teacher" checked="true">老師　　
		<input type="radio" class="" name="identity" value="student">學生　　
		<input type="radio" class="" name="identity" value="parent">家長　　
		
		<h3 id="h3"></h3>身分證號碼:　
        <input class="retextarea" type="test" name="idnumber" placeholder="*必要輸入欄位"><br />
        <h3 id="h3"></h3>學號(ID):　　　　
		<input class="retextarea" type="test" name="id" placeholder="*必要輸入欄位最多9碼"><br />
		<h3 id="h3"></h3>姓名:　　　　
		<input class="retextarea" type="test" name="name" placeholder="請輸入真實姓名"><br />
		<h3 id="h3"></h3>電話:　　　　
		<input class="retextarea" type="test" name="phone"><br />
		<h3 id="h3"></h3>信箱:　　　　
		<input class="retextarea" type="test" name="email"><br />
		<h3 id="h3"></h3>密碼:　　　　
		<input class="retextarea" type="test" name="password" placeholder="*必要輸入欄位"><br />
		<h3 id="h3"></h3>家長_ID:  
		<input class="retextarea" type="test" name="pid" placeholder="*僅學生在註冊時需要填寫"><br />
		<h3 id="h3"></h3>老師_ID:  
		<input class="retextarea" type="test" name="tid" placeholder="*僅學生在註冊時需要填寫"><br />
		<br />
		<input class="" type="submit" name="register" value="註冊"><br />
		
        
	</form>
	<!-- jQuery (ncol-lg-3 col-md-3 col-sm-6 col-xs-12for Bootstrap's JavaScript plugins) -->
	<script src="../js/jquery-1.11.3.min.js"></script>

	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="../js/bootstrap.js"></script>
</body>
</html>