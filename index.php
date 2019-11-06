<?php
include_once("DBConnector.php");

$DB = new DBConnector;

#EXAMPLE

/* STORE PROCEDURE SELECT
$storeProcSelect = $DB->storeProcSelect(0);
echo "<pre>";
var_dump($storeProcSelect);
*/

/* STORE PROCEDURE UPDATE AND INSERT
$data = array(
    "userId" => 5,
    "uname" => "anne_".date("his"),
    "fname" => "anneffff",
    "lname" => "curtis",
    "email" => "anne@gmail.com"
);

$storeProcSave = $DB->storeProcSave($data);
echo "<pre>";
var_dump($storeProcSave);
*/

/* STORE PROCEDURE DELETE
$storeProcDelete = $DB->storeProcDelete(7);
echo $storeProcDelete;
*/

/* INLINE SQL QUERY
$getRecord = $DB->getRecord("users", false, false);
var_dump($getRecord);
*/



?>