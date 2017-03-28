<div class="col-md-9">
  <div class="row">
    <div class="col-md-12">
      <div class="radio">
        <input id="gallery-frontpage-banner-intersticial-800x600" name="type_advertisement" type="radio" value="450" {if isset($advertisement) && $advertisement->type_advertisement == 450}checked="checked" {/if}/>
        <label for="gallery-frontpage-banner-intersticial-800x600">
          Banner Intersticial - Gallery (800x600)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="gallery-frontpage-left-skyscraper-160x600" name="type_advertisement" type="radio" value="491" {if isset($advertisement) && $advertisement->type_advertisement == 491}checked="checked" {/if}/>
        <label for="gallery-frontpage-left-skyscraper-160x600">
          Left Skyscraper (160x600)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="gallery-frontpage-right-skyscraper-160x600" name="type_advertisement" type="radio" value="492" {if isset($advertisement) && $advertisement->type_advertisement == 492}checked="checked" {/if}/>
        <label for="gallery-frontpage-right-skyscraper-160x600">
          Right Skyscraper (160x600)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="gallery-frontpage-big-banner-top-728x90" name="type_advertisement" type="radio" value="401" {if isset($advertisement) && $advertisement->type_advertisement == 401}checked="checked" {/if}/>
        <label for="gallery-frontpage-big-banner-top-728x90">
          Big Banner Top (728x90)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="gallery-frontpage-banner-top-right-234x90" name="type_advertisement" type="radio" value="402" {if isset($advertisement) && $advertisement->type_advertisement == 402}checked="checked" {/if}/>
        <label for="gallery-frontpage-banner-top-right-234x90">
          Banner Top Right (234x90)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6 col-md-offset-6">
      <div class="radio">
        <input id="gallery-frontpage-banner1-column-right-I-300x*" name="type_advertisement" type="radio" value="403" {if isset($advertisement) && $advertisement->type_advertisement == 403}checked="checked" {/if}/>
        <label for="gallery-frontpage-banner1-column-right-I-300x*">
          Banner1 Column Right (I) (300x*)
        </label>
      </div>
    </div>
    <div class="col-md-6 col-md-offset-6">
      <div class="radio">
        <input id="gallery-frontpage-banner2-column-right-II-300x*" name="type_advertisement" type="radio" value="405" {if isset($advertisement) && $advertisement->type_advertisement == 405}checked="checked" {/if}/>
        <label for="gallery-frontpage-banner2-column-right-II-300x*">
          Banner2 Column Right(II) (300x*)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="gallery-frontpage-big-banner-bottom-728x90" name="type_advertisement" type="radio" value="409" {if isset($advertisement) && $advertisement->type_advertisement == 409}checked="checked" {/if}/>
        <label for="gallery-frontpage-big-banner-bottom-728x90">
          Big Banner Bottom (728x90)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="gallery-frontpage-banner-bottom-right-234x90" name="type_advertisement" type="radio" value="410" {if isset($advertisement) && $advertisement->type_advertisement == 410}checked="checked" {/if}/>
        <label for="gallery-frontpage-banner-bottom-right-234x90">
          Banner Bottom Right (234x90)
        </label>
      </div>
    </div>
  </div>
</div>
<div class="col-md-3">
  <div id="advertisement-mosaic-gallery">
    <div id="advertisement-mosaic-frame-gallery"></div>
    <img src="{$_template->getImageDir()}/advertisement/right2Ads.png" style="width:100%" usemap="#mapGallery" />
  </div>
</div>
