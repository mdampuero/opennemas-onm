<div class="col-md-9">
  <div class="row">
    <div class="col-md-12">
      <div class="radio">
        <input id="newsletter-big-banner-top-728x90" name="type_advertisement" type="radio" value="1001" {if isset($advertisement) && $advertisement->type_advertisement == 1001}checked="checked" {/if}/>
        <label for="newsletter-big-banner-top-728x90">
          Big Banner Top (728x90)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-12">
      <div class="radio">
        <input id="newsletter-big-banner-bottom-728x90" name="type_advertisement" type="radio" value="1009" {if isset($advertisement) && $advertisement->type_advertisement == 1009}checked="checked" {/if}/>
        <label for="newsletter-big-banner-bottom-728x90">
          Big Banner Bottom (728x90)
        </label>
      </div>
    </div>
  </div>
</div>
<div class="col-md-3">
  <div id="advertisement-mosaic-newsletter">
    <div id="advertisement-mosaic-frame-newsletter"></div>
    <img src="{$params.IMAGE_DIR}/advertisement/newsletter.png" width="240" usemap="#mapGallery" />
  </div>
</div>
