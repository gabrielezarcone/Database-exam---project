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
            <h1 class="uk-heading-primary">Perfect!!</h1>
            <form action="end_task.php" method="POST">
                
                <div class="uk-margin" >
                
                    <div class="uk-margin">
                    <?php
                        
                        create_task($_SESSION[task][title], $_SESSION[task][description], $_SESSION[task][campaign], $_SESSION[task][n_workers], $_SESSION[task][threshold], $_SESSION[task][pay_type], $_SESSION[task][pay_description]);
                        foreach ($_SESSION[task][answers] as $key => $answer) {
                            insert_answer(get_task($_SESSION[task][title], $_SESSION[task][campaign]), $answer);
                        }
                        foreach ($_SESSION[task][skill] as $key => $keyword) {
                            insert_keyword(get_task($_SESSION[task][title], $_SESSION[task][campaign]), $keyword, "knowledge");
                        }

                        print('<div class="uk-alert-success" uk-alert>
                                    <a class="uk-alert-close" uk-close></a>
                                    <p>Task '.$_SESSION[task][name].' created</p>
                                </div>');
                        
                    ?>
                    </div>
                    
            </form>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
    
</body>
</html>