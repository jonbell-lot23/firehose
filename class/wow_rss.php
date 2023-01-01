<?php

/******
 *Class Name:WowSingleRSS
 *Author: Tracy Ridge
 *Version: 1.2
 *Updated 21st December 2019
 *Author URL:  https:www.worldoweb.co.uk/
 *Page URL:  https://wp.me/poe8j-2Ya
 *Description:  Display either a single or multiple RSS feeds on a website.
 *******/

class WowSingleRss
{
  //Default Values - Overidden by constructor
  private $options = [
    "url" => "",
    "maxItems" => 10,
    "date_format" => "jS F, Y",
    "sort" => ["sort_order" => "new_first"],
  ];

  /**
   * Constructor
   * @private
   * @author Tracy Ridge
   * @param array|string   $urls             Feed URL(S)
   * @param integer|string $maxitems         Maximum Items to display
   * @param string         [$date_format=''] Date Format
   * @param array|bool     $sortfeed         Sort feed for output
   */
  public function __construct($urls, $maxitems, $date_format = "", $sortfeed)
  {
    //Checks to see you have entered a string or an array
    if (is_string($urls) || is_array($urls)) {
      $this->urls = $urls;
    } else {
      $this->urls = $this->options["url"];
    }

    if (is_string($date_format)) {
      $this->date_format = $date_format;
    } else {
      $this->date_format = $this->options["date_format"];
    }

    switch ($sortfeed) {
      case is_array($sortfeed):
        $this->sortfeed = $sortfeed;
        break;
      default:
        $this->sortfeed = $this->options["sort"];
        break;
    }

    //Checks for intval (string or int)
    if (intval($maxitems)) {
      $this->maxitems = $maxitems;
    } else {
      $this->maxitems = $this->options["maxItems"];
    }
  }

  /**
   * @private
   * @author Tracy Ridge
   * @return string Outputs feed as string
   */
  public function __toString()
  {
    return $this->displayRss();
  }

  /**
   * Gets single feed using curl and converts to array
   * @author Tracy Ridge
   * @return array
   */
  private function getFeed()
  {
    $curl = curl_init($this->urls);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($curl, CURLOPT_VERBOSE, true);
    /*Minor change to allow for https */
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $rss = curl_exec($curl);
    // $rss = simplexml_load_string($rss, "SimpleXMLElement", LIBXML_NOCDATA);
    $rss = str_replace("<content:encoded>", "<contentEncoded>", $rss);
    $rss = str_replace("</content:encoded>", "</contentEncoded>", $rss);
    $rss = simplexml_load_string($rss);

    curl_close($curl);
    $array = json_decode(json_encode($rss), true);

    /*The output which tests if it works*/
    foreach ($rss->channel->item as $item) {
      echo $item->contentEncoded;
    }

    return $array;
  }

  protected function checkFeed()
  {
    return $this->getFeed();
  }

  /**
   * Processes array for outputting
   * @author Tracy Ridge
   * @return string Displays Feed
   */
  public function displayRss()
  {
    $array = $this->checkFeed();
    /*
    echo "<div style='background-color: #eeeeee'>";
    print_r($array);
    echo "</div>";
    */

    $array = $this->sortFeeds($array);
    //Start Outputting our feed
    $feed = "";
    $feed .= '<div class="wow_feed">'; //wrapper for wow feed

    $i = -1;

    foreach ($array["channel"]["item"] as $values) {
      if (++$i == $this->maxitems) {
        break;
      } //breaks when max-items is reached

      if ($this->date_format != "") {
        $date = $values["pubDate"];
        $d = $this->date_format;
        $date = date($d, $date); //Convert to proper date
      }
      if (isset($values["category"]) && !empty($values["category"])) {
        // $cat = $values["category"];
        $cat = null;
      } else {
        $cat = null;
      }
      $url = $values["link"];
      $title = $values["title"];
      $desc = $values["description"];
      $contentEncoded = $values["contentEncoded"];

      /*
      echo "<pre>";
      print_r($desc);
      echo "</pre>";
      */

      // $content = $values["content"];

      // $fuck = $values["content"];

      if (empty($title)) {
        $title = "(Empty title)";
      }

      $feed .= "<h2><a href='{$url}' title='{$title}'>{$title}</a></h2>";

      if (isset($cat) && !empty($cat)) {
        $feed .= "<div class='cat'>$cat</div>";
      }
      if (!empty($date)) {
        $feed .= "<div class='date'>$date</div>";
      }

      if (empty($desc)) {
        $desc = $contentEncoded;
      }

      $feed .= "<div class='desc'>{$desc}</div>";
    }

    $feed .= "</div>"; //end wrapper

    return $feed;
  }

