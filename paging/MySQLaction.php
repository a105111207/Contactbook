<?php
    header("Content-type: text/html; charset=UTF-8");
    session_start();
    //連接資料庫
    $dbconnect = @new mysqli("localhost", "contactbook", "123", "contactbook");
    if($dbconnect->connect_errno != 0){
        printf($dbconnect->connect_errno, $dbconnect->connect_error);
        if (mysqli_connect_errno() == 1045) {
        echo "<script>alert('權限錯誤，請重新檢查或是恰客服人員'); location.href = '../index.html';</script>";
        }
        elseif (mysqli_connect_errno() == 2002) {
            echo "<script>alert('資料庫IP錯誤，請重新檢查或是恰客服人員'); location.href = '../index.html';</script>";
        }
        exit();
    }
    //設定資料庫編碼格式
    $sql = "set names utf8mb4"; 
    $result = $dbconnect->query($sql);
    if($result !== TRUE){       //如果無法設定，顯示警告視窗並跳回index.html
        echo "<script>alert('無法設定字碼格式'); location.href = '../index.html';</script>";
        exit();
    }

    function IDA($identity){
        if($identity=="teacher"){
            return $id="./Contact_Teacher.php";
        }
        elseif($identity=="student"){
            return $id="./Contact_Student.php";
        }
        else{
            return $id="./Contact_Parent.php";
        }
    }

    //依據身分判斷跳轉網頁
    function ID($identity){
        $id=IDA($identity);
        printf("<script>location.href = '%s';</script>", $id); //跳轉至下一頁
    }
    //釋放查詢到的資料
    function freesql($sql){
        if(isset($sql) && is_resource($sql)){
            mysqli_free_result($sql);
        }
    }
    //老師網頁列出學生名單
    function classlist($dbconnect, $account){
        $sql = "select * from student where tid='$account'";
		$seldata = $dbconnect->query($sql);
		if(!empty($seldata) && ($seldata->num_rows) > 0){
            if(isset($_SESSION["classlist"]) && ($_SESSION["classlist"]!=NULL)) {
                unset($_SESSION["classlist"]);
            }
			while(($row=$seldata->fetch_array(MYSQLI_ASSOC)) != NULL){
                $id=$row["id"];
                $name=$row["name"];
                $classlist.="<option value='$id'>$name</option>";
            }
            $_SESSION["classlist"]=$classlist;
		}
		freesql($sql);
    }
    //查詢資料並存放session，檢查是否有值，有值的話就清空
    function sessiondb($dbconnect, $sql, $rowdata){
        $seldata = $dbconnect->query($sql);
        if(!empty($seldata) && ($seldata->num_rows) > 0){
            if(isset($_SESSION["{$rowdata}"]) && ($_SESSION["{$rowdata}"]!=NULL)) {
                unset($_SESSION["{$rowdata}"]);
            }
            while(($row=$seldata->fetch_array(MYSQLI_ASSOC)) != NULL){
                $_SESSION["{$rowdata}"]=$row["{$rowdata}"];
            }
        }
        else {
            $_SESSION["{$rowdata}"]="";
        }
        freesql($sql);
    }
        
    if(isset($_POST["loging"]) && ($_POST["loging"]=="登入")){
        if(isset($_POST["account"]) && ($_POST["account"]!=NULL)){
            if(isset($_POST["password"]) && ($_POST["password"]!=NULL)){
                $identity=trim($_POST["identity"]); //trim是去掉空格，取得上一頁的身分
                $account=trim($_POST["account"]);   //取得上一頁的帳號
                $password=trim($_POST["password"]); //取得上一頁的密碼
                //搜尋人員帳密
                $sql_account = "select * from $identity where id='$account' and password='$password'";
                $seldata = $dbconnect->query($sql_account);
                //如果搜尋表單中的有該筆資料
                if(!empty($seldata) && ($seldata->num_rows) > 0){
                    //檢查session中是否有值，有值的話就清空
                    if(isset($_SESSION["identity"]) && ($_SESSION["identity"]!=NULL)) {
                        unset($_SESSION["identity"]);
                    }
                    $_SESSION["identity"]=$identity;
                    if(isset($_SESSION["account"]) && ($_SESSION["account"]!=NULL)) {
                        unset($_SESSION["account"]);
                    }
                    $_SESSION["account"]=$account;
                    ID($_SESSION["identity"]);  //跳轉至不同身分的網頁
                    if($identity="teacher"){
                        classlist($dbconnect, $account);
                    }
                }
                else {
                    printf("<script>alert('請檢查帳號或密碼是否正確'); location.href = '../index.html';</script>");
                    exit();
                }
                if(isset($sql_account) && is_resource($sql_account)){
                    mysqli_free_result($sql_account);
                }
            }
            else{
                printf("<script>alert('請檢查帳號或密碼是否正確'); location.href = '../index.html';</script>");
                exit();
            }
        }
        else{
            printf("<script>alert('請檢查帳號或密碼是否正確'); location.href = '../index.html';</script>");
            exit();
        }
    }
    elseif(isset($_POST["search"]) && ($_POST["search"]=="查詢")){
        if(isset($_POST["date"]) && ($_POST["date"]!=NULL)){
            $date=$_POST["date"];
            //清空之前date的值，並存放現在的值
            if(isset($_SESSION["date"]) && ($_SESSION["date"]!=NULL)) {
                unset($_SESSION["date"]);
            }
            $_SESSION["date"]=$date;
            $account=$_SESSION["account"];
            //查詢回家作業、準備小考
            $sql = "select * from homework where date='$date'";
            sessiondb($dbconnect, $sql, "homework");
            sessiondb($dbconnect, $sql, "quiz");
            //依不同身分執行搜尋札記、筆記、家長及老師確認、評語
            if($_SESSION["identity"]==="teacher"){    //身分為老師
                if(isset($_POST["ChoiceStudent"]) && ($_POST["ChoiceStudent"]!=NULL)) {
                    //清空之前selstudent的值，並存放現在的值
                    $selstudent=$_POST["ChoiceStudent"];
                    if(isset($_SESSION["selstudent"]) && ($_SESSION["selstudent"]!=NULL)) {
                        unset($_SESSION["selstudent"]);
                    }
                    $_SESSION["selstudent"]=$selstudent;
                    //查詢選擇的學生姓名
                    $sql = "select * from student where id='$selstudent'";
                    $seldata = $dbconnect->query($sql);
                    if(isset($_SESSION["selname"]) && ($_SESSION["selname"]!=NULL)) {
                        unset($_SESSION["selname"]);
                    }
                    while(($row=$seldata->fetch_array(MYSQLI_ASSOC)) != NULL){
                        $_SESSION["selname"]=$row["name"];
                    }
                }
                //搜尋札記
                $sql = "select * from diary where date='$date' and sid='$selstudent'";
                sessiondb($dbconnect, $sql, "diary");
                //搜尋筆記
                $sql = "select * from note where date='$date' and sid='$selstudent'";
                sessiondb($dbconnect, $sql, "note");
                //搜尋家長及老師確認、評語
                $sql = "select * from bothcheck where date='$date' and sid='$selstudent'";
                sessiondb($dbconnect, $sql, "parcheck");
                sessiondb($dbconnect, $sql, "parcomment");
                sessiondb($dbconnect, $sql, "teacheck");
                sessiondb($dbconnect, $sql, "teacomment");
            }
            elseif($_SESSION["identity"]==="student"){  //身分為學生
                //搜尋札記
                $sql = "select * from diary where sid='$account' and date='$date'";
                sessiondb($dbconnect, $sql, "diary");
                //搜尋筆記
                $sql = "select * from note where sid='$account' and date='$date'";
                sessiondb($dbconnect, $sql, "note");
                //搜尋家長及老師確認、評語
                $sql = "select * from bothcheck where date='$date' and sid='$account'";
                sessiondb($dbconnect, $sql, "parcheck");
                sessiondb($dbconnect, $sql, "parcomment");
                sessiondb($dbconnect, $sql, "teacheck");
                sessiondb($dbconnect, $sql, "teacomment");
            }
            else{    //身分為家長
                //搜尋札記
                $sql = "select * from diary where sid=(select id from student where pid='$account') and date='$date'";
                sessiondb($dbconnect, $sql, "diary");
                //搜尋筆記
                $sql = "select * from note where sid=(select id from student where pid='$account') and date='$date'";
                sessiondb($dbconnect, $sql, "note");
                //搜尋家長及老師確認、評語
                $sql = "select * from bothcheck where date='$date' and sid=(select id from student where pid='$account')";
                sessiondb($dbconnect, $sql, "parcheck");
                sessiondb($dbconnect, $sql, "parcomment");
                sessiondb($dbconnect, $sql, "teacheck");
                sessiondb($dbconnect, $sql, "teacomment");
            }
            ID($_SESSION["identity"]);  //跳轉至不同身分的網頁
        }
        else{
            ID($_SESSION["identity"]);
            exit();
        }
    }
    elseif(isset($_POST["save"]) && ($_POST["save"]=="儲存")){
        if(isset($_POST["date"]) && ($_POST["date"]!=NULL)){
            $date=$_POST["date"];
            $homework=$_POST["homework"];
            $quiz=$_POST["quiz"];
            $diary=$_POST["diary"];
            $note=$_POST["note"];
            $account=$_SESSION["account"];
            if(isset($_POST["pc"])){$pc=1;}//家長確認紐
            else{$pc=0;}
            if(isset($_POST["parcheck"]) && ($_POST["parcheck"]!=NULL)){    //家長評語
                $parcheck=$_POST["parcheck"];
            }
            else{
                $parcheck="";
            }
            if(isset($_POST["tc"])){$tc=1;}//老師確認紐
            else{$tc=0;}
            if(isset($_POST["teacheck"]) && ($_POST["teacheck"]!=NULL)){    //家長評語
                $teacheck=$_POST["teacheck"];
            }
            else{
                $teacheck="";
            }
            if($_SESSION["identity"]==="teacher"){
                $selstudent=$_POST["ChoiceStudent"];
                echo $_POST["ChoiceStudent"];
                //查詢回家作業、準備小考，如果有值的話就更新，沒有就新增
                $sql = "select * from homework where date='$date'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"]!=NULL)){
                    $sql = "update homework set date='$date', homework='$homework', quiz='$quiz' where date='$date' and sid='$selstudent'";
                }
                else{
                    $sql = "insert into homework(date, homework, quiz) values ('$date','$homework','$quiz')";
                }
                $dbconnect->query($sql);
                freesql($sql);
                //搜尋家長及老師確認、評語，如果有值的話就更新，沒有就新增
                $sql = "select * from bothcheck where date='$date' and sid='$selstudent'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"]!=NULL)){
                    $sql = "update bothcheck set sid='$selstudent', date='$date', parcheck='$pc', parcomment='$parcheck', teacheck='$tc', teacomment='$teacheck' where date='$date' and sid='$selstudent'";
                }
                else{
                    $sql = "insert into bothcheck(sid, date, parcheck, parcomment, teacheck, teacomment) values ('$selstudent', '$date', '$pc', '$parcheck', '$tc', '$teacheck')";
                }
                $dbconnect->query($sql);
                freesql($sql);
            }
            elseif($_SESSION["identity"]==="student"){
                //搜尋札記，如果有值的話就更新，沒有就新增
                $sql = "select * from diary where sid='$account' and date='$date'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"]!=NULL)){
                    $sql = "update diary set sid='$account', date='$date', diary='$diary' where sid='$account' and date='$date'";
                }
                else{
                    $sql = "insert into diary(sid, date, diary) values ('$account', '$date', '$diary')";
                }
                $dbconnect->query($sql);
                freesql($sql);
                //搜尋筆記，如果有值的話就更新，沒有就新增
                $sql = "select * from note where sid='$account' and date='$date'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"]!=NULL)){
                    $sql = "update note set sid='$account', date='$date', note='$note' where sid='$account' and date='$date'";
                }
                else{
                    $sql = "insert into note(sid, date, note) values ('$account', '$date', '$note')";
                }
                $dbconnect->query($sql);
                freesql($sql);
            }
            else{    //身分為家長
                //搜尋家長及老師確認、評語，如果有值的話就更新，沒有就新增
                $sql = "select * from bothcheck where date='$date' and sid=(select id from student where pid='$account')";
                sessiondb($dbconnect, $sql, "sid");
                $sid=$_SESSION["sid"];
                if(isset($_SESSION["date"]) && ($_SESSION["date"]!=NULL)){
                    $sql = "update bothcheck set sid='$selstudent', date='$date', parcheck='$pc', parcomment='$parcheck', teacheck='$tc', teacomment='$teacheck' where date='$date'";
                }
                else{
                    $sql = "insert into bothcheck(sid, date, parcheck, parcomment, teacheck, teacomment) values ('$selstudent', '$date', '$pc', '$parcheck', '$tc', '$teacheck')";
                }
                $dbconnect->query($sql);
                freesql($sql);
            }
            $id=IDA($_SESSION["identity"]);
            printf("<script>alert('儲存成功！'); location.href = '%s';</script>", $id);  //跳轉至不同身分的網頁
        }
        else{
            $id=IDA($_SESSION["identity"]);
            printf("<script>alert('儲存失敗，請檢查！'); location.href = '%s';</script>", $id);  //跳轉至不同身分的網頁
            exit();
        }
    }
    elseif(isset($_POST["delete"]) && ($_POST["delete"]=="刪除")){
        if(isset($_POST["date"]) && ($_POST["date"]!=NULL)){
            $date=$_POST["date"];
            $homework=$_POST["homework"];
            $quiz=$_POST["quiz"];
            $diary=$_POST["diary"];
            $note=$_POST["note"];
            if(isset($_POST["pc"])){$pc=1;}//家長確認紐狀態
            else{$pc=0;}
            $parcheck=$_POST["parcheck"];
            if(isset($_POST["tc"])){$tc=1;}//老師確認紐狀態
            else{$tc=0;}
            $teacheck=$_POST["teacheck"];
            if($_SESSION["identity"]==="teacher"){
                $selstudent=$_POST["ChoiceStudent"];
                //查詢回家作業、準備小考，如果有值的話就刪除
                $sql = "select * from homework where date='$date'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"]!=NULL)){
                    $sql = "delete from homework where date='$date'";
                    $dbconnect->query($sql);
                    freesql($sql);
                }
                //搜尋家長及老師確認、評語，如果有值的話就刪除
                $sql = "select * from bothcheck where date='$date' and sid='$selstudent'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"]!=NULL)){
                    $sql = "delete bothcheck where date='$date' and sid='$selstudent'";
                    $dbconnect->query($sql);
                    freesql($sql);
                }
            }
            elseif($_SESSION["identity"]==="student"){
                //搜尋札記，如果有值的話就更新，沒有就刪除
                $sql = "select * from diary where sid='$account' and date='$date'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"]!=NULL)){
                    $sql = "delete diary where sid='$account' and date='$date'";
                    $dbconnect->query($sql);
                    freesql($sql);
                }
                
                //搜尋筆記，如果有值的話就更新，沒有就刪除
                $sql = "select * from note where sid='$account' and date='$date'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"]!=NULL)){
                    $sql = "delete bothcheck where sid='$account' and date='$date'";
                    $dbconnect->query($sql);
                    freesql($sql);
                }
            }
            else{    //身分為家長
                //搜尋家長及老師確認、評語，如果有值的話就更新，沒有就刪除
                $sql = "select * from bothcheck where date='$date' and sid=(select id from student where pid='$account')";
                sessiondb($dbconnect, $sql, "sid");
                $sid=$_SESSION["sid"];
                if(isset($_SESSION["date"]) && ($_SESSION["date"]!=NULL)){
                    $sql = "delete bothcheck where sid= '$sid' and date='$date'";
                    $dbconnect->query($sql);
                    freesql($sql);
                }
            }
            $id=IDA($_SESSION["identity"]);
            printf("<script>alert('刪除成功！'); location.href = '%s';</script>", $id);  //跳轉至不同身分的網頁
        }
        else{
            $id=IDA($_SESSION["identity"]);
            printf("<script>alert('刪除失敗，請檢查！'); location.href = '%s';</script>", $id);  //跳轉至不同身分的網頁
            exit();
        }
    }
    elseif(isset($_POST["register"]) && ($_POST["register"]=="註冊")){      //註冊帳號
        if(isset($_POST["idnumber"]) && ($_POST["idnumber"]!=NULL)){        //檢查必要欄位身分證號碼
            if(isset($_POST["id"]) && ($_POST["id"]!=NULL)){                //檢查必要欄位學號(ID)
                if(isset($_POST["name"]) && ($_POST["name"]!=NULL)){        //檢查必要欄位姓名
                    if(isset($_POST["password"]) && ($_POST["password"]!=NULL)){    //檢查必要欄位密碼
                        $identity=$_POST["identity"];
                        $idnumber=trim($_POST["idnumber"]);
                        $id=trim($_POST["id"]);
                        $name=trim($_POST["name"]);
                        if(isset($_POST["phone"]) && ($_POST["phone"]!=NULL)){
                            $phone=trim($_POST["phone"]);}
                            $phone=NULL;
                        $password=$_POST["password"];
                        if(isset($_POST["email"]) && ($_POST["email"]!=NULL)){
                            $email=trim($_POST["email"]);}
                            $email=NULL;
                        if($identity==="teacher"){
                            $sql = "select * from teacher where idnumber='$idnumber' or id='$id'";}
                        elseif($identity==="student"){
                            $sql = "select * from student where idnumber='$idnumber' or id='$id'";
                        }
                        else{
                            $sql = "select * from parent where idnumber='$idnumber' or id='$id'";
                        }
                        $seldata = $dbconnect->query($sql);
                        if(($seldata->num_rows) < 1){
                            if($identity==="teacher"){
                                $sql = "insert into teacher(idnumber, id, name, phone, password, email) values ('$idnumber','$id','$name','$phone','$password','$email')";
                            }
                            elseif($identity==="student"){
                                if(isset($_POST["tid"]) && ($_POST["tid"]!=NULL)){  //檢查是否有輸入老師的ID
                                    $tid=trim($_POST["tid"]);
                                    $sql = "select * from teacher where id='$tid'";
                                    $seldata=$dbconnect->query($sql);
                                    if(($seldata->num_rows) < 1){
                                        printf("<script>alert('註冊失敗，請檢查老師ID！'); location.href = './Register.php';</script>");  //跳轉回註冊頁
                                        exit();
                                    }
                                }
                                else{
                                    printf("<script>alert('註冊失敗，請輸入老師ID！'); location.href = './Register.php';</script>");  //跳轉回註冊頁
                                    exit();}
                                if(isset($_POST["pid"]) && ($_POST["pid"]!=NULL)){  //檢查是否有輸入家長的ID
                                    $pid=trim($_POST["pid"]);
                                    $sql = "select * from parent where id='$pid'";
                                    $seldata=$dbconnect->query($sql);
                                    if(($seldata->num_rows) < 1){
                                        printf("<script>alert('註冊失敗，請檢查家長ID！'); location.href = './Register.php';</script>");  //跳轉回註冊頁
                                        exit();
                                    }
                                }
                                else{
                                    printf("<script>alert('註冊失敗，請輸入家長ID！'); location.href = './Register.php';</script>");  //跳轉回註冊頁
                                    exit();}
                                $sql = "insert into student(idnumber, id, name, phone, password, email, tid, pid) values ('$idnumber','$id','$name','$phone','$password','$email','$tid','$pid')";
                            }
                            else{
                                $sql = "insert into parent(idnumber, id, name, phone, password, email) values ('$idnumber','$id','$name','$phone','$password','$email')";
                            }
                            $dbconnect->query($sql);
                        }
                        freesql($sql);
                        printf("<script>alert('註冊成功！'); location.href = './Register.php';</script>");  //跳轉回註冊頁
                    }
                    else{
                        printf("<script>alert('註冊失敗，請檢查密碼！'); location.href = './Register.php';</script>");  //跳轉回註冊頁
                        exit();}
                }
                else{
                    printf("<script>alert('註冊失敗，請檢查姓名！'); location.href = './Register.php';</script>");  //跳轉回註冊頁
                    exit();}
            }
            else{
                printf("<script>alert('註冊失敗，請檢查學號(ID)！'); location.href = './Register.php';</script>");  //跳轉回註冊頁
                exit();}
        }
        else{
            printf("<script>alert('註冊失敗，請檢查身分證號碼！'); location.href = './Register.php';</script>");  //跳轉回註冊頁
            exit();}
    }
    elseif(isset($_POST["findpassword"]) && ($_POST["findpassword"]=="確認")){      //找回密碼
        if(isset($_POST["id"]) && ($_POST["id"]!=NULL)){                //檢查必要欄位學號(ID)
            if(isset($_POST["email"]) && ($_POST["email"]!=NULL)){
                $id=$_POST["id"];
                $email=$_POST["email"];
                //檢查是帳號跟email是否匹配
                $sql = "select * from student, teacher, parent where (student.id='$id' and student.email='$email') or (teacher.id='$id' and teacher.email='$email') or (parent.id='$id' and parent.email='$email')";
                $seldata = $dbconnect->query($sql);
                if(!empty($seldata) && ($seldata->num_rows) > 0){
                    while(($row=$seldata->fetch_array(MYSQLI_ASSOC)) != NULL){
                        $password=$row["password"];
                    }
                    printf("<script>alert('您的密碼是：%s'); location.href = '../index.html';</script>", $password);  //跳轉回找回密碼頁
                }
                else{
                    printf("<script>alert('請檢查帳號或email'); location.href = './FindPassword.php';</script>", $password);  //跳轉回找回密碼頁
                    exit();
                }
                freesql($sql);
            }
            else{
                printf("<script>alert('請輸入e-mail'); location.href = './FindPassword.php';</script>");  //跳轉回找回密碼頁
                exit();}
        }
        else{
            printf("<script>alert('請輸入學號或ID'); location.href = './FindPassword.php';</script>");  //跳轉回找回密碼頁
            exit();}
    }
    mysqli_close($dbconnect);
?>