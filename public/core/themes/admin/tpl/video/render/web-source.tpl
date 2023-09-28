<div class="video-container">
  {if $isAmp}
    <iframe allowfullscreen frameborder="0" height={$height} src="{$info['embedUrl']}" title="{$title}" width={$width}></iframe>
  {else}
    <div class="lazyframe"
      data-vendor="{$type}"
      data-title="{$title}"
      data-thumbnail="{$info['thumbnail']}"
      data-src="{$info['embedUrl']}"
      data-ratio="16:9">
    </div>
  {/if}
</div>
