<?php
    session_start();
    include_once("lib/function.php");

    $_SESSION[task][answers] = array();
    foreach ($_POST as $k => $value) {
        if(preg_match ('/answ./' , $k)==1){
            $_SESSION[task][answers][$k] = $value;
        }
    }       
    $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($_SESSION[task]));        
    foreach ($iterator as $k => $value) {
        if($value!=null){
            create_task($_SESSION[task][title], $_SESSION[task][description], $_SESSION[task][campaign], $_SESSION[task][n_workers], $_SESSION[task][threshold], $_SESSION[task][pay_type], $_SESSION[task][pay_description]);

            print('<div class="uk-alert-success" uk-alert>
                                                    <a class="uk-alert-close" uk-close></a>
                                                    <p>Task '.$_SESSION[task][name].' created</p>
                                                </div>');
        }
    }   
    
    print_r($_SESSION);
    

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
            <h1 class="uk-heading-primary">Finish <?php $task[title] ?></h1>
            <form action="end_task.php" method="POST">
                
                <div class="uk-margin" >
                
                    <div class="uk-margin">
                    <?php
                        
                        if(isset($task[title])&&isset($task[description])&&isset($task[campaign])&&isset($task[n_workers])&&isset($task[threshold])&&isset($task[pay_type])&&isset($task[pay_description])){
                            
                        }
                    ?>
                    </div>
                    
            </form>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
    
</body>
</html>