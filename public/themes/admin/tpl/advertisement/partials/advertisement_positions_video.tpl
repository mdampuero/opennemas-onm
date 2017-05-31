<div class="col-md-9">
  <div class="row">
    <div class="col-md-12">
      <div class="radio">
        <input id="banner-intersticial-video-frontpage-800x600" name="type_advertisement" type="radio" value="250" {if isset($advertisement) && $advertisement->type_advertisement == 250}checked="checked" {/if}/>
        <label for="banner-intersticial-video-frontpage-800x600">
          Banner Intersticial - Video Frontpage (800X600)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="video-frontpage-left-skyscraper-160x600" name="type_advertisement" type="radio" value="291" {if isset($advertisement) && $advertisement->type_advertisement == 291}checked="checked" {/if}/>
        <label for="video-frontpage-left-skyscraper-160x600">
          Left Skyscraper (160 x 600)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="video-frontpage-right-skyscraper-160x600" name="type_advertisement" type="radio" value="292" {if isset($advertisement) && $advertisement->type_advertisement == 292}checked="checked" {/if}/>
        <label for="video-frontpage-right-skyscraper-160x600">
          Right Skyscraper (160 x 600)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="video-frontpage-banner-top-728x90" name="type_advertisement" type="radio" value="201" {if isset($advertisement) && $advertisement->type_advertisement == 201}checked="checked" {/if}/>
        <label for="video-frontpage-banner-top-728x90">
          Big Banner Top (728x90)
        </label>
      </div>

    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="video-frontpage-banner-top-right-234x90" name="type_advertisement" type="radio" value="202" {if isset($advertisement) && $advertisement->type_advertisement == 202}checked="checked" {/if}/>
        <label for="video-frontpage-banner-top-right-234x90">
          Banner Top Right (234x90)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6 col-md-offset-6">
      <div class="radio">
        <input id="video-frontpage-banner1-column-right-I-300x*" name="type_advertisement" type="radio" value="203" {if isset($advertisement) && $advertisement->type_advertisement == 203}checked="checked" {/if}/>
        <label for="video-frontpage-banner1-column-right-I-300x*">
          Banner1 Column Right (I) (300x*)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="video-frontpage-big-banner-bottom-728x90" name="type_advertisement" type="radio" value="209" {if isset($advertisement) && $advertisement->type_advertisement == 209}checked="checked" {/if}/>
        <label for="video-frontpage-big-banner-bottom-728x90">
          Big Banner Bottom (728x90)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="video-frontpage-banner-bottom-right-234x90" name="type_advertisement" type="radio" value="210" {if isset($advertisement) && $advertisement->type_advertisement == 210}checked="checked" {/if}/>
        <label for="video-frontpage-banner-bottom-right-234x90">
          Banner Bottom Right (234x90)
        </label>
      </div>
    </div>
  </div>
</div>
<div class="col-md-3">
  <div id="advertisement-mosaic-video" style="">
    <div id="advertisement-mosaic-frame-video"></div>
    <img src="{$_template->getImageDir()}/advertisement/videoAds.png" style="width:100%" height="401" usemap="#mapVideo" />
  </div>
</div>
