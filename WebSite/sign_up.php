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
            <h1 class="uk-heading-primary">Sign up</h1>
            <ul class="uk-subnav uk-subnav-pill" uk-switcher="animation: uk-animation-fade" uk-grid>
                <li class="uk-width-1-2"><a class="worker" href="#">Worker</a></li>
                <li class="uk-width-1-2"><a class="requester" href="#">Requester</a></li>
            </ul>

            <ul class="uk-switcher uk-margin">
                <li id="worker">
                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: user"></span>
                            <input class="uk-input" type="text">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: lock"></span>
                            <input class="uk-input" type="password">
                        </div>
                    </div>
                    <div class="uk-margin">
                        <button class="uk-button uk-button-default worker">Send</button>
                    </div>
                </li>
                <li id="requester">
                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: user"></span>
                            <input class="uk-input" type="text">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: lock"></span>
                            <input class="uk-input" type="password">
                        </div>
                    </div>
                    <div class="uk-margin">
                        <button class="uk-button uk-button-default requester">Send</button>
                        <input class="uk-button uk-button-default requester" type="submit">
                    </div>
                </li>
                
            </ul>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
</body>
</html>