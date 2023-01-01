<?php
header("Content-type: text/xml");

echo "<?xml version='1.0' encoding='UTF-8'?>
 <rss version='2.0'>
 <channel>
 <title>The Jon Bell Firehose</title>
 <description>This will list everything Jon Bell is working on. Buckle up.</description>
 <language>en-us</language>";

include "class/wow_rss.php";

//Above feeds Combined in array for combined multiple feed option
$urls = [
  "https://jonb.tumblr.com/rss?boop=ack",
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
echo $combine->displayRssAsFeed();

echo "</channel></rss>";
?>
