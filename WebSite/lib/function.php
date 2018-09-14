<?php

function open_pg_connection() {
	include_once('conf/conf.php');
    $connection = "host=".myhost." dbname=".mydb." user=".myuser." password=".mypsw;
    return pg_connect ($connection);  
}

function close_pg_connection($db) {
    return pg_close ($db);  
}

function show_campaigns_W($user){
    $query = "SELECT name
                FROM crowdsourcing.joins_campaign AS JC JOIN crowdsourcing.campaign AS C ON JC.campaign=C.id
                WHERE JC.worker = $1;";
    $values = array(1=>$user);
    $db = open_pg_connection();
    $res = pg_prepare($db, "function", $query);
    $res = pg_execute($db, "function", $values);
    $numrows = pg_numrows($res);
    if($numrows==0){
        print('<h3 class="uk-text-center uk-text-muted">Please subscrive some campaign to begin ;)</h3>');
    }
    for($i=0; $i<$numrows; $i++){
        $campaign = pg_fetch_array($res, $i);
        print('<a href="#?campaign='.$campaign[0].'"><div class="uk-card uk-card-default uk-card-body uk-margin-top uk-flex-wrap-stretch">'.$campaign[0].'</div></a>');
    }
    pg_free_result($res);
    close_pg_connection($db);

    return $numrows;
}
function show_campaigns_R($user){
    $query = "SELECT name
                FROM crowdsourcing.campaign AS C 
                WHERE C.requester = $1;";
    $values = array(1=>$user);
    $db = open_pg_connection();
    $res = pg_prepare($db, "function", $query);
    $res = pg_execute($db, "function", $values);
    $numrows = pg_numrows($res);
    if($numrows==0){
        print('<h3 class="uk-text-center uk-text-muted">Please create some campaign to begin ;)</h3>');
    }
    for($i=0; $i<$numrows; $i++){
        $campaign = pg_fetch_array($res, $i);
        print('<a href="#?campaign='.$campaign[0].'"><div class="uk-card uk-card-default uk-card-body uk-margin-top uk-flex-wrap-stretch">'.$campaign[0].'</div></a>');
    }
    pg_free_result($res);
    close_pg_connection($db);

    return $numrows;
}

?>