<?php
header("Access-Control-Allow-Origin: *");
session_start();

$user = 'root';
$pass = '';
$host = "localhost";
if (isset($_SESSION['cat_i'])) {
  $i = intval($_SESSION['cat_i']);
  $_SESSION['cat_i'] = $i + 1;
}

$database = 'SprintScraper';
$dsn = "mysql:host=$host;dbname=$database";
$pdo_opt = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

$json = $_POST['data'];
$data = json_decode($json);
try {
  $dbh = new PDO($dsn, $user, $pass, $pdo_opt);
  for ($i = 0; $i < sizeof($data);$i++) {
      $a     = get_object_vars($data[$i]);
     $statement = $dbh->prepare("INSERT INTO courses (link, author, title, availability, date)
       VALUES(:link, :aut, :tit, :ava, :dat)");
     $statement->execute(array(
         "link" => $a['link'],
         "aut" => $a['author'],
         "tit" => $a['title'],
         "ava" => $a['availability'],
         "dat" => $a['date']
     ));
  }
  $dbh = null;
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}
$url = "http://localhost/sprint1/edxSuccess";
header("Location: " . $url);
