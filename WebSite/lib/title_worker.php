<link href="css/style.css" rel="stylesheet" type="text/css">
<div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky; bottom: #transparent-sticky-navbar">
    <nav class="uk-navbar-container" uk-navbar>
        <div class="uk-navbar-left">
            <ul class="uk-navbar-nav">
                <li class="uk-navbar-item">
                    <a href="index.php"><img src="src/oak_worker.png"></a>
                    <div class="title uk-heading-primary"><a href="index.php" style="text-decoration: none;color: inherit;">Oak_Sourcing</a></div>
                </li>
            </ul>
        </div>
        <div class="uk-navbar-right" style="margin-right: 1%;">
            <ul class="uk-navbar-nav">
                <li>
                    <a href="worker.php">
                        <span class="uk-icon uk-margin-small-right" uk-icon="icon: user"></span>
                        <?php print($_SESSION[user]); ?>
                    </a>
                </li>
                <li>
                    <a href="requester.php?logout=true">
                        <span class="uk-icon uk-margin-small-right" uk-icon="icon: push"></span>
                        LogOut
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>
