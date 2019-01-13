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
                    if(isset($campaign[name])&&isset($campaign[reg_start])&&isset($campaign[reg_end])&&isset($campaign[start])&&isset($campaign[end])){
                        create_campaign($campaign[name], $campaign[reg_start], $campaign[reg_end], $campaign[start], $campaign[end], $_SESSION[user]);
                        if($campaign[reg_start]<date('Y-m-d')){
                            print('<div class="uk-alert-danger" uk-alert>
                                                    <a class="uk-alert-close" uk-close></a>
                                                    <p>Campaign REGISTRATION START DATE cannot be before today</p>
                                                </div>');
                        }
                        else if($campaign[reg_end]<=$campaign[reg_start]){
                            print('<div class="uk-alert-danger" uk-alert>
                            <a class="uk-alert-close" uk-close></a>
                            <p>Registration cannot finish before it starts!</p>
                            </div>');
                        }
                        else if($campaign[reg_end]<=date('Y-m-d')){
                            print('<div class="uk-alert-danger" uk-alert>
                            <a class="uk-alert-close" uk-close></a>
                            <p>Campaign REGISTRATION END DATE cannot be before today</p>
                            </div>');
                        }
                        else if($campaign[start]<date('Y-m-d')){
                            print('<div class="uk-alert-danger" uk-alert>
                            <a class="uk-alert-close" uk-close></a>
                            <p>Campaign START DATE cannot be before today</p>
                            </div>');
                        }
                        else if($campaign[end]<=$campaign[start]){
                            print('<div class="uk-alert-danger" uk-alert>
                            <a class="uk-alert-close" uk-close></a>
                            <p>The campaign cannot finish before it starts!</p>
                            </div>');
                        }
                        else if($campaign[end]<=date('Y-m-d')){
                            print('<div class="uk-alert-danger" uk-alert>
                            <a class="uk-alert-close" uk-close></a>
                            <p>Campaign END DATE cannot be before today</p>
                            </div>');
                        }
                        else{
                            print('<div class="uk-alert-success" uk-alert>
                                                    <a class="uk-alert-close" uk-close></a>
                                                    <p>Campaign '.$campaign[name].' created</p>
                                                </div>');
                        }
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
                    <button class="uk-button uk-button-default requester">Create</button>
                </div>
            </form>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
    
</body>
</html>