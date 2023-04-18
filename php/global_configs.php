<?php
    include_once('..\..\vendor\ezyang\htmlpurifier\library\HTMLPurifier.auto.php');

    $prods_per_page = 20;
    $prods_rows = 4;
    $prods_cols = 5;

    $htmlpurifier_config = HTMLPurifier_Config::createDefault();
    $htmlpurifier = new HTMLPurifier($htmlpurifier_config);
?>