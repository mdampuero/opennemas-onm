{capture name=purchase_url}{url name="backend_ws_purchase_get_pdf" id=$purchase->id absolute=true}{/capture}
<p>
  <strong>{t}Thank you for choosing Opennemas!{/t}</strong>
</p>

<p>
  {t}You have just purchased the following items:{/t}
</p>

<ul>
 {foreach from=$items item=item}
   <li>{$item['description']}</li>
 {/foreach}
</ul>

<p>
{t escape=off 1=$smarty.capture.purchase_url}You can find your invoice <a href="%1">here</a>{/t}
</p>
