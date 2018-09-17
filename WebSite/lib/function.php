<?php

function open_pg_connection() {
	include_once('conf/conf.php');
    $connection = "host=".myhost." dbname=".mydb." user=".myuser." password=".mypsw;
    return pg_connect ($connection);  
}
function force_pg_connection() {
	include_once('conf/conf.php');
    $connection = "host=".myhost." dbname=".mydb." user=".myuser." password=".mypsw;
    return pg_connect ($connection, PGSQL_CONNECT_FORCE_NEW );  
}

function close_pg_connection($db) {
    return pg_close ($db);  
}




/*
function next_page($url, $check_arr){
    foreach ($check_arr as $key => $value) {
        if(isset($check_arr[key])===FALSE){
            print($url);
            return;
        }
    }
    print('#');
    return '<div class="uk-alert-danger" uk-alert>
                <a class="uk-alert-close" uk-close></a>
                <p>Fill every field please</p>
            </div>';
}
*/


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
function show_keyword_opt(){
    $query = "SELECT keyword
                FROM crowdsourcing.keyword;";
    $values = array();
    $db = open_pg_connection();
    $res = pg_prepare($db, "keywords", $query);
    $res = pg_execute($db, "keywords", $values);
    $numrows = pg_numrows($res);
    
    for($i=0; $i<$numrows; $i++){
        $keyword = pg_fetch_array($res, $i);
        $str = trim('<option>'.$keyword[0].'</option>');
        echo $str;
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
function create_task($title, $description, $campaign, $n_workers, $threshold, $pay_type, $pay_description){
    if(isset($title)){
        $query = 'INSERT INTO crowdsourcing.task(description, title, n_workers, threshold, valid_bit, campaign, pay_type, pay_description) VALUES($1, $2, $3, $4, $5, $6, $7, $8);';
        $values = array(1=>$description, $title, $n_workers, $threshold, 'false', $campaign, $pay_type, $pay_description);
        $db = open_pg_connection();
        $res = pg_prepare($db, "task", $query);
        $res = pg_execute($db, "task", $values);
        close_pg_connection($db);
    }
}
function insert_answer($task, $answer){
    $db = open_pg_connection();
    $query = 'INSERT INTO crowdsourcing.answer(task, value) VALUES($1, $2);';
    $values = array(1=>$task, $answer);
    $res = pg_prepare($db, "insert_answer", $query);
    $res = pg_execute($db, "insert_answer", $values);
    close_pg_connection($db);
}
function check_keyword($keyword){
    $query = "SELECT *
                FROM crowdsourcing.keyword
                WHERE keyword = $1;";
    $values = array(1=> $keyword);
    $db = open_pg_connection();
    $res = pg_prepare($db, "check_key", $query);
    $res = pg_execute($db, "check_key", $values);
    $numrows = pg_numrows($res);
    pg_free_result($res);
    close_pg_connection($db);
    return $numrows;
}
function insert_keyword($task, $keyword, $keyword_type){
    if(check_keyword($keyword)===0){
        $query = "INSERT INTO crowdsourcing.keyword(keyword, type) VALUES($1,$2);";
        $values = array(1=> $keyword, $keyword_type);
        $db = open_pg_connection();
        $res = pg_prepare($db, "add_key", $query);
        $res = pg_execute($db, "add_key", $values);
        $query = "INSERT INTO crowdsourcing.requires_keyword(task, keyword) VALUES($1,$2);";
        $values = array(1=> $task, $keyword);
        $res = pg_prepare($db, "requires_key", $query);
        $res = pg_execute($db, "requires_key", $values);
        pg_free_result($res);
        close_pg_connection($db);
    }
    else{
        $db = open_pg_connection();
        $query = "INSERT INTO crowdsourcing.requires_keyword(task, keyword) VALUES($1,$2);";
        $values = array(1=> $task, $keyword);
        $res = pg_prepare($db, "requires_key", $query);
        $res = pg_execute($db, "requires_key", $values);
        pg_free_result($res);
        close_pg_connection($db);
    }
    return $numrows;
}


function show_keyword(){
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
function show_keyword_list($campaign){
    $query = "SELECT DISTINCT RK.keyword
                FROM crowdsourcing.task AS T JOIN crowdsourcing.requires_keyword AS RK ON T.id = RK.task
                WHERE T.campaign = $1;";
    $values = array($campaign);
    $db = open_pg_connection();
    $res = pg_prepare($db, "key_list", $query);
    $res = pg_execute($db, "key_list", $values);
    $numrows = pg_numrows($res);
    
    for($i=0; $i<$numrows; $i++){
        $keyword = pg_fetch_array($res, $i);
        print('<li><h4>#</h4>'.$keyword[0].'</li>');
    }
    pg_free_result($res);
    close_pg_connection($db);
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
}
function get_task($title, $campaign){
    $query = 'SELECT id
                FROM crowdsourcing.task as A
                WHERE campaign=$1 and title=$2;';
    $values = array(1=>$campaign, $title);
    $db = open_pg_connection();
    $res = pg_prepare($db, "get_task", $query);
    $res = pg_execute($db, "get_task", $values);
    $row = pg_fetch_array($res);
    pg_free_result($res);
    close_pg_connection($db);
    return $row[0];
}

function get_keyword_task($campaign, $task){
    $query = 'SELECT keyword
                FROM crowdsourcing.requires_keyword
                WHERE task=$1;';
    $values = array(1=>$task);
    $db = force_pg_connection();
    $res = pg_prepare($db, "get_key_tasks", $query);
    $res = pg_execute($db, "get_key_tasks", $values);
    $numrows = pg_numrows($res);
    $keywords = array();
    for($i=0; $i<$numrows; $i++){
        $row = pg_fetch_array($res, $i, PGSQL_NUM);
        $keywords[$i] = $row[0]; 
    }
    pg_free_result($res);
    close_pg_connection($db);
    return $keywords;
}
function get_answers_task($campaign, $task){
    $query = 'SELECT value
                FROM crowdsourcing.answer
                WHERE task=$1;';
    $values = array(1=>$task);
    $db = force_pg_connection();
    $res = pg_prepare($db, "get_ans_tasks", $query);
    $res = pg_execute($db, "get_ans_tasks", $values);
    $numrows = pg_numrows($res);
    $keywords = array();
    for($i=0; $i<$numrows; $i++){
        $row = pg_fetch_array($res, $i, PGSQL_NUM);
        $answers[$i] = $row[0]; 
    }
    pg_free_result($res);
    close_pg_connection($db);
    return $answers;
}
function show_card_R($campaign){
    $query = 'SELECT *
                FROM crowdsourcing.task
                WHERE campaign=$1';
    $values = array(1=>$campaign);
    $db = open_pg_connection();
    $res = pg_prepare($db, "tasks", $query);
    $res = pg_execute($db, "tasks", $values);
    $numrows = pg_numrows($res);
    if($campaign==""){
        print('<h3 class="uk-text-center uk-text-muted"> Select a campaign</h3>');
    }
    else if($numrows==0){
        print('<h3 class="uk-text-center uk-text-muted"> There are no task in this Campaign</h3>');
    }
    for($i=0; $i<$numrows; $i++){
        $task = pg_fetch_array($res, $i);
        $answers = get_answers_task($campaign, $task[id]);
        $keywords = get_keyword_task($campaign, $task[id]);
        
        print('
            <div class="uk-card uk-card-default uk-card-body uk-animation-scale-down uk-width-expand uk-margin card-requester myCard">
                <h1 class="card" style="color: white;">'.$task[title].'</h1>
                <h2 class="card" style="color: white;">'.$task[description].'</h2>
                <div class="uk-card-footer">');
                    foreach ($keywords as $key => $keyword) {
                        print('<span class="uk-label uk-label-warning">#'.$keyword.' </span>');
                    }
                    print('<ul class="uk-list uk-list-bullet">');
                    foreach ($answers as $key => $answer) {
                        print('<li>'.$answer.'</li>');
                    }
                    print('</ul>   
                </div>
            </div>
        ');
        
    }
    pg_free_result($res);
    close_pg_connection($db);
}


?>