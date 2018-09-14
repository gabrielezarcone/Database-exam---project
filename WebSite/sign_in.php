<?php
    $worker = array('user' =>$_POST[user_W],
                    'password' => $_POST[password_W]);

    $requester = array('user' =>$_POST[user_R],
                       'password' => $_POST[password_R]);

    include_once("lib/function.php");
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
                <li class="uk-width-1-2"><a class="worker" href="#">Worker</a></li>
                <li class="uk-width-1-2"><a class="requester" href="#">Requester</a></li>
            </ul>

            <ul class="uk-switcher uk-margin">

                <li id="worker">

                    <form action="#" method="POST">
                        <?php
                            $db = open_pg_connection();
                            $query = 'SELECT user_name, password from crowdsourcing.worker as w WHERE w.user_name=$1;';
                            $res = pg_prepare($db, "worker", $query);
                            $values = array($worker[user]);
                            $res = pg_execute($db, "worker", $values);
                            $result = pg_fetch_array($res);
                            close_pg_connection($db);
                            if(isset($result[user_name]) && $worker[password]==$result[password]){
                                //create session
                                print('<div class="uk-alert-success" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Hi '.$worker[user].', welcome back</p>
                                        </div>');
                            }
                            else if(isset($worker[user])){
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

                    <form action="#" method="POST">
                        <?php
                            $db = open_pg_connection();
                            $query = 'SELECT user_name, password from crowdsourcing.requester as r WHERE r.user_name=$1;';
                            $res = pg_prepare($db, "requester", $query);
                            $values = array($requester[user]);
                            $res = pg_execute($db, "requester", $values);
                            $result = pg_fetch_array($res);
                            close_pg_connection($db);
                            if(isset($result[user_name]) && $requester[password]==$result[password]){
                                //create session
                                print('<div class="uk-alert-success" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Hi '.$requester[user].', welcome back</p>
                                        </div>');
                            }
                            else if(isset($requester[user])){
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
                            <input class="uk-button uk-button-default requester" type="submit">
                        </div>
                    </form>
                </li>
                
            </ul>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
</body>
</html>