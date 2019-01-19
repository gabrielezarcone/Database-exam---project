<?php
    session_start();
    include_once("lib/function.php");


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
    <title><?php print($_SESSION[user])?> :: create </title>
</head>
<body>
    <div uk-grid style="margin-top:2%;">
        <div class="uk-width-1-3"></div>
        <div class="uk-width-1-3">
            <h1 class="uk-heading-primary">Create Task</h1>
            <form action="add_answers.php" method="POST">
                
                <div class="uk-margin">
                    <p>Campaign</p>
                        <select class="uk-input uk-select" name="campaign">
                            <span class="uk-form-icon" uk-icon="icon: bookmark"></span>
                            <?php 
                                show_campaign_opt($_SESSION[user],$_SESSION[campaign]); 
                            ?>
                        </select>
                </div>

                <div class="uk-margin">
                    <p>Task Title</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: bookmark"></span>
                        <input class="uk-input" type="text" name="title" placeholder="Title">
                    </div>
                </div>

                <div class="uk-margin">
                    <p>Description</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: menu"></span>
                        <input class="uk-input uk-textarea" type="textarea" name="description" placeholder="Description">
                    </div>
                </div>
                
                <div class="uk-margin">
                    <p>Number of worker</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: users"></span>
                        <input class="uk-input" type="number" name="n_workers">
                    </div>
                </div>

                <div class="uk-margin">
                    <p>Threshold</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: settings"></span>
                        <input class="uk-input" type="number" name="threshold">
                    </div>
                    %
                </div>

                <div class="uk-margin" id="skill_container">
                    <p>Skills</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: hashtag"></span>
                        <input class="uk-input" type="text" name="skill" list="skills_list" placeholder="Select or add your own skill">
                    </div>
                    <datalist id="skills_list">
                        <?php show_keyword_opt(); ?>
                    </datalist>
                    <input type="button" class="uk-button uk-button-default requester" onclick="add_skills_form('skill_container', '<?php show_keyword_opt()?>');" value="Add skill">
                </div>
                
                <div class="uk-margin">
                    <p>Pay type</p>
                    
                        <select class="uk-input" name="pay_type">
                            <span class="uk-form-icon" uk-icon="icon: bookmark"></span>
                            <?php show_pay_opt(); ?>
                        </select>
                </div>

                <div class="uk-margin">
                    <p>Pay Description</p>
                    <div class="uk-inline">
                        <span class="uk-form-icon" uk-icon="icon: tag"></span>
                        <input class="uk-input uk-textarea" type="textarea" name="pay_description" placeholder="Description">
                    </div>
                </div>
                
                <div class="uk-margin">
                    <button class="uk-button uk-button-default requester" type="submit">Next</button>
                </div>
            </form>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
    
</body>
</html>