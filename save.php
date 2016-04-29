<?php
header("Access-Control-Allow-Origin: *");
session_start();

$user = 'root';
$pass = '';
$host = "localhost";
if (isset($_SESSION['cat_i'])) {
  $i = intval($_SESSION['cat_i']);
  $_SESSION['cat_i'] = 0;
}


$database = 'SprintScraper';
$dsn = "mysql:host=$host;dbname=$database";
$pdo_opt = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

$json = $_POST['data'];
$current_page = intval($_POST['currentPage']);
if ($current_page == 0) {
  $current_page = 1;
}
$last_page = intval($_POST['lastPage']);
$url = $_POST['url'];
$next_url = "";
$page_str = "?p=";
$next_page = $current_page + 1;
$pos = strpos($url, "all-courses/");
if (strpos($url, $page_str) === false) {
  // not found
  if (strpos($url, "all-courses/") === false) {
    $next_url = $url . "/?p=" . $next_page;
  } else {
    $next_url = substr_replace($url, "/?p=" . $next_page, $pos + 11);
  }
} else {
  // found
  $next_url = substr_replace($url, "/?p=" . $next_page, $pos + 11);
}

$data = json_decode($json);
try {
  $dbh = new PDO($dsn, $user, $pass, $pdo_opt);
  for ($i = 0; $i < 10;$i++) {
      $a     = get_object_vars($data[$i]);
     $statement = $dbh->prepare("INSERT INTO courses (link, author, title, price, rating, timesRated, level)
       VALUES(:link, :aut, :tit, :pri, :rat, :tim, :lev)");
     $statement->execute(array(
         "link" => $a['link'],
         "aut" => $a['author'],
         "tit" => $a['title'],
         "pri" => substr(trim($a['price']), 1, strlen($a['price'])),
         "rat" => substr($a['rating'], 0, -1),
         "tim" => substr($a['timesRated'], 2, -1),
         "lev" => $a['level']
     ));
  }
  $dbh = null;
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}
if ($current_page == $last_page) {
  $url = "http://localhost/sprint1/index.php";
  $_SESSION['cat_i'] = intval($_SESSION['cat_i']) + 1;
  header("Location: " . $url);
} else {
  header("Location: " . $next_url);
}
