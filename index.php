<?php
header("Access-Control-Allow-Origin: *");
session_start();
$catNum = 0;

if (isset($_SESSION['cat_i']) and intval($_SESSION['cat_i']) > 0) {
  $catNum = intval($_SESSION['cat_i']);
}

$udemy_cats_file = "category_list.txt";
$handle = @fopen($udemy_cats_file, "r");
$line = 0;

if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
        if ($line == $catNum) {
          $base_url = "https://www.udemy.com";
          $spec_url = trim($buffer);
          $end_url = "all-courses/";
          header("Location: " . $base_url . $spec_url . $end_url);
        }
        $line++;
    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}
