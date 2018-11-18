<?php
    include_once("lib/function.php");
    session_start();

    unset($_SESSION['result_pw']);


    $_SESSION[campaign] = $_GET[campaign];
    if(isset($_POST[answer])){
        assign_task_to_worker($_SESSION[task], $_SESSION[user]);
        choose_answer($_SESSION[user], $_SESSION[task], $_POST[answer]);
        unset($_SESSION[task]);
        unset($_POST[answer]);
    }
?>

<!DOCTYPE html5>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once("lib/header.php")?>
    <?php include_once("lib/title_worker.php"); ?>
    <?php include_once("lib/navbarWOR.php")?>
    <title><?php print($_SESSION[user])?> :: worker </title>
</head>
<body>
    <div uk-grid style="margin-top:2%;">
        <div class="uk-width-1-4 scrollable-side ">
            <h2 style="color: var(--worker-color); text-align: center;">Campaigns</h2>
            <div class="uk-flex uk-flex-column">
                <?php $num_camp = show_campaigns_W($_SESSION[user]); ?>
            </div>
        </div>

        <div class="uk-width-1-4"></div>

        <div class="uk-width-1-2 card-container">
        <?php show_card_W($_SESSION[user], $_SESSION[campaign]) ?>
        
        </div>
        <div class="uk-width-1-4 card-container">
            <div class="uk-card uk-card-default uk-card-body ">
                <h2 style="color: var(--worker-color)">Worker info</h2>
                <ul class="uk-list">
                    <li><h4>User_Name: </h4><?php print($_SESSION[user]) ?></li>
                    <li><h4>Number of joined campaings: </h4> <?php print($num_camp); ?> </li>
                    <li><h4>Success rate: </h4>@@@@</li>
                </ul>
            </div>
            <div class="uk-card uk-card-default uk-card-body uk-margin-top">
                <h2 style="color: var(--worker-color)">Skills</h2>
                <ul class="uk-list">
                    <?php show_keyword_list_W($_SESSION[user]); ?>
                </ul>
            </div>
        </div>
    </div>
    
</body>
</html>