<?php
    include_once("lib/function.php");
    session_start();

    if(isset($_GET[logout])){
        session_destroy();
        print('<meta http-equiv="refresh" content="0; url=index.php">');
    }

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

        <?php $stat=worker_stat($_SESSION[campaign], $_SESSION[user]);?>
        <div class="uk-width-1-4 card-container">
            <div class="uk-card uk-card-default uk-card-body ">
                <h2 style="color: var(--worker-color)">Worker info   <a href="worker_settings.php" class="uk-icon-button uk-margin-small-right" uk-icon="cog" style="background-color: var(--worker-color); color:black"></a></h2>
                <ul class="uk-list">
                    <li><h4>User_Name: </h4><?php print($_SESSION[user]) ?></li>
                    <li><h4>Number of joined campaings: </h4> <?php print($num_camp); ?> </li>
                    <li><h4>Task answered: </h4><?php print($stat[answered]);?></li>
                    <li><h4>Task answered correctly: </h4><?php print($stat[correct]); ?></li>
                    <li><h4>Success rate: </h4><?php    if($stat[answered]!=0){
                                                            print(($stat[correct]*100)/$stat[answered].'%');
                                                        }
                                                        else{
                                                            print('0%');
                                                        }; ?></li>
                </ul>
            </div>
            <div class="uk-card uk-card-default uk-card-body uk-margin-top">
                <h2 style="color: var(--worker-color)">In this campaing</h2>
                <ul class="uk-list">
                    <li><h4>Standing position: </h4><?php print($stat[position]); ?></li>
                    <li><h4>Task answered: </h4><?php print($stat[answered_camp]);?></li>
                    <li><h4>Task answered correctly: </h4><?php print($stat[correct_camp]); ?></li>
                    <li><h4>Success rate: </h4><?php    if($stat[answered_camp]!=0){
                                                            print(($stat[correct_camp]*100)/$stat[answered_camp].'%');
                                                        }
                                                        else{
                                                            print('0%');
                                                        }; ?></li>
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