<script>
  var _gaq = _gaq || [];

  {foreach from=$params key=key item=account}
    {if !empty($key)}
      {assign var="key" value="account{$key}."}
    {else}
      {assign var="key" value=""}
    {/if}
    _gaq.push(['{$key}_setAccount', '{trim($account["api_key"])}']);
    {if !empty($account['base_domain']) && !empty(trim($account['base_domain']))}
      _gaq.push(['{$key}_setDomainName', '{trim($account["base_domain"])}']);
    {/if}
    {if !empty($account['custom_var'])}
      {base64_decode(trim($account['custom_var']))}
    {/if}
    {if array_key_exists('category', $account) && is_array($account['category']) && array_key_exists('index', $account['category']) && !empty($account['category']['index'])}
      _gaq.push(['{$key}_setCustomVar', '{$account["category"]["index"]}', '{$account["category"]["key"]}', '{$extra["category"]}', '{$account["category"]["scope"]}']);
    {/if}
    {if array_key_exists('extension', $account) && is_array($account['extension']) && array_key_exists('index', $account['extension']) && !empty($account['extension']['index'])}
      _gaq.push(['{$key}_setCustomVar', '{$account["extension"]["index"]}', '{$account["extension"]["key"]}', '{$extra["extension"]}', '{$account["extension"]["scope"]}']);
    {/if}
    _gaq.push(['{$key}_trackPageview']);
  {/foreach}

  _gaq.push(['onm._setAccount', 'UA-40838799-5']);
  _gaq.push(['onm._trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
  })();
</script>
