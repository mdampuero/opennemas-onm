<script>
  (function(i,s,o,g,r,a,m){
    i['GoogleAnalyticsObject'] = r;
    i[r] = i[r] || function(){ (i[r].q = i[r].q || []).push(arguments)}, i[r].l = 1 * new Date();
    a=s.createElement(o), m=s.getElementsByTagName(o)[0];
    a.async=1;a.src=g;m.parentNode.insertBefore(a,m) })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  {foreach $params as $key => $account}
    {if !empty($key)}
      {assign var="key" value="account{$key}"}
    {else}
      {assign var="key" value=""}
    {/if}

    {if is_array($account) && array_key_exists('api_key', $account) && !empty(trim($account['api_key']))}
      {if array_key_exists('base_domain', $account) && !empty(trim($account['base_domain']))}
        ga('create', '{$account['api_key']}', '{trim($account['base_domain'])}', '{$key}');
      {else}
        ga('create', '{$account['api_key']}', 'auto', 'account{$key}');
      {/if}
    {/if}

    ga('{$key}.require', 'displayfeatures');
    ga('{$key}.set', 'campaignSource', 'Facebook');
    ga('{$key}.set', 'campaignMedium', 'Social Instant Article');
    ga('{$key}.send', 'pageview', { title: '{$title}' });
  {/foreach}

  ga('create', 'UA-40838799-5', 'opennemas.com','onm');
  ga('onm.require', 'displayfeatures');
  ga('onm.set', 'campaignSource', 'Facebook');
  ga('onm.set', 'campaignMedium', 'Social Instant Article');
  ga('onm.send', 'pageview', { title: '{$title}' });
</script>
