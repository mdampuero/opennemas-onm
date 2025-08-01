<figure class="op-ad{if $default} op-ad-default{/if}">
  {if $iframe}
    {$content}
  {else}
    <iframe height="{$height}" width="{$width}">
      {$content}
    </iframe>
  {/if}
</figure>
