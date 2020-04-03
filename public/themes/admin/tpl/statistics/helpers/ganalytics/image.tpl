{foreach from=$params item=account}
  <img src="http://www.google-analytics.com/__utm.gif?utmwv=4&utmn={$random}&utmdt=Newsletter[{$date}]&utmhn={$url}&utmr={$newsurl}&utmp={$relurl}&utmac={trim($account['api_key'])}&utmcc={$utma}" style="border:0" alt="" />
{/foreach}
<img src="http://www.google-analytics.com/__utm.gif?utmwv=4&utmn={$random}&utmdt=Newsletter[{$date}]&utmhn={$url}&utmr={$newsurl}&utmp={$relurl}&utmac=UA-40838799-5&utmcc={$utma}" style="border:0" alt="" />
