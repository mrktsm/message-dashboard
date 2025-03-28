<?php
$server = "cray.cs.gettysburg.edu";
$dbase = "s25_tsymma01";
$user = "tsymma01";
$pass = "tsymma01";
$dsn = "mysql:host=$server;dbname=$dbase"; // data source name
try {
    $db = new PDO($dsn, $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    print "<H3>Successfully connected to database</H3>\n";
}
catch(PDOException $e) {
    error_log($e->getMessage());
    print "<H3>ERROR connecting to database</H3>\n";
    exit();
}
?>
