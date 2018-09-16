<?php
    session_start();
    include_once("lib/function.php");

    $_SESSION[task][answers] = array();
    foreach ($_POST as $k => $value) {
        if(preg_match ('/answ./' , $k)==1){
            $_SESSION[task][answers][$k] = $value;
        }
    }               
    

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
            <h1 class="uk-heading-primary">Add answers to task <?php $task[title] ?></h1>
            <form action="end_task.php" method="POST">
                
                <div class="uk-margin" >
                
                    <div class="uk-margin">
                        
                    </div>
                    
            </form>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
    
</body>
</html>