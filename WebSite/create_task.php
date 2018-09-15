<?php
    include_once("lib/function.php");
    session_start();

    $task = array('user' =>$_SESSION[user],
                    'description' => $_POST[description],
                    'title' => $_POST[title],
                    'n_workers' => $_POST[n_workers],
                    'threshold' => $_POST[threshold],
                    'campaign' => $_POST[campaign],
                    'pay_type' => $_POST[pay_type],
                    'pay_description' => $_POST[pay_description]);

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
            <h1 class="uk-heading-primary">Create Task</h1>
            <form action="#" method="POST">
                <?php
                    create_task($campaign[name], $campaign[reg_start], $campaign[reg_end], $campaign[start], $campaign[end], $_SESSION[user]);
                ?>
                <div class="uk-margin">
                    <p>Campaign</p>
                        <select class="uk-input uk-select" name="title">
                            <span class="uk-form-icon" uk-icon="icon: bookmark"></span>
                            <?php show_campaign_opt($_SESSION[user],$_SESSION['campaign']); ?>
                        </select>
                    
                </div>
                <div class="uk-margin">
                    <p>Task Title</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: bookmark"></span>
                        <input class="uk-input" type="text" name="title" placeholder="Title">
                    </div>
                </div>
                <div class="uk-margin">
                    <p>Description</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: bookmark"></span>
                        <input class="uk-input uk-textarea" type="textarea" name="description" placeholder="Description">
                    </div>
                </div>
                
                <div class="uk-margin">
                    <p>Number of worker</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: calendar"></span>
                        <input class="uk-input" type="number" name="n_worker">
                    </div>
                </div>
                <div class="uk-margin">
                    <p>Threshold</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: calendar"></span>
                        <input class="uk-input" type="number" name="threshold">
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