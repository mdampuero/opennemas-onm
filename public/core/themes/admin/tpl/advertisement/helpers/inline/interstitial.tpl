<div class="interstitial">
  <div class="interstitial-wrapper" style="width: {$size['width']}px;">
    <div class="interstitial-header">
      <span class="interstitial-header-title">
        {t}Entering on the requested page{/t}
      </span>
      <a class="interstitial-close-button" href="#" title="{t}Skip advertisement{/t}">
        <span>{t}Skip advertisement{/t}</span>
      </a>
    </div>
    <div class="interstitial-content" style="height: {$size['height']|cat:'px'|default:'auto'};">
      <div class="ad-slot oat oat-visible oat-{$orientation}" data-id="{$ad->id}" data-timeout="{$ad->timeout|default:5}" data-type="{', '|implode:$ad->positions}">
        {$content}
      </div>
    </div>
  </div>
</div>
