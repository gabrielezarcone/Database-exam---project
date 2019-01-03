<?php
    $worker = array('user' =>$_POST[user_name_W],
                    'pass1' => $_POST[password_W],
                    'pass2' => $_POST[password2_W],
                    'name' => $_POST[name_W],
                    'surname' => $_POST[surname_W]);

    $requester = array('user' =>$_POST[user_name_R],
                    'pass1' => $_POST[password_R],
                    'pass2' => $_POST[password2_R],
                    'name' => $_POST[name_R],
                    'surname' => $_POST[surname_R]);
    
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
            <h1 class="uk-heading-primary">Sign up</h1>
            <ul class="uk-subnav uk-subnav-pill" uk-switcher="animation: uk-animation-fade" uk-grid>
                <li class="uk-width-1-2 <?php print($active_worker); ?>"><a class="worker" href="#">Worker</a></li>
                <li class="uk-width-1-2 <?php print($active_req); ?>"><a class="requester" href="#">Requester</a></li>
            </ul>

            <ul class="uk-switcher uk-margin">
                <li id="worker">
                    <form action="?active=worker" method="POST">
                        <?php 
                            if(isset($_POST[password_W])&&$_POST[name_W]==""){
                                print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Empty name field</p>
                                        </div>');
                            }
                            else if(isset($_POST[password_W])&&$_POST[surname_W]==""){
                                print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Empty surname field</p>
                                        </div>');
                            }
                            else if($_POST[password_W]===$_POST[password2_W] && isset($_POST[password_W])){
                                $query = "INSERT INTO crowdsourcing.worker(user_name, password, name, surname) 
                                            VALUES($1,$2,$3,$4)";
                                $values = array(1=>$worker[user], $worker[pass1], $worker[name], $worker[surname]);
                                $db = open_pg_connection();
                                $res = pg_prepare($db, "worker", $query);
                                $res = pg_execute($db, "worker", $values);
                                if($res==false){
                                    if(strpos(pg_last_error($db), 'duplicate key value violates unique constraint "worker_pkey"') !== false){
                                        print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Sorry, worker '.$worker[user].' already exist</p>
                                        </div>');
                                    }
                                    else if(strpos(pg_last_error($db), 'check constraint "worker_password_check"') !== false){
                                        print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Empty password field</p>
                                        </div>');
                                    }
                                    else if(strpos(pg_last_error($db), 'check constraint "worker_user_name_check"') !== false){
                                        print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Empty user name field</p>
                                        </div>');
                                    }
                                    else{
                                        print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Sorry, some error occours</p>
                                        </div>');
                                    }
                                }
                                else{
                                    print('<div class="uk-alert-success" uk-alert>
                                                <a class="uk-alert-close" uk-close></a>
                                                <p>Hi '.$worker[name].', welcome to Oak_sourcing</p>
                                            </div>');
                                }
                                close_pg_connection($db);
                            }
                            else if(isset($_POST[password_W])){
                                print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>The second password doesn\'t match with the first one</p>
                                        </div>');
                            }
                        ?>
                        <div class="uk-margin">
                            <div class="uk-inline">
                                <span class="uk-form-icon" uk-icon="icon: user"></span>
                                <input class="uk-input" type="text"  name="name_W"  placeholder="Name">
                            </div>
                            <div class="uk-inline">
                                <input class="uk-input" type="text"  name="surname_W" placeholder="Surname">
                            </div>
                        </div>

                        <div class="uk-margin">
                            <div class="uk-inline">
                                <span class="uk-form-icon" uk-icon="icon: bolt"></span>
                                <input class="uk-input" type="text"  name="user_name_W" placeholder="Username">
                            </div>
                        </div>

                        <div class="uk-margin">
                            <div class="uk-inline">
                                <span class="uk-form-icon" uk-icon="icon: lock"></span>
                                <input class="uk-input" type="password"  name="password_W" placeholder="Password">
                            </div>
                            <div class="uk-inline">
                                <input class="uk-input" type="password"  name="password2_W" placeholder="Repeat your password">
                            </div>
                        </div>
                        <div class="uk-margin">
                            <button class="uk-button uk-button-default worker">Submit</button>
                        </div>
                    </form>
                </li>

                <li id="requester">
                    <form action="?active=requester" method="POST">
                        <?php 
                            if(isset($_POST[password_R])&&$_POST[name_R]==""){
                                print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Empty name field</p>
                                        </div>');
                            }
                            else if(isset($_POST[password_R])&&$_POST[surname_R]==""){
                                print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Empty surname field</p>
                                        </div>');
                            }
                            else if($_POST[password_R]===$_POST[password2_R] && isset($_POST[password_R])){
                                $query = "INSERT INTO crowdsourcing.requester(user_name, password, name, surname) 
                                            VALUES($1,$2,$3,$4)";
                                $values = array(1=>$requester[user], $requester[pass1], $requester[name], $requester[surname]);
                                $db = open_pg_connection();
                                $res = pg_prepare($db, "requester", $query);
                                $res = pg_execute($db, "requester", $values);
                                if($res==false){
                                    if(strpos(pg_last_error($db), 'duplicate key value violates unique constraint "requester_pkey"') !== false){
                                        print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Sorry, requester '.$requester[user].' already exist</p>
                                        </div>');
                                    }
                                    else if(strpos(pg_last_error($db), 'check constraint "requester_password_check"') !== false){
                                        print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Empty password field</p>
                                        </div>');
                                    }
                                    else if(strpos(pg_last_error($db), 'check constraint "requester_user_name_check"') !== false){
                                        print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Empty user name field</p>
                                        </div>');
                                    }
                                    else{
                                        print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Sorry, some error occours</p>
                                        </div>');
                                    }
                                }
                                else{
                                    print('<div class="uk-alert-success" uk-alert>
                                                <a class="uk-alert-close" uk-close></a>
                                                <p>Hi '.$requester[name].', welcome to Oak_sourcing</p>
                                            </div>');
                                }
                                close_pg_connection($db);
                            }
                            else if(isset($_POST[password_R])){
                                print('<div class="uk-alert-danger" uk-alert>
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>The second password doesn\'t match with the first one</p>
                                        </div>');
                            }
                            
                        ?>
                        <div class="uk-margin">
                            <div class="uk-inline">
                                <span class="uk-form-icon" uk-icon="icon: user"></span>
                                <input class="uk-input" type="text" name="name_R" placeholder="Name">
                            </div>
                            <div class="uk-inline">
                                <input class="uk-input" type="text" name="surname_R" placeholder="Surname">
                            </div>
                        </div>

                        <div class="uk-margin">
                            <div class="uk-inline">
                                <span class="uk-form-icon" uk-icon="icon: bolt"></span>
                                <input class="uk-input" type="text" name="user_name_R" placeholder="Username">
                            </div>
                        </div>

                        <div class="uk-margin">
                            <div class="uk-inline">
                                <span class="uk-form-icon" uk-icon="icon: lock"></span>
                                <input class="uk-input" type="password" name="password_R" placeholder="Password">
                            </div>
                            <div class="uk-inline">
                                <input class="uk-input" type="password" name="password2_R" placeholder="Repeat your password">
                            </div>
                        </div>
                        <div class="uk-margin">
                            <button class="uk-button uk-button-default requester">Submit</button>
                        </div>
                    </form>
                </li>
                
            </ul>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
</body>
</html>