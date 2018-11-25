<?php
    session_start();
    include_once("lib/function.php");
    if(isset($_GET[logout])){
        session_destroy();
        print('<meta http-equiv="refresh" content="0; url=index.php">');
    }
    unset($_SESSION['result_pw']);
    $_SESSION[campaign] = $_GET[campaign];
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
        <?php 
            if($_SESSION[user]!='admin'){
                print('<h2 style="color: var(--requester-color); text-align: center;">My Campaigns</h2>');
            }
        ?>
            <div class="uk-flex uk-flex-column">
                <?php 
                    if($_SESSION[user]!='admin'){
                        $num_camp = show_campaigns_R($_SESSION[user]); 
                    }
                ?>
            </div>
        </div>

        <div class="uk-width-1-4"></div>

        <div class="uk-width-1-2 cards-container">
            <?php 
                if($_SESSION[user]=='admin'){
                    accepted_requester();
                }
                else{
                    $show=show_card_R($_GET[campaign]); 
                }
            ?>
            
        </div>
        <div class="uk-width-1-4 card-container">

            <?php 
                if($_SESSION[user]!='admin'){
                    print('
                            <div class="uk-card uk-card-default uk-card-body ">
                                <h2 style="color: var(--requester-color)">Worker info</h2>
                                <ul class="uk-list">
                                    <li><h4>User_Name: </h4><?php print($_SESSION[user]) ?></li>
                                    <li><h4>Number of created campaings: </h4> <?php print($num_camp); ?> </li>
                                </ul>
                            </div>
                            <div class="uk-card uk-card-default uk-card-body uk-margin-top">
                                <h2 style="color: var(--requester-color)">Campaign Stats</h2>
                                <ul class="uk-list">
                                    <?php $stat=campaign_stat($_SESSION[campaign])?>
                                    <li><h4>Task Number: </h4> <?php print($stat[num_task]); ?> </li>
                                    <li><h4>Completed Task: </h4> <?php print($show[completed_num]); ?> </li>
                                    <li><h4>Completed Task (%): </h4> <?php print($stat[percent]); ?>% </li>
                                </ul>
                            </div>
                            <div class="uk-card uk-card-default uk-card-body uk-margin-top">
                                <h2 style="color: var(--requester-color)"> This Campaign Keywords</h2>
                                <ul class="uk-list">
                                    <?php show_keyword_list($_SESSION[campaign]) ?>
                                </ul>
                            </div>
                    ');
                }
            ?>
        </div>
    </div>
    
</body>
</html>