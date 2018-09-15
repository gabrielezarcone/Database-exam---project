<?php
    include_once("lib/function.php");
    session_start();
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
                    createcampaign($_SESSION[user]);
                    if(isset($result[user_name]) && $worker[password]==$result[password]){
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
                    <button class="uk-button uk-button-default requester">LogIn</button>
                </div>
            </form>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
    
</body>
</html>