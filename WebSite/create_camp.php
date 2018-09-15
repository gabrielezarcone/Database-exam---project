<?php
    include_once("lib/function.php");
    session_start();

    $campaign = array('user' =>$_SESSION[user],
                    'name' => $_POST[name],
                    'reg_start' => $_POST[reg_start],
                    'reg_end' => $_POST[reg_end],
                    'start' => $_POST[start],
                    'end' => $_POST[end]);


?>

<!DOCTYPE html5>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once("lib/header.php")?>
    <?php include_once("lib/title_requester.php"); ?>
    <?php include_once("lib/navbarREQ.php")?>
    <title><?php print($_SESSION[user])?> :: create </title>
</head>
<body>
    <div uk-grid style="margin-top:2%;">
        <div class="uk-width-1-3"></div>
        <div class="uk-width-1-3">
            <h1 class="uk-heading-primary">Create Campaign</h1>
            <form action="#" method="POST">
                <?php
                    create_campaign($campaign[name], $campaign[reg_start], $campaign[reg_end], $campaign[start], $campaign[end], $_SESSION[user]);
                ?>
                <div class="uk-margin">
                    <p>Campaign Name</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: bookmark"></span>
                        <input class="uk-input" type="text" name="name" placeholder="Name">
                    </div>
                </div>
                
                <div class="uk-margin">
                    <p>Start Registration Date</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: calendar"></span>
                        <input class="uk-input" type="date" name="reg_start">
                    </div>
                </div>
                <div class="uk-margin">
                    <p>End Registration Date</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: calendar"></span>
                        <input class="uk-input" type="date" name="reg_end">
                    </div>
                </div>

                <div class="uk-margin">
                    <p>Start Date</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: calendar"></span>
                        <input class="uk-input" type="date" name="start">
                    </div>
                </div>
                <div class="uk-margin">
                    <p>End Date</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: calendar"></span>
                        <input class="uk-input" type="date" name="end">
                    </div>
                </div>
                <div class="uk-margin">
                    <button class="uk-button uk-button-default requester">Create</button>
                </div>
            </form>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
    
</body>
</html>