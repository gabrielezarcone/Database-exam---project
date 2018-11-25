<?php
    $worker = array('user' =>$_POST[user_W],
                    'password' => $_POST[password_W]);

    $requester = array('user' =>$_POST[user_R],
                       'password' => $_POST[password_R]);



    session_start();
    unset($_SESSION[user]);


    if(isset($_POST[user_W])){
        $_SESSION[worker]=$worker;
    }
    if (isset($_POST[user_R])) {
        $_SESSION[requester]=$requester;
    }

    

    if(isset($_SESSION[worker][user]) && $_SESSION[worker][password]==$_SESSION[result_pw] && $_SESSION[worker][password]!=""){
        $_SESSION['user'] = $_SESSION[worker][user];
        unset($_SESSION[worker]);
        print('<meta http-equiv="refresh" content="0.01; url=worker.php">');
    }
    if(isset($_SESSION[requester][user]) && $_SESSION[requester][password]==$_SESSION[result_pw] && $_SESSION[requester][password]!=""){
        $_SESSION[user] = $_SESSION[requester][user];
        unset($_SESSION[requester]);
        print('<meta http-equiv="refresh" content="0.01; url=requester.php">');
    }

    include_once("lib/function.php");

    if($_GET[active]=="worker"){
        $active_worker = "uk-active";
    }
    else if($_GET[active]=="requester"){
        $active_req = "uk-active";
    }
?>




<!DOCTYPE html5>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once("lib/header.php")?>
    <?php include_once("lib/title.php"); ?>
    <?php include_once("lib/navbar.php")?>
    <title>Sign in</title>
</head>
<body>
    <div uk-grid style="margin-top:2%;">
        <div class="uk-width-1-3"></div>
        <div class="uk-width-1-3">
            <h1 class="uk-heading-primary">Sign in</h1>
            <ul class="uk-subnav uk-subnav-pill" uk-switcher="animation: uk-animation-fade" uk-grid>
                <li class="uk-width-1-2 <?php print($active_worker); ?>"><a class="worker" href="#">Worker</a></li>
                <li class="uk-width-1-2 <?php print($active_req); ?>"><a class="requester" href="#">Requester</a></li>
            </ul>

            <ul class="uk-switcher uk-margin">

                <li id="worker">

                    <form action="?active=worker" method="POST">
                        <?php
                            $db = open_pg_connection();
                            $query = 'SELECT user_name, password from crowdsourcing.worker as w WHERE w.user_name=$1;';
                            $res = pg_prepare($db, "worker", $query);
                            $values = array($worker[user]);
                            $res = pg_execute($db, "worker", $values);
                            $result = pg_fetch_array($res);
                            close_pg_connection($db);
                            if(isset($result[user_name]) && $_SESSION[worker][password]==$result[password]){
                                print('<div class="uk-alert-success" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Hi '.$_SESSION[worker][user].', welcome back</p>
                                        </div>');
                                $_SESSION[result_pw]=$result[password];
                                print('<meta http-equiv="refresh" content="0.9">');
                            }
                            else if(isset($_SESSION[worker][user])){
                                print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Sorry, wrong username or password</p>
                                        </div>');
                            }
                        ?>
                        <div class="uk-margin">
                            <div class="uk-inline">
                                <span class="uk-form-icon" uk-icon="icon: user"></span>
                                <input class="uk-input" type="text" name="user_W">
                            </div>
                        </div>

                        <div class="uk-margin">
                            <div class="uk-inline">
                                <span class="uk-form-icon" uk-icon="icon: lock"></span>
                                <input class="uk-input" type="password" name="password_W">
                            </div>
                        </div>
                        <div class="uk-margin">
                            <button class="uk-button uk-button-default worker">LogIn</button>
                        </div>
                    </form>
                </li>

                <li id="requester">

                    <form action="?active=requester" method="POST">
                        <?php
                            $db = open_pg_connection();
                            $query = 'SELECT user_name, password, accepted from crowdsourcing.requester as r WHERE r.user_name=$1;';
                            $res = pg_prepare($db, "requester", $query);
                            $values = array($requester[user]);
                            $res = pg_execute($db, "requester", $values);
                            $result = pg_fetch_array($res);
                            close_pg_connection($db);
                            if(isset($result[user_name]) && $_SESSION[requester][password]==$result[password] && $result[accepted]==f){
                                print('<div class="uk-alert-warning" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Sorry '.$_SESSION[requester][user].', still not approved</p>
                                        </div>');
                            }
                            else if(isset($result[user_name]) && $_SESSION[requester][password]==$result[password]){
                                print('<div class="uk-alert-success" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Hi '.$_SESSION[requester][user].', welcome back</p>
                                        </div>');
                                $_SESSION[result_pw]=$result[password];
                                print('<meta http-equiv="refresh" content="0.9">');
                            }
                            else if(isset($_SESSION[requester][user])){
                                print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Sorry, wrong username or password</p>
                                        </div>');
                            }
                        ?>
                        <div class="uk-margin">
                            <div class="uk-inline">
                                <span class="uk-form-icon" uk-icon="icon: user"></span>
                                <input class="uk-input" type="text" name="user_R">
                            </div>
                        </div>

                        <div class="uk-margin">
                            <div class="uk-inline">
                                <span class="uk-form-icon" uk-icon="icon: lock"></span>
                                <input class="uk-input" type="password" name="password_R">
                            </div>
                        </div>
                        <div class="uk-margin">
                            <button class="uk-button uk-button-default requester">LogIn</button>
                        </div>
                    </form>
                </li>
                
            </ul>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
</body>
</html>