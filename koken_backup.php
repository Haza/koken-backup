<?php

parse_str(implode('&', array_slice($argv, 1)), $_GET);

error_reporting(0);

date_default_timezone_set('UTC');

if (isset($_GET['id'])) {
  $album = $_GET['id'];
}
else {
  die('You need to specify an album ID.' . PHP_EOL . 'example: php koken_backup.php id=12' . PHP_EOL);
}

if (isset($_SERVER['QUERY_STRING'])) {
  $path = urldecode($_SERVER['QUERY_STRING']);
}
else {
  if (isset($_SERVER['PATH_INFO'])) {
    $path = $_SERVER['PATH_INFO'];
  }
  else {
    if (isset($_SERVER['REQUEST_URI'])) {
      $path = preg_replace('/.*\/i.php/', '', $_SERVER['REQUEST_URI']);
    }
  }
}

$ds = DIRECTORY_SEPARATOR;
$root = dirname(__FILE__);
$content = $root . $ds . 'storage';

require($content . $ds . 'configuration' . $ds . 'database.php');
$interface = $KOKEN_DATABASE['driver'];
if ($interface == 'mysqli') {
  $db_link = mysqli_connect($KOKEN_DATABASE['hostname'], $KOKEN_DATABASE['username'], $KOKEN_DATABASE['password'], NULL, (int) $KOKEN_DATABASE['port'], $KOKEN_DATABASE['socket']);

  // Get album name.
  $query = "SELECT * from koken_albums where id  = $album";
  $db_link->select_db($KOKEN_DATABASE['database']);
  $result = $db_link->query($query);
  while ($row = $result->fetch_assoc()) {
    $slug = $row['slug'];
    mkdir($content . $ds . 'backup' . $ds . $slug, 0777, TRUE);
  }

  $query = "SELECT * from koken_content where id IN (select content_id from koken_join_albums_content where album_id = $album)";
  $db_link->select_db($KOKEN_DATABASE['database']);
  $result = $db_link->query($query);
  $count_success = $count_fail = 0;
  while ($row = $result->fetch_assoc()) {
    $internal_path = substr($row['internal_id'], 0, 2) . $ds . substr($row['internal_id'], 2, 2);
    $original = $content . $ds . 'originals' . $ds . $internal_path . $ds . $row['filename'];
    $destination = $content . $ds . 'backup' . $ds . $slug . $row['filename'];
    if (file_exists($original) && copy($original, $destination)) {
      print_r('Copied ' . $original . ' to ' . $destination . PHP_EOL);
      $count_success++;
    }
    else {
      print_r('Failled to copy ' . $original . PHP_EOL);
      $count_fail++;
    }

  }
  $result->close();

  $db_link->close();
  print_r('--------------------' . PHP_EOL .
  'Processed ' . ($count_success + $count_fail) . ' items (' . $count_success . ' created, ' . $count_fail . ' failed)' . PHP_EOL);
}

