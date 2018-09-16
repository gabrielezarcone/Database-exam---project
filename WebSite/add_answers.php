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
                    'pay_description' => $_POST[pay_description],
                    'skill' => array() );
    
    
    foreach ($_POST as $k => $value) {
        if(preg_match ('/skil./' , $k)===1){
            $task[skill][$k] = $value;
        }
    }               
    
    $_SESSION[task] = $task;


?>

<script>i=0</script>

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
                
                    <div class="uk-margin" id="answer_container">
                        <p>Answer</p>
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: menu"></span>
                            <input class="uk-input uk-textarea" type="textarea" name="answer" placeholder="Option">
                        </div>
                        <input type="button" class="uk-button uk-button-default requester" onclick="add_answers_form('answer_container');" value="Add option">
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