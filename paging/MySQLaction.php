<?php
    header("Content-type: text/html; charset = UTF-8");
    session_start();

    //Connecting to the database
    $dbconnect = @new mysqli("localhost", "contactbook", "123", "contactbook");
    if($dbconnect->connect_errno != 0){
        printf($dbconnect->connect_errno, $dbconnect->connect_error);
        if (mysqli_connect_errno() == 1045) {
        echo "<script>alert('權限錯誤，請重新檢查或是恰客服人員'); location.href = '../index.html';</script>";
        } elseif (mysqli_connect_errno() == 2002) {
            echo "<script>alert('資料庫IP錯誤，請重新檢查或是恰客服人員'); location.href = '../index.html';</script>";
        }
        exit();
    }

    //Setting format of the database
    $sql = "set names utf8mb4";
    $result = $dbconnect->query($sql);
    if($result != TRUE) {       //If couldn't set, showing a error window and back to index.htmls
        echo "<script>alert('無法設定字碼格式'); location.href = '../index.html';</script>";
        exit();
    }

    function IDA($identity) {
        if($identity == "teacher"){
            return $id = "./Contact_Teacher.php";
        } elseif($identity == "student") {
            return $id = "./Contact_Student.php";
        } else {
            return $id = "./Contact_Parent.php";
        }
    }

    //Redirecting to web pages based on user identity
    function ID($identity) {
        $id = IDA($identity);
        printf("<script>location.href = '%s';</script>", $id); //跳轉至下一頁
    }
    //Releasing Querying data
    function freesql($sql) {
        if(isset($sql) && is_resource($sql)) {
            mysqli_free_result($sql);
        }
    }
    //Showing name list of student on teacher's web
    function classlist($dbconnect, $account) {
        $sql = "select * from student where tid = '$account'";
		$seldata = $dbconnect->query($sql);
		if(!empty($seldata) && ($seldata->num_rows) > 0) {
            if(isset($_SESSION["classlist"]) && ($_SESSION["classlist"] != NULL)) {
                unset($_SESSION["classlist"]);
            }

			while(($row = $seldata->fetch_array(MYSQLI_ASSOC)) != NULL) {
                $id = $row["id"];
                $name = $row["name"];
                $classlist .= "<option value='$id'>$name</option>";
            }
            $_SESSION["classlist"] = $classlist;
		}

		freesql($sql);

    }
    //Querying data and save to session
    //If there are values in $rowdata, clearing previous values of $rowdata
    function sessiondb($dbconnect, $sql, $rowdata) {
        $seldata = $dbconnect->query($sql);
        if(!empty($seldata) && ($seldata->num_rows) > 0){
            if(isset($_SESSION["{$rowdata}"]) && ($_SESSION["{$rowdata}"] != NULL)) {
                unset($_SESSION["{$rowdata}"]);
            }
            while(($row = $seldata->fetch_array(MYSQLI_ASSOC)) != NULL) {
                $_SESSION["{$rowdata}"] = $row["{$rowdata}"];
            }
        } else {
            $_SESSION["{$rowdata}"] = "";
        }

        freesql($sql);
    }
        
    if(isset($_POST["loging"]) && ($_POST["loging"] == "登入")) {
        if(isset($_POST["account"]) && ($_POST["account"] != NULL)) {
            if(isset($_POST["password"]) && ($_POST["password"] != NULL)) {
                $identity = trim($_POST["identity"]); //trim -> Delecting space. Getting forward page's identity
                $account = trim($_POST["account"]);   //Getting forward page's account
                $password = trim($_POST["password"]); //Getting forward page's password
                //Querying account and password
                $sql_account = "select * from $identity where id = '$account' and password = '$password'";
                $seldata = $dbconnect->query($sql_account);
                //If the data existing in the search form
                if(!empty($seldata) && ($seldata->num_rows) > 0) {
                    //Checking values in the session
                    //If there are, clear it
                    if(isset($_SESSION["identity"]) && ($_SESSION["identity"] != NULL)) {
                        unset($_SESSION["identity"]);
                    }
                    $_SESSION["identity"] = $identity;
                    if(isset($_SESSION["account"]) && ($_SESSION["account"] != NULL)) {
                        unset($_SESSION["account"]);
                    }
                    $_SESSION["account"] = $account;
                    ID($_SESSION["identity"]);  //Jumping to different identity pages
                    if($identity = "teacher"){
                        classlist($dbconnect, $account);
                    }

                } else {
                    printf("<script>alert('請檢查帳號或密碼是否正確'); location.href = '../index.html';</script>");
                    exit();
                }

                if(isset($sql_account) && is_resource($sql_account)) {
                    mysqli_free_result($sql_account);
                }

            } else {
                printf("<script>alert('請檢查帳號或密碼是否正確'); location.href = '../index.html';</script>");
                exit();
            }
        } else {
            printf("<script>alert('請檢查帳號或密碼是否正確'); location.href = '../index.html';</script>");
            exit();
        }

    } elseif(isset($_POST["search"]) && ($_POST["search"] == "查詢")) {
        if(isset($_POST["date"]) && ($_POST["date"] != NULL)) {
            $date = $_POST["date"];
            //Clearing the previous value of "date" and storing the current value
            if(isset($_SESSION["date"]) && ($_SESSION["date"] != NULL)) {
                unset($_SESSION["date"]);
            }
            $_SESSION["date"] = $date;

            $account = $_SESSION["account"];
            //Querying homework and preparing quizzes
            $sql = "select * from homework where date = '$date'";
            sessiondb($dbconnect, $sql, "homework");
            sessiondb($dbconnect, $sql, "quiz");

            //Based on different roles, search for diaries, notes, confirmations from parents and teachers, and comments
            if($_SESSION["identity"] === "teacher"){    //Teacher
                if(isset($_POST["ChoiceStudent"]) && ($_POST["ChoiceStudent"] != NULL)) {
                    //Clearing the previous value of "selstudent" and storing the current value
                    $selstudent = $_POST["ChoiceStudent"];
                    if(isset($_SESSION["selstudent"]) && ($_SESSION["selstudent"] != NULL)) {
                        unset($_SESSION["selstudent"]);
                    }
                    $_SESSION["selstudent"] = $selstudent;
                    //Querying the name of the selected student
                    $sql = "select * from student where id = '$selstudent'";
                    $seldata = $dbconnect->query($sql);
                    if(isset($_SESSION["selname"]) && ($_SESSION["selname"] != NULL)) {
                        unset($_SESSION["selname"]);
                    }
                    while(($row = $seldata->fetch_array(MYSQLI_ASSOC)) !=  NULL) {
                        $_SESSION["selname"] = $row["name"];
                    }
                }
                //Querying diary
                $sql = "select * from diary where date = '$date' and sid = '$selstudent'";
                sessiondb($dbconnect, $sql, "diary");
                //Querying note
                $sql = "select * from note where date = '$date' and sid = '$selstudent'";
                sessiondb($dbconnect, $sql, "note");
                //Querying confirmations from parents and teacher, and comments
                $sql = "select * from bothcheck where date = '$date' and sid = '$selstudent'";
                sessiondb($dbconnect, $sql, "parcheck");
                sessiondb($dbconnect, $sql, "parcomment");
                sessiondb($dbconnect, $sql, "teacheck");
                sessiondb($dbconnect, $sql, "teacomment");
            } elseif($_SESSION["identity"] === "student") {  //Student
                
                $sql = "select * from diary where sid = '$account' and date = '$date'";
                sessiondb($dbconnect, $sql, "diary");
                
                $sql = "select * from note where sid = '$account' and date = '$date'";
                sessiondb($dbconnect, $sql, "note");
                
                $sql = "select * from bothcheck where date = '$date' and sid = '$account'";
                sessiondb($dbconnect, $sql, "parcheck");
                sessiondb($dbconnect, $sql, "parcomment");
                sessiondb($dbconnect, $sql, "teacheck");
                sessiondb($dbconnect, $sql, "teacomment");
            } else {    //Parent
                
                $sql = "select * from diary where sid = (select id from student where pid = '$account') and date = '$date'";
                sessiondb($dbconnect, $sql, "diary");
                
                $sql = "select * from note where sid = (select id from student where pid = '$account') and date = '$date'";
                sessiondb($dbconnect, $sql, "note");
                
                $sql = "select * from bothcheck where date = '$date' and sid = (select id from student where pid = '$account')";
                sessiondb($dbconnect, $sql, "parcheck");
                sessiondb($dbconnect, $sql, "parcomment");
                sessiondb($dbconnect, $sql, "teacheck");
                sessiondb($dbconnect, $sql, "teacomment");
            }
            ID($_SESSION["identity"]);  //Jumping to different identity pages

        } else {
            ID($_SESSION["identity"]);
            exit();
        }
    } elseif(isset($_POST["save"]) && ($_POST["save"] == "儲存")) {
        if(isset($_POST["date"]) && ($_POST["date"] != NULL)) {
            $date = $_POST["date"];
            $homework = $_POST["homework"];
            $quiz = $_POST["quiz"];
            $diary = $_POST["diary"];
            $note = $_POST["note"];
            $account = $_SESSION["account"];

            if(isset($_POST["pc"])) { //Parent confirmation button
                $pc = 1;
            } else {
                $pc = 0;
            }

            if(isset($_POST["parcheck"]) && ($_POST["parcheck"] != NULL)) {    //Comment of parent
                $parcheck = $_POST["parcheck"];
            } else {
                $parcheck = "";
            }

            if(isset($_POST["tc"])) { //Teacher confirmation button
                $tc = 1;
            } else {
                $tc = 0;
            }

            if(isset($_POST["teacheck"]) && ($_POST["teacheck"] != NULL)) {    //Comment of teacher
                $teacheck = $_POST["teacheck"];
            } else {
                $teacheck = "";
            }

            if($_SESSION["identity"] === "teacher") {
                $selstudent = $_POST["ChoiceStudent"];
                echo $_POST["ChoiceStudent"];
                //Querying homework and preparing quizzes
                //If values exist, update them; if not, add new ones
                $sql = "select * from homework where date = '$date'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"] != NULL)) {
                    $sql = "update homework set date = '$date', homework = '$homework', quiz = '$quiz' where date = '$date' and sid = '$selstudent'";
                } else {
                    $sql = "insert into homework(date, homework, quiz) values ('$date','$homework','$quiz')";
                }
                $dbconnect->query($sql);
                freesql($sql);

                //Querying confirmations from parents and teachers as well as comments
                $sql = "select * from bothcheck where date = '$date' and sid = '$selstudent'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"] != NULL)) {
                    $sql = "update bothcheck set sid = '$selstudent', date = '$date', parcheck = '$pc', parcomment = '$parcheck', teacheck = '$tc', teacomment = '$teacheck' where date = '$date' and sid = '$selstudent'";
                } else {
                    $sql = "insert into bothcheck(sid, date, parcheck, parcomment, teacheck, teacomment) values ('$selstudent', '$date', '$pc', '$parcheck', '$tc', '$teacheck')";
                }
                $dbconnect->query($sql);
                freesql($sql);

            } elseif($_SESSION["identity"] === "student") {
                //Querying diary
                $sql = "select * from diary where sid = '$account' and date = '$date'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"] != NULL)) {
                    $sql = "update diary set sid = '$account', date = '$date', diary = '$diary' where sid = '$account' and date = '$date'";
                } else {
                    $sql = "insert into diary(sid, date, diary) values ('$account', '$date', '$diary')";
                }
                $dbconnect->query($sql);
                freesql($sql);

                //Querying note
                $sql = "select * from note where sid = '$account' and date = '$date'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"] != NULL)){
                    $sql = "update note set sid = '$account', date = '$date', note = '$note' where sid = '$account' and date = '$date'";
                }
                else{
                    $sql = "insert into note(sid, date, note) values ('$account', '$date', '$note')";
                }
                $dbconnect->query($sql);
                freesql($sql);

            } else {    //Parent
                //Querying confirmations from parents and teachers as well as comments
                $sql = "select * from bothcheck where date = '$date' and sid = (select id from student where pid = '$account')";
                sessiondb($dbconnect, $sql, "sid");
                $sid = $_SESSION["sid"];
                if(isset($_SESSION["date"]) && ($_SESSION["date"] != NULL)) {
                    $sql = "update bothcheck set sid = '$selstudent', date = '$date', parcheck = '$pc', parcomment = '$parcheck', teacheck = '$tc', teacomment = '$teacheck' where date = '$date'";
                } else {
                    $sql = "insert into bothcheck(sid, date, parcheck, parcomment, teacheck, teacomment) values ('$selstudent', '$date', '$pc', '$parcheck', '$tc', '$teacheck')";
                }
                $dbconnect->query($sql);
                freesql($sql);

            }
            $id = IDA($_SESSION["identity"]);
            printf("<script>alert('儲存成功！'); location.href = '%s';</script>", $id);  //back to forward page

        } else {
            $id = IDA($_SESSION["identity"]);
            printf("<script>alert('儲存失敗，請檢查！'); location.href = '%s';</script>", $id);  //back to forward page
            exit();
        }

    } elseif(isset($_POST["delete"]) && ($_POST["delete"] == "刪除")) {
        if(isset($_POST["date"]) && ($_POST["date"] != NULL)) {
            $date = $_POST["date"];
            $homework = $_POST["homework"];
            $quiz = $_POST["quiz"];
            $diary = $_POST["diary"];
            $note = $_POST["note"];

            if(isset($_POST["pc"])) { //Parent confirmation button
                $pc = 1;
            } else {
                $pc = 0;
            }

            $parcheck = $_POST["parcheck"];
            if(isset($_POST["tc"])) { //Teacher confirmation button
                $tc = 1;
            } else {
                $tc = 0;
            }

            $teacheck = $_POST["teacheck"];
            if($_SESSION["identity"] === "teacher") {
                $selstudent = $_POST["ChoiceStudent"];
                //Querying homework and preparing quizzes
                //If values exist, delete them
                $sql = "select * from homework where date = '$date'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"] != NULL)) {
                    $sql = "delete from homework where date = '$date'";
                    $dbconnect->query($sql);
                    freesql($sql);
                }
                //Querying parent and teacher's checking and comments
                //If values exist, delete them
                $sql = "select * from bothcheck where date = '$date' and sid = '$selstudent'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"] != NULL)) {
                    $sql = "delete bothcheck where date = '$date' and sid = '$selstudent'";
                    $dbconnect->query($sql);
                    freesql($sql);
                }
            }
            elseif($_SESSION["identity"] === "student"){
                //Querying diary
                //If values exist, delete them
                $sql = "select * from diary where sid = '$account' and date = '$date'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"] != NULL)) {
                    $sql = "delete diary where sid = '$account' and date = '$date'";
                    $dbconnect->query($sql);
                    freesql($sql);
                }
                
                //Querying diary
                //If values exist, delete them
                $sql = "select * from note where sid = '$account' and date = '$date'";
                sessiondb($dbconnect, $sql, "date");
                if(isset($_SESSION["date"]) && ($_SESSION["date"] != NULL)) {
                    $sql = "delete bothcheck where sid = '$account' and date = '$date'";
                    $dbconnect->query($sql);
                    freesql($sql);
                }

            } else {    //Parent
                //Querying parent and teacher's checking and comments
                //If values exist, update them; else delect
                $sql = "select * from bothcheck where date = '$date' and sid = (select id from student where pid = '$account')";
                sessiondb($dbconnect, $sql, "sid");
                $sid = $_SESSION["sid"];
                if(isset($_SESSION["date"]) && ($_SESSION["date"] != NULL)) {
                    $sql = "delete bothcheck where sid = '$sid' and date = '$date'";
                    $dbconnect->query($sql);
                    freesql($sql);
                }
            }
            $id = IDA($_SESSION["identity"]);
            printf("<script>alert('刪除成功！'); location.href = '%s';</script>", $id);  //Jumping to forward page
        } else {
            $id = IDA($_SESSION["identity"]);
            printf("<script>alert('刪除失敗，請檢查！'); location.href = '%s';</script>", $id);
            exit();
        }

    } elseif(isset($_POST["register"]) && ($_POST["register"] == "註冊")) {      //Signing up
        //Checking the required field for the ID number
        if(isset($_POST["idnumber"]) && ($_POST["idnumber"] != NULL)) {
            //Checking the required field for the student's ID
            if(isset($_POST["id"]) && ($_POST["id"] != NULL)) {
                //Checking the required field for the name
                if(isset($_POST["name"]) && ($_POST["name"] != NULL)) {
                    //Checking the required field for the password
                    if(isset($_POST["password"]) && ($_POST["password"] != NULL)) {
                        $identity = $_POST["identity"];
                        $idnumber = trim($_POST["idnumber"]);
                        $id = trim($_POST["id"]);
                        $name = trim($_POST["name"]);
                        $phone = NULL;
                        if(isset($_POST["phone"]) && ($_POST["phone"] != NULL)) {
                            $phone = trim($_POST["phone"]);
                        }
                        $password = $_POST["password"];
                        $email = NULL;
                        if(isset($_POST["email"]) && ($_POST["email"] != NULL)) {
                            $email = trim($_POST["email"]);
                        }

                        if($identity === "teacher") {
                            $sql = "select * from teacher where idnumber = '$idnumber' or id = '$id'";
                        } elseif($identity === "student") {
                            $sql = "select * from student where idnumber = '$idnumber' or id = '$id'";
                        } else {
                            $sql = "select * from parent where idnumber = '$idnumber' or id = '$id'";
                        }

                        $seldata = $dbconnect->query($sql);
                        if(($seldata->num_rows) < 1) {
                            if($identity === "teacher"){
                                $sql = "insert into teacher(idnumber, id, name, phone, password, email) values ('$idnumber','$id','$name','$phone','$password','$email')";
                            } elseif($identity === "student") {
                                if(isset($_POST["tid"]) && ($_POST["tid"] != NULL)) {  //Checking if teacher's ID is inputted
                                    $tid = trim($_POST["tid"]);
                                    $sql = "select * from teacher where id = '$tid'";
                                    $seldata = $dbconnect->query($sql);
                                    if(($seldata->num_rows) < 1) {
                                        printf("<script>alert('註冊失敗，請檢查老師ID！'); location.href = './Register.php';</script>");
                                        exit();
                                    }

                                } else {
                                    printf("<script>alert('註冊失敗，請輸入老師ID！'); location.href = './Register.php';</script>");
                                    exit();
                                }

                                if(isset($_POST["pid"]) && ($_POST["pid"] != NULL)) {  //Checking if parent's ID is inputted
                                    $pid = trim($_POST["pid"]);
                                    $sql = "select * from parent where id = '$pid'";
                                    $seldata = $dbconnect->query($sql);
                                    if(($seldata->num_rows) < 1) {
                                        printf("<script>alert('註冊失敗，請檢查家長ID！'); location.href = './Register.php';</script>");
                                        exit();
                                    }

                                } else {
                                    printf("<script>alert('註冊失敗，請輸入家長ID！'); location.href = './Register.php';</script>");
                                    exit();
                                }
                                $sql = "insert into student(idnumber, id, name, phone, password, email, tid, pid) values ('$idnumber','$id','$name','$phone','$password','$email','$tid','$pid')";
                            
                            } else {
                                $sql = "insert into parent(idnumber, id, name, phone, password, email) values ('$idnumber','$id','$name','$phone','$password','$email')";
                            }
                            
                            $dbconnect->query($sql);
                        }
                        freesql($sql);
                        printf("<script>alert('註冊成功！'); location.href = './Register.php';</script>");
                    }
                    else{
                        printf("<script>alert('註冊失敗，請檢查密碼！'); location.href = './Register.php';</script>");
                        exit();
                    }
                }
                else{
                    printf("<script>alert('註冊失敗，請檢查姓名！'); location.href = './Register.php';</script>");
                    exit();
                }
            }
            else{
                printf("<script>alert('註冊失敗，請檢查學號(ID)！'); location.href = './Register.php';</script>");
                exit();
            }
        }
        else{
            printf("<script>alert('註冊失敗，請檢查身分證號碼！'); location.href = './Register.php';</script>");
            exit();
        }

    } elseif(isset($_POST["findpassword"]) && ($_POST["findpassword"] == "確認")) {      //finding password
        //Checking the required field for the student's ID
        if(isset($_POST["id"]) && ($_POST["id"] != NULL)) {
            if(isset($_POST["email"]) && ($_POST["email"] != NULL)) {
                $id = $_POST["id"];
                $email = $_POST["email"];
                //Checking if the account and email match
                $sql = "select * from student, teacher, parent where (student.id = '$id' and student.email = '$email') or (teacher.id = '$id' and teacher.email = '$email') or (parent.id = '$id' and parent.email = '$email')";
                $seldata = $dbconnect->query($sql);
                if(!empty($seldata) && ($seldata->num_rows) > 0) {
                    while(($row = $seldata->fetch_array(MYSQLI_ASSOC)) != NULL) {
                        $password = $row["password"];
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