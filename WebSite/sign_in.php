<!DOCTYPE html5>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once("lib/header.php")?>
    <?php include_once("lib/navbar.php")?>
    <title>Sign in</title>
</head>
<body>
    <div uk-grid style="margin-top:2%;">
        <div class="uk-width-1-3"></div>
        <div class="uk-width-1-3">
            <ul class="uk-subnav uk-subnav-pill" uk-switcher="animation: uk-animation-fade" uk-grid>
                <li class="uk-width-1-2"><a class="worker" href="#">Worker</a></li>
                <li class="uk-width-1-2"><a class="requester" href="#">Requester</a></li>
            </ul>

            <ul class="uk-switcher uk-margin">
                <li>Hello!</li>
                <li>Hello again!</li>
            </ul>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
</body>
</html>