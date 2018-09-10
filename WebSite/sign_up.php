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
                            <input class="uk-input" type="text" placeholder="Name">
                        </div>
                        <div class="uk-inline">
                            <input class="uk-input" type="text" placeholder="Surname">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: bolt"></span>
                            <input class="uk-input" type="text" placeholder="Username">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: lock"></span>
                            <input class="uk-input" type="password" placeholder="Password">
                        </div>
                        <div class="uk-inline">
                            <input class="uk-input" type="password" placeholder="Repeat your password">
                        </div>
                    </div>
                    <div class="uk-margin">
                        <button class="uk-button uk-button-default worker">Submit</button>
                    </div>
                </li>

                <li id="requester">
                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: user"></span>
                            <input class="uk-input" type="text" placeholder="Name">
                        </div>
                        <div class="uk-inline">
                            <input class="uk-input" type="text" placeholder="Surname">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: bolt"></span>
                            <input class="uk-input" type="text" placeholder="Username">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <div class="uk-inline">
                            <span class="uk-form-icon" uk-icon="icon: lock"></span>
                            <input class="uk-input" type="password" placeholder="Password">
                        </div>
                        <div class="uk-inline">
                            <input class="uk-input" type="password" placeholder="Repeat your password">
                        </div>
                    </div>
                    <div class="uk-margin">
                        <button class="uk-button uk-button-default requester">Submit</button>
                    </div>
                </li>
                
            </ul>
        </div>
        <div class="uk-width-1-3"></div>
    </div>
</body>
</html>