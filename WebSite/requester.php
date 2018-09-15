<?php
    include_once("lib/function.php");
    session_start();
    
    $_SESSION[campaign] = $_GET['campaign'];
    print($_GET['campaign']);
?>

<!DOCTYPE html5>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once("lib/header.php")?>
    <?php include_once("lib/title_requester.php"); ?>
    <?php include_once("lib/navbarREQ.php")?>
    <title><?php print($_SESSION[user])?> :: requester </title>
</head>
<body>
    <div uk-grid style="margin-top:2%;" >
        <div class="uk-width-1-4 scrollable-side ">
            <h2 style="color: var(--requester-color); text-align: center;">My Campaigns</h2>
            <div class="uk-flex uk-flex-column">
                <?php $num_camp = show_campaigns_R($_SESSION[user]); ?>
            </div>
        </div>

        <div class="uk-width-1-4"></div>

        <div class="uk-width-1-2 cards-container">
            <div class="uk-card uk-card-default uk-card-body uk-animation-scale-down uk-width-expand uk-margin card-requester myCard">
                <h1 class="card" style="color: white;">Title</h1>
                <h2 class="card" style="color: white;">Lorem ipsum <a href="#">dolor</a> sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</h2>
                <div class="uk-card-footer">
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <ul class="uk-list uk-list-bullet">
                        <li> Answeranswer A</li>
                        <li> Answer B</li>
                        <li> Answer C</li>
                        <li> Answer D</li>
                    </ul>   
                </div>
            </div>
            
            <div class="uk-card uk-card-default uk-card-body uk-animation-scale-down uk-width-expand uk-margin card-requester myCard">
                <h1 class="card" style="color: white;">Title</h1>
                <h2 class="card" style="color: white;">Lorem ipsum <a href="#">dolor</a> sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</h2>
                <div class="uk-card-footer">
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <ul class="uk-list uk-list-bullet">
                        <li> Answeranswer A</li>
                        <li> Answer B</li>
                        <li> Answer C</li>
                        <li> Answer D</li>
                    </ul>   
                </div>
            </div>
            
            <div class="uk-card uk-card-default uk-card-body uk-animation-scale-down uk-width-expand uk-margin card-requester myCard">
                <h1 class="card" style="color: white;">Title</h1>
                <h2 class="card" style="color: white;">Lorem ipsum <a href="#">dolor</a> sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</h2>
                <div class="uk-card-footer">
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <ul class="uk-list uk-list-bullet">
                        <li> Answeranswer A</li>
                        <li> Answer B</li>
                        <li> Answer C</li>
                        <li> Answer D</li>
                    </ul>   
                </div>
            </div>
            
            <div class="uk-card uk-card-default uk-card-body uk-animation-scale-down uk-width-expand uk-margin card-requester myCard">
                <h1 class="card" style="color: white;">Title</h1>
                <h2 class="card" style="color: white;">Lorem ipsum <a href="#">dolor</a> sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</h2>
                <div class="uk-card-footer">
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <ul class="uk-list uk-list-bullet">
                        <li> Answeranswer A</li>
                        <li> Answer B</li>
                        <li> Answer C</li>
                        <li> Answer D</li>
                    </ul>   
                </div>
            </div>
            
            <div class="uk-card uk-card-default uk-card-body uk-animation-scale-down uk-width-expand uk-margin card-requester myCard">
                <h1 class="card" style="color: white;">Title</h1>
                <h2 class="card" style="color: white;">Lorem ipsum <a href="#">dolor</a> sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</h2>
                <div class="uk-card-footer">
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <ul class="uk-list uk-list-bullet">
                        <li> Answeranswer A</li>
                        <li> Answer B</li>
                        <li> Answer C</li>
                        <li> Answer D</li>
                    </ul>   
                </div>
            </div>
            
            <div class="uk-card uk-card-default uk-card-body uk-animation-scale-down uk-width-expand uk-margin card-requester myCard">
                <h1 class="card" style="color: white;">Title</h1>
                <h2 class="card" style="color: white;">Lorem ipsum <a href="#">dolor</a> sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</h2>
                <div class="uk-card-footer">
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <span class="uk-label uk-label-warning">#skill</span>
                    <ul class="uk-list uk-list-bullet">
                        <li> Answeranswer A</li>
                        <li> Answer B</li>
                        <li> Answer C</li>
                        <li> Answer D</li>
                    </ul>   
                </div>
            </div>
            
        </div>
        <div class="uk-width-1-4 card-container">
            <div class="uk-card uk-card-default uk-card-body ">
                <h2 style="color: var(--requester-color)">Worker info</h2>
                <ul class="uk-list">
                    <li><h4>User_Name: </h4><?php print($_SESSION[user]) ?></li>
                    <li><h4>Number of created campaings: </h4> <?php print($num_camp); ?> </li>
                    <li><h4>Success rate: </h4>@@@@</li>
                </ul>
            </div>
            <div class="uk-card uk-card-default uk-card-body uk-margin-top">
                <h2 style="color: var(--requester-color)"> This Campaign Skills</h2>
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