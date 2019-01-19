<?php
    session_start();
    include_once("lib/function.php");

    $campaign =$_POST[campaign];

?>

<script>
    i=0;
</script>

<!DOCTYPE html5>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once("lib/header.php")?>
    <?php include_once("lib/title_requester.php"); ?>
    <?php include_once("lib/navbarREQ.php")?>
    <title><?php print($_SESSION[user])?> :: top10 </title>
</head>
<body>
    <div uk-grid style="margin-top:2%;">
        <div class="uk-width-1-3"></div>
        <div class="uk-width-1-3">
            <h1 class="uk-heading-primary">Top 10</h1>
            <form action="top10.php" method="POST">
                
                <div class="uk-margin">
                    <p>Campaign</p>
                        <select class="uk-input uk-select" name="campaign">
                            <span class="uk-form-icon" uk-icon="icon: bookmark"></span>
                            <?php 
                                show_campaign_opt($_SESSION[user],$_SESSION[campaign]); 
                            ?>
                        </select>
                </div>


                <div>
                    <?php show_top10($campaign); ?>
                </div>
                
                
                <div class="uk-margin">
                    <button class="uk-button uk-button-default requester" type="submit">Load</button>
                </div>
            </form>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
    
</body>
</html>