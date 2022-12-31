<?php
/*******
 *Check for errors on non production site - Please COMMENT OUT if live (//)
 ********/
/*
error_reporting(E_ALL);
ini_set("display_errors", 1);
*/
?>

<!doctype html>
<head>
<link rel="stylesheet" href="css/index.css">
<title>Combined RSS</title>
</head>
<body>

<h2>Under construction</h2>

<a href="http://firehose.lot43.com/feed.php">Direct RSS feed: http://firehose.lot43.com/feed.php</a>



<?php
include "class/wow_rss.php";

//Above feeds Combined in array for combined multiple feed option
$urls = [
  "https://jonb.tumblr.com/rss",
  "https://medium.com/feed/@jonbell",
  "https://a-blog-about-jon-bell.ghost.io/fullrss/",
];

/*
$urls = [
  "https://jonb.tumblr.com/rss",
  "https://medium.com/feed/@jonbell",
  "https://a-blog-about-jon-bell.ghost.io/fullrss/",
  "https://www.lexaloffle.com/bbs/feed.php?uid=17302",
  "https://jonbell.micro.blog/feed.xml",
];
*/

$date_format = "j F, Y";

//Do we want our newest posts displayed first
$sort_feed = ["sort_order" => "new_first"];

$combine = new WowMultiRss($urls, "80", "", true);
echo $combine->displayRss();
?>
</body>
