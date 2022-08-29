<?php
$serverName = "DESKTOP-MUACTJ4\SQLEXPRESS"; //serverName\instanceName

// Since UID and PWD are not specified in the $connectionInfo array,
// The connection will be attempted using Windows Authentication.
$connectionInfo = array( "Database"=>"pos","UID"=>"","PWD"=>"");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {

   }else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}
?>


