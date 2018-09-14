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
    <?php include_once("lib/title_worker.php"); ?>
    <?php include_once("lib/navbar.php")?>
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
            <div class="uk-card uk-card-default uk-card-body uk-animation-scale-down uk-width-expand card-worker myCard">
                <h1 class="card" style="color: #ffffff;">Title</h1>
                <h2 class="card" style="color: white;">Lorem ipsum <a href="#">dolor</a> sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</h2>
            </div>
             <form class="uk-margin">
                <ul class="uk-list">
                    <li><h2><input class="uk-radio" type="radio" name="radio1" checked> Answeranswer A</h2></li>
                    <li><h2><input class="uk-radio" type="radio" name="radio1"> Answer B</h2></li>
                    <li><h2><input class="uk-radio" type="radio" name="radio1"> Answer C</h2></li>
                    <li><h2><input class="uk-radio" type="radio" name="radio1"> Answer D</h2></li>
                    
                </ul>
                <button class="uk-button uk-button-default worker">Submit Aswer</button>
                <button class="uk-button uk-button-default ">Skip</button>
            </form>
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
                    <li><h4>skill-1: </h4>nome@@@@</li>
                    <li><h4>skill-2: </h4>@@@@</li>
                    <li><h4>skill-3: </h4>@@@@</li>
                </ul>
            </div>
        </div>
    </div>
    
</body>
</html>