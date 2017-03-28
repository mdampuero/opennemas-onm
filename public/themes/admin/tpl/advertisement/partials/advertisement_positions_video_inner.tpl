<div class="col-md-9">
  <div class="row">
    <div class="col-md-12">
      <div class="radio">
        <input id="video-inner-banner-intersticial-800x600" name="type_advertisement" type="radio" value="350" {if isset($advertisement) && $advertisement->type_advertisement == 350}checked="checked" {/if}/>
        <label for="video-inner-banner-intersticial-800x600">
          Banner Intersticial - Video Inner (800x600)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="video-inner-left-skyscraper-160x600" name="type_advertisement" type="radio" value="391" {if isset($advertisement) && $advertisement->type_advertisement == 391}checked="checked" {/if}/>
        <label for="video-inner-left-skyscraper-160x600">
          Left Skyscraper (160x600)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="video-inner-right-skyscrapper-160x600" name="type_advertisement" type="radio" value="392" {if isset($advertisement) && $advertisement->type_advertisement == 392}checked="checked" {/if}/>
        <label for="video-inner-right-skyscrapper-160x600">
          Right Skyscraper (160x600)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="video-frontpage-big-banner-top-728x90" name="type_advertisement" type="radio" value="301" {if isset($advertisement) && $advertisement->type_advertisement == 301}checked="checked" {/if}/>
        <label for="video-frontpage-big-banner-top-728x90">
          Big Banner Top (728x90)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="video-inner-banner-top-right-234x90" name="type_advertisement" type="radio" value="302" {if isset($advertisement) && $advertisement->type_advertisement == 302}checked="checked" {/if}/>
        <label for="video-inner-banner-top-right-234x90">
          Banner Top Right (234x90)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6 col-md-offset-6">
      <div class="radio">
        <input id="video-inner-button-column-300x250" name="type_advertisement" type="radio" value="303" {if isset($advertisement) && $advertisement->type_advertisement == 303}checked="checked" {/if}/>
        <label for="video-inner-button-column-300x250">
          Button Column (300x250)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="video-inner-big-banner-bottom-728x90" name="type_advertisement" type="radio" value="309" {if isset($advertisement) && $advertisement->type_advertisement == 309}checked="checked" {/if}/>
        <label for="video-inner-big-banner-bottom-728x90">
          Big Banner Bottom (728x90)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="video-inner-banner-bottom-right-234x90" name="type_advertisement" type="radio" value="310" {if isset($advertisement) && $advertisement->type_advertisement == 310}checked="checked" {/if}/>
        <label for="video-inner-banner-bottom-right-234x90">
          Banner Bottom Right (234x90)
        </label>
      </div>
    </div>
  </div>
</div>
<div class="col-md-3">
  <div id="advertisement-mosaic-videointerior" style=" ">
    <div id="advertisement-mosaic-videointerior-frame"></div>
    <img src="{$_template->getImageDir()}/advertisement/videoAds.png" style="width:100%" height="435" usemap="#mapVideoInterior" />
  </div>
</div>
