<?php

/*
* Smarty plugin
* -------------------------------------------------------------
* Type:     modifier
* Name:     relative_date
* Version:  1.1
* Date:     November 28, 2008
* Author:   Chris Wheeler <chris@haydendigital.com>
* Purpose:  Output dates relative to the current time
* Input:    timestamp = UNIX timestamp or a date which can be converted by strtotime()
*           days = use date only and ignore the time
*           format = (optional) a php date format (for dates over 1 year)
* -------------------------------------------------------------
*/

function smarty_modifier_relative_date($timestamp, $days = false, $format = "M j, Y") {

  if (!is_numeric($timestamp)) {
    // It's not a time stamp, so try to convert it...
    $timestamp = strtotime($timestamp);
  }

  if (!is_numeric($timestamp)) {
    // If its still not numeric, the format is not valid
    return false;
  }

  // Calculate the difference in seconds
  $difference = time() - $timestamp;

  // Check if we only want to calculate based on the day
  if ($days && $difference < (60*60*24)) {
    return "Today";
  }
  if ($difference < 3) {
    return _("Just now");
  }
  if ($difference < 60) {    
    return sprintf(_("%d seconds ago"), $difference);
  }
  if ($difference < (60*2)) {    
    return _("1 minute ago");
  }
  if ($difference < (60*60)) {
    return sprintf(_("%d minutes ago"), intval($difference / 60));
  }
  if ($difference < (60*60*2)) {
    return _("1 hour ago");
  }
  if ($difference < (60*60*24)) {    
    return sprintf(_("%d hours ago"), intval($difference / (60*60)));
  }
  if ($difference < (60*60*24*2)) {
    return _("1 day ago");
  }
  if ($difference < (60*60*24*7)) {
    return sprintf(_("%d days ago"), intval($difference / (60*60*24)));
  }
  if ($difference < (60*60*24*7*2)) {
    return _("1 week ago");
  }
  if ($difference < (60*60*24*7*(52/12))) {
    return sprintf(_("%d weeks ago"), intval($difference / (60*60*24*7)));
  }
  if ($difference < (60*60*24*7*(52/12)*2)) {
    return _("1 month ago");
  }
  if ($difference < (60*60*24*364)) {
    return sprintf(_("%d months ago"), intval($difference / (60*60*24*7*(52/12))));
  }

  // More than a year ago, just return the formatted date
  return @date($format, $timestamp);

}

?>
