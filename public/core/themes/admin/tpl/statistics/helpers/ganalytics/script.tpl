{if !empty($accounts)}
  <script async src="https://www.googletagmanager.com/gtag/js?id={$accounts[0]}"></script>
{else}
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-40838799-5"></script>
{/if}
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){ dataLayer.push(arguments); }
  gtag('js', new Date());

  {foreach $accounts as $account}
    gtag('config', '{$account}');
  {/foreach}

  gtag('config', 'UA-40838799-5');
</script>
