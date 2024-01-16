<div class="video-container">
  {if $isAmp}
    <iframe allowfullscreen frameborder="0" height={$height} src="{$info['embedUrl']}" title="{$title}" width={$width}></iframe>
  {elseif $info['service'] === 'Globalmest'}
    <script type="application/javascript" src="{$info['embedUrl']|replace:".html":".js"}"></script>
  {else}
    <div class="lazyframe"
      data-vendor="{$type}"
      data-title="{$title|escape:'html'}"
      data-thumbnail="{$info['thumbnail']}"
      data-src="{$info['embedUrl']}"
      data-ratio="16:9">
    </div>
  {/if}
</div>
