<?php
    include_once("lib/function.php");
    session_start();

    assign_task_to_worker($_GET[task], $_POST[user]);
    header("worker.php");
?>