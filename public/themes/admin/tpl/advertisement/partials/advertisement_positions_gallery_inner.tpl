<div class="col-md-9">
  <div class="row">
    <div class="col-md-12">
      <div class="radio">
        <input id="gallery-inner-banner-intersticial-800x600" name="type_advertisement" type="radio" value="550" {if isset($advertisement) && $advertisement->type_advertisement == 550}checked="checked" {/if}/>
        <label for="gallery-inner-banner-intersticial-800x600">
          Banner Intersticial - Gallery (800x600)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="gallery-inner-left-skyscraper-160x600" name="type_advertisement" type="radio" value="591" {if isset($advertisement) && $advertisement->type_advertisement == 591}checked="checked" {/if}/>
        <label for="gallery-inner-left-skyscraper-160x600">
          Left Skyscraper (160x600)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="gallery-inner-right-skyscraper-160x600" name="type_advertisement" type="radio" value="592" {if isset($advertisement) && $advertisement->type_advertisement == 592}checked="checked" {/if}/>
        <label for="gallery-inner-right-skyscraper-160x600">
          Right Skyscraper (160x600)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="gallery-inner-big-banner-top-728x90" name="type_advertisement" type="radio" value="501" {if isset($advertisement) && $advertisement->type_advertisement == 501}checked="checked" {/if}/>
        <label for="gallery-inner-big-banner-top-728x90">
          Big Banner Top (728x90)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="gallery-inner-banner-top-right-234x90" name="type_advertisement" type="radio" value="502" {if isset($advertisement) && $advertisement->type_advertisement == 502}checked="checked" {/if}/>
        <label for="gallery-inner-banner-top-right-234x90">
          Banner Top Right (234x90)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6 col-md-offset-6">
      <div class="radio">
        <input id="gallery-inner-banner1-column-right-I-300x*" name="type_advertisement" type="radio" value="503" {if isset($advertisement) && $advertisement->type_advertisement == 503}checked="checked" {/if}/>
        <label for="gallery-inner-banner1-column-right-I-300x*">
          Banner1 Column Right (I) (300x*)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="gallery-inner-big-banner-bottom-728x90" name="type_advertisement" type="radio" value="509" {if isset($advertisement) && $advertisement->type_advertisement == 509}checked="checked" {/if}/>
        <label for="gallery-inner-big-banner-bottom-728x90">
          Big Banner Bottom (728x90)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="gallery-inner-banner-bottom-right-234x90" name="type_advertisement" type="radio" value="510" {if isset($advertisement) && $advertisement->type_advertisement == 510}checked="checked" {/if}/>
        <label for="gallery-inner-banner-bottom-right-234x90">
          Banner Bottom Right (234x90)
        </label>
      </div>
    </div>
  </div>
</div>
<div class="col-md-3">
  <div id="advertisement-mosaic-gallery-inner" style="">
    <div id="advertisement-mosaic-frame-gallery-inner"></div>
    <img src="{$_template->getImageDir()}/advertisement/right1Ads.png" style="width:100%" usemap="#mapGallery" />
  </div>
</div>
