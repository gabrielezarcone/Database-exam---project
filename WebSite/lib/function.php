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
        print('<a href="worker.php?campaign='.$campaign[0].'"><div class="uk-card uk-card-default uk-card-body uk-margin-top uk-flex-wrap-stretch">'.$campaign[0].'</div></a>');
    }
    pg_free_result($res);
    close_pg_connection($db);

    return $numrows;
}
function show_campaigns_R($user){
    $query = "SELECT name, id
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
        print('<a href="requester.php?campaign='.$campaign[1].'"><div class="uk-card uk-card-default uk-card-body uk-margin-top uk-flex-wrap-stretch">'.$campaign[0].'</div></a>');
    }
    pg_free_result($res);
    close_pg_connection($db);

    return $numrows;
}
function show_campaign_opt($user, $actual_camp_id){
    $query = "SELECT name,id
                FROM crowdsourcing.campaign AS C 
                WHERE C.requester = $1;";
    $values = array(1=>$user);
    $db = open_pg_connection();
    $res = pg_prepare($db, "function", $query);
    $res = pg_execute($db, "function", $values);
    $numrows = pg_numrows($res);
    print('<option selected="selected" value="'.$actual_camp_id.'">'.campaign_name($actual_camp_id).'</option>');
    
    for($i=0; $i<$numrows; $i++){
        $campaign = pg_fetch_array($res, $i);
        if($actual_camp_id!=$campaign[1]){
            print('<option value="'.$campaign[1].'">'.$campaign[0].'</option>');
        }
    }
    pg_free_result($res);
    close_pg_connection($db);
}
function show_pay_opt(){
    $query = "SELECT type
                FROM crowdsourcing.pay;";
    $values = array();
    $db = open_pg_connection();
    $res = pg_prepare($db, "function", $query);
    $res = pg_execute($db, "function", $values);
    $numrows = pg_numrows($res);
    
    for($i=0; $i<$numrows; $i++){
        $pay_type = pg_fetch_array($res, $i);
        
        print('<option>'.$pay_type[0].'</option>');
    }
    pg_free_result($res);
    close_pg_connection($db);
}


function create_campaign($name, $reg_start, $reg_end, $start, $end, $user){
    if(isset($name)){
        $query = 'INSERT INTO crowdsourcing.campaign( name, registration_start_date, registration_end_date, start_date, end_date, requester) VALUES($1, $2, $3, $4, $5, $6);';
        $values = array(1=>$name, $reg_start, $reg_end, $start, $end, $user);
        $db = open_pg_connection();
        $res = pg_prepare($db, "campaign", $query);
        $res = pg_execute($db, "campaign", $values);
        close_pg_connection($db);
    }
}
function create_task($title, $description, $campaign, $n_workers, $threshold, $pay_type, $pay_description, $user){
    if(isset($title)){
        $query = 'INSERT INTO crowdsourcing.task(description, title, n_workers, threshold, valid_bit, campaign, pay_type, pay_description) VALUES($1, $2, $3, $4, $5, $6, $7, $8);';
        $values = array(1=>$description, $title, $n_workers, $threshold, 'false', $campaign, $pay_type, $pay_description);
        $db = open_pg_connection();
        $res = pg_prepare($db, "task", $query);
        $res = pg_execute($db, "task", $values);
        close_pg_connection($db);
    }
}


function campaign_name($campaign_id){
    $query = 'SELECT name
                FROM crowdsourcing.campaign AS C
                WHERE C.id = $1;';
    $values = array(1=>$campaign_id);
    $db = open_pg_connection();
    $res = pg_prepare($db, "camp", $query);
    $res = pg_execute($db, "camp", $values);
    $row = pg_fetch_array($res);
    pg_free_result($res);
    close_pg_connection($db);
    return $row[0]; 
};
?>