<?php 
    session_start();
    if(isset($_SESSION[user_type]) && $_SESSION[user_type]=='worker'){
        print('<meta http-equiv="refresh" content="0.5; url=worker.php">');
    }
    else if(isset($_SESSION[user_type]) && $_SESSION[user_type]=='requester'){
        print('<meta http-equiv="refresh" content="0.5; url=requester.php">');
    }
    else{
        print('<meta http-equiv="refresh" content="0.5; url=home.php">');
    }
?>
<!DOCTYPE html5>

<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once("lib/header.php")?>
    <title>Oak_Sourcing</title>
</head>
<body>
    <div align="center" style="margin-top:15%">
        <div class="title uk-heading-primary"><p style="text-decoration: none;color: inherit; font-size:200%">Oak_Sourcing</p></div>
        <div uk-spinner="ratio: 3"></div>
    </div>
</body>