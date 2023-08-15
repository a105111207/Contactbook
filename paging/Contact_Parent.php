<!DOCTYPE html>
<html lang="en">
<head>
 	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
 	<title>生活聯絡簿</title>
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link href="../css/bootstrap.css" rel="stylesheet">
  	<link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<body class="body">
	<?php
		session_start();
		function sessioncod($name){
			if(isset($_SESSION["{$name}"]) && ($_SESSION["{$name}"]!=NULL)) {
                echo $_SESSION["{$name}"];
            }
		}
		function bothcheck($check){
			if(isset($_SESSION["{$check}"]) && ($_SESSION["{$check}"]!=NULL)) {
				if($_SESSION["{$check}"] === "1"){
					echo "checked";}
			}
		}
	?>
    <form method="post" action="./MySQLaction.php">
		<h1 id="h1">生活聯絡簿系統</h1>
		選擇日期:　<input type="date" class="input" name="date" value="<?php sessioncod("date"); ?>" min="" max="Year(Now)-Month(Now)-Day(Now)">
		<input class="" type="submit"  name="search" value="查詢"><br />
		
		<h3 id="h3">*今日作業: </h3>
        需繳交作業:　
        <textarea class="textarea" name="homework" row="30" cols="60" readonly><?php sessioncod("homework"); ?></textarea><br />
		需準備小考:　
        <textarea class="textarea" name="quiz" row="30" cols="60" readonly><?php sessioncod("quiz"); ?></textarea><br />
		<hr />
        
        <h3 id="h3">*學生-生活札記: </h3>
        札記內容:　<textarea class="textareatext" name="diary" row="30" cols="60" readonly><?php sessioncod("diary"); ?></textarea><br />
        <hr />
		
		上課筆記:　
		<textarea class="textareatext" name="note" row="30" cols="60" readonly><?php sessioncod("note"); ?></textarea><br />
        <hr />
		

		<h3 id="h3">*家長-簽核聯絡簿: </h3>
        <input class="" type="checkbox" name="pc" value="1" <?php bothcheck("parcheck"); ?>>家長確認　<br />
        家長評語:　<textarea class="textareatext" name="parcheck" row="30" cols="60" ><?php sessioncod("parcomment"); ?></textarea><br />
        <hr />
	
		<h3 id="h3">*老師-檢查聯絡簿: </h3>
        <input class="" type="checkbox" name="tc" value="1" disabled="true" <?php bothcheck("teacheck"); ?>>老師確認　<br />
        老師評語:　<textarea class="textareatext" name="teacheck" row="30" cols="60" readonly><?php sessioncod("teacomment"); ?></textarea><br />
        <hr />

        <input class="" type="submit" name="save" value="儲存">
		<input class="" type="submit" name="delete" value="刪除"><br />
        
	</form>
	<!-- jQuery (ncol-lg-3 col-md-3 col-sm-6 col-xs-12for Bootstrap's JavaScript plugins) -->
	<script src="../js/jquery-1.11.3.min.js"></script>

	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="../js/bootstrap.js"></script>
</body>
</html>