  public function displayRssAsFeed()
  {
    $array = $this->checkFeed();
    $array = $this->sortFeeds($array);

    // now we need to do gnarly conversions to get content out
    /*
    $search = "<content:encoded>";
    $replace = "<contentEncoded>";

    foreach ($array as &$item) {
      $item = str_replace($search, $replace, $item);
    }
    */

    //Start Outputting our feed
    $feed = "";

    $i = -1;
    foreach ($array["channel"]["item"] as $values) {
      /*
      echo "<pre>FUCK FUCK FUCK";
      print_r($values);
      echo "</pre>";
      

      function test_print($item2, $key)
      {
        echo "$key. $item2\n";
      }
      */

      // array_walk($values, "test_print");

      if (++$i == $this->maxitems) {
        break;
      } //breaks when max-items is reached

      if (isset($values["category"]) && !empty($values["category"])) {
        $cat = $values["category"];
      } else {
        $cat = null;
      }
      $url = $values["link"];
      $title = $values["title"];
      $desc = $values["description"];
      $contentEncoded = $values["contentEncoded"];

      $date = $values["pubDate"];
      $date = date("r", $date); //Convert to proper date

      if (empty($desc)) {
        $desc = $contentEncoded;
      }

      if ($title) {
        $feed .= "<item>\n<title>{$title}</title>\n<link>{$url}</link>\n<pubDate>{$date} </pubDate>\n<description><![CDATA[{$desc}]]></description>\\n</item>\n";
      }

      /*
      if (isset($cat) && !empty($cat)) {
        $feed .= "<div class='cat'>$cat</div>";
      }
      if (!empty($date)) {
        $feed .= "<div class='date'>$date</div>";
      }
      $feed .= "<div class='desc'>{$desc}</div>";
	  */
    }

    return $feed;
  }

  /**
   * Converts published date to a unix timestamp via callback
   * @author Tracy Ridge
   */
  private function convertTimestamp(&$item, $key)
  {
    if ($key == "pubDate") {
      $item = strtotime(trim($item));
    }
  }

  /**
   * Sorts the feed(s) ascending or descending
   * @author Tracy Ridge
   * @param  array $feed Called by displayRss()
   * @return array Returns sorted array
   */
  private function sortFeeds($feed)
  {
    $array = $feed;
    //converts the timestamp for sorting
    array_walk_recursive($array, [$this, "convertTimestamp"]);

    //sort the feed using compare function
    switch ($this->sortfeed["sort_order"]) {
      case "new_first":
        usort($array["channel"]["item"], function ($a, $b) {
          return -($a["pubDate"] <=> $b["pubDate"]);
        });
        break;

      case "old_first":
        usort($array["channel"]["item"], function ($a, $b) {
          return $a["pubDate"] <=> $b["pubDate"];
        });
        break;
    }
    return $array;
  }
}

class WowMultiRss extends WowSingleRss
{
  /**
   * Gets Multiple Feeds- uses combineRss
   * @author Tracy Ridge
   * @return array
   */
  protected function getFeeds()
  {
    $mh = curl_multi_init();
    $requests = [];

    foreach ($this->urls as $key => $url) {
      // Add initialized cURL object to array
      $requests[$key] = curl_init($url);

      curl_setopt($requests[$key], CURLOPT_RETURNTRANSFER, true);

      // Add cURL object to multi-handle
      curl_multi_add_handle($mh, $requests[$key]);
    }

    // Do while all request have been completed
    do {
      curl_multi_exec($mh, $active);
    } while ($active > 0);

    // Collect all data here and clean up
    foreach ($requests as $key => $request) {
      $returned[$key] = curl_multi_getcontent($request);
      curl_multi_remove_handle($mh, $request);
      curl_close($request); //THIS MUST GO AFTER curl_multi_getcontent();
    }

    curl_multi_close($mh);

    foreach ($returned as $key => $value) {
      /*
      echo "<pre>";
      $value = str_replace("<content:encoded>", "<contentEncoded>", $value);
      $value = str_replace("</content:encoded>", "</contentEncoded>", $value);
      print_r($value);
      echo "</pre>";
      */

      $search = "content:encoded";
      $replace = "contentEncoded";

      $newValue = str_replace($search, $replace, $value);

      // print_r($newValue);

      // $array = array_replace_value($array, "Trump", "Fuckface");

      $array[$key] = simplexml_load_string(
        $newValue,
        "SimpleXMLElement",
        LIBXML_NOCDATA
      );
    }
    //combine the multiple feeds
    $array = $this->combineRss($array);

    // print_r($array);

    return $array;
  }

  /**
   * 	Combines Feeds
   * @author Tracy Ridge
   * @param  $array
   * @return $array
   */
  protected function combineRss($array)
  {
    $res = json_decode(json_encode($array), true); //convert to array

    $array = call_user_func_array("array_merge_recursive", $res);

    return $array;
  }

  protected function checkFeed()
  {
    return $this->getFeeds();
  }
}
