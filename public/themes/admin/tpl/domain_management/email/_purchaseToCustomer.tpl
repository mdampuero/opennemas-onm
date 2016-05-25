<p>
  <strong>{t}Thank you for choosing Opennemas!{/t}</strong>
</p>

<p>
  {t}You have just purchased the following items:{/t}
</p>

<ul>
  {foreach from=$domains item=domain}
    <li>{$domain['description']}</li>
  {/foreach}
</ul>

<p>
  {t}You can find your invoice on the following page:{/t}
</p>

<ul>
  <li>{$url}</li>
</ul>
