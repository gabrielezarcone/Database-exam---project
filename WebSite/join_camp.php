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
    <?php include_once("lib/navbarWOR.php")?>
    <title><?php print($_SESSION[user])?> :: join </title>
</head>
<body>
<div uk-grid style="margin-top:2%;">
        <div class="uk-width-1-3"></div>
        <div class="uk-width-1-3">
            <h1 class="uk-heading-primary">Add skill</h1>
            <form action="#" method="POST">
                <?php
                    if(isset($_POST[keyword])&&isset($_POST[score])){
                        insert_keyword_work($_SESSION[user], $_POST[keyword],$_POST[score], "knowledge");
                        print('<div class="uk-alert-success" uk-alert>
                                                <a class="uk-alert-close" uk-close></a>
                                                <p>Skill '.$_POST[keyword].' created</p>
                                            </div>');
                    }
                ?>

                <div class="uk-margin">
                    <p>Select campaing</p>
                    <?php show_joinable_camp($_SESSION[user]); ?>
                </div>
                
                <div class="uk-margin">
                    <button class="uk-button uk-button-default worker">Join</button>
                </div>
            </form>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
    
</body>
</html>