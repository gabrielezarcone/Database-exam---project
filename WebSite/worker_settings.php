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
    session_start();
?>

<!DOCTYPE html5>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once("lib/header.php")?>
    <?php include_once("lib/title_worker.php"); ?>
    <?php include_once("lib/navbarWOR.php")?>
    <title><?php print($_SESSION[user])?> :: worker settings</title>
</head>
<body>

    <?php
    $query = "SELECT * FROM crowdsourcing.worker WHERE user_name=$1";
    $values = array(1=>$_SESSION[user]);
    $db = open_pg_connection();
    $res1 = pg_prepare($db, "worker", $query);
    $res1 = pg_execute($db, "worker", $values);
    $old_worker = pg_fetch_array($res1, 0);
    close_pg_connection($db);
    ?>

    <div uk-grid style="margin-top:2%;">
        <div class="uk-width-1-3"></div>
        <div class="uk-width-1-3">
            <h1 class="uk-heading-primary"><?php print($_SESSION[user])?></h1>

            <form action="#" method="POST">
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
                        $query = "UPDATE crowdsourcing.worker SET password=$2, name=$3, surname=$4 WHERE user_name=$1";
                        $values = array(1=>$worker[user], $worker[pass1], $worker[name], $worker[surname]);
                        $db = open_pg_connection();
                        $res = pg_prepare($db, "update_w", $query);
                        $res = pg_execute($db, "update_w", $values);
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
                        <input class="uk-input" type="text"  name="name_W"  value="<?php print($old_worker[name])?>">
                    </div>
                    <div class="uk-inline">
                        <input class="uk-input" type="text"  name="surname_W" value="<?php print($old_worker[surname])?>">
                    </div>
                </div>

                <div class="uk-margin">
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: bolt"></span>
                        <input class="uk-input" type="text"  name="user_name_W" value="<?php print($_SESSION[user])?>" readonly>
                            <div uk-drop="animation: uk-animation-slide-top-small; duration: 200; delay-hide:1">
                                <div class="uk-card uk-card-body uk-card-default uk-alert-danger" uk-alert>User Names are not editable</div>
                            </div>
                    </div>
                </div>

                <div class="uk-margin">
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: lock"></span>
                        <input class="uk-input" type="password"  name="password_W" value="<?php print($old_worker[password])?>">
                    </div>
                    <div class="uk-inline">
                        <input class="uk-input" type="password"  name="password2_W" value="<?php print($old_worker[password])?>">
                    </div>
                </div>
                <div class="uk-margin">
                    <button class="uk-button uk-button-default worker">Submit</button>
                </div>
            </form>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
</body>
</html>