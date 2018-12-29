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
    <title><?php print($_SESSION[user])?> :: worker </title>
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
                    <p>New skill</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: hashtag"></span>
                        <input class="uk-input" type="text" name="keyword" list="skills_list" placeholder="Select or add your own skill">
                    </div>
                    <datalist id="skills_list">
                        <?php show_keyword_opt();?>
                    </datalist>
                </div>

                <div class="uk-margin">
                    <p>Score</p>
                    <div class="uk-margin">
                        <input class="uk-range" type="range" name="score" id="score" value="5" min="0" max="10" step="1" onmousemove="show_number_range('score','num')" onclick="show_number_range('score','num')">
                        <p id="num"></p>
                        <script>
                            show_number_range('score','num');
                        </script>
                    </div>
                </div>
                
                
                <div class="uk-margin">
                    <button class="uk-button uk-button-default worker">Create</button>
                </div>
            </form>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
    
</body>
</html>