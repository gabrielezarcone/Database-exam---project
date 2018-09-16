<?php
    session_start();
    include_once("lib/function.php");

    $task = array('user' =>$_SESSION[user],
                    'title' => $_POST[title],           //
                    'description' => $_POST[description],//
                    'campaign' => $_POST[campaign],     //
                    'n_workers' => $_POST[n_workers],   //
                    'threshold' => $_POST[threshold],   //
                    'pay_type' => $_POST[pay_type],
                    'pay_description' => $_POST[pay_description]);
    
                    print_r($_POST);


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
            <h1 class="uk-heading-primary">Add skills to task <?php $task[title] ?></h1>
            <form action="add_skills.php" method="POST">
                
                <div class="uk-margin" >
                
                    Lorem Ipsum
                
                <div class="uk-margin">
                    <button class="uk-button uk-button-default requester">Create</button>
                </div>
            </form>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
    
</body>
</html>