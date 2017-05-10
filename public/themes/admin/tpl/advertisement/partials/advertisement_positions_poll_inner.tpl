<div class="col-md-9">
  <div class="row">
    <div class="col-md-12">
      <div class="radio">
        <input id="poll-inner-banner-intersticial-800x600" name="type_advertisement" type="radio" value="950" {if isset($advertisement) && $advertisement->type_advertisement == 950}checked="checked" {/if}/>
        <label for="poll-inner-banner-intersticial-800x600">
          Banner Intersticial - {t}Polls{/t}(800x600)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="poll-inner-left-skyscraper-160x600" name="type_advertisement" type="radio" value="991" {if isset($advertisement) && $advertisement->type_advertisement == 991}checked="checked" {/if}/>
        <label for="poll-inner-left-skyscraper-160x600">
          Left Skyscraper (160x600)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="poll-inner-right-skyscraper" name="type_advertisement" type="radio" value="992" {if isset($advertisement) && $advertisement->type_advertisement == 992}checked="checked" {/if}/>
        <label for="poll-inner-right-skyscraper">
          Right Skyscraper (160x600)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="poll-inner-big-banner-top-728x90" name="type_advertisement" type="radio" value="901" {if isset($advertisement) && $advertisement->type_advertisement == 901}checked="checked" {/if}/>
        <label for="poll-inner-big-banner-top-728x90">
          Big Banner Top (728X90)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="poll-inner-banner-top-right-234x90" name="type_advertisement" type="radio" value="902" {if isset($advertisement) && $advertisement->type_advertisement == 902}checked="checked" {/if}/>
        <label for="poll-inner-banner-top-right-234x90">
          Banner Top Right (234x90)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6 col-md-offset-6">
      <div class="radio">
        <input id="poll-inner-banner1-column-right-I-300x*" name="type_advertisement" type="radio" value="903" {if isset($advertisement) && $advertisement->type_advertisement == 903}checked="checked" {/if}/>
        <label for="poll-inner-banner1-column-right-I-300x*">
          Banner1 Column Right (I) (300x*)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="poll-inner-big-banner-bottom-728x90" name="type_advertisement" type="radio" value="909" {if isset($advertisement) && $advertisement->type_advertisement == 909}checked="checked" {/if}/>
        <label for="poll-inner-big-banner-bottom-728x90">
          Big Banner Bottom (728x90)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="poll-inner-banner-bottom-right-234x90" name="type_advertisement" type="radio" value="910" {if isset($advertisement) && $advertisement->type_advertisement == 910}checked="checked" {/if}/>
        <label for="poll-inner-banner-bottom-right-234x90">
          Banner Bottom Right (234x90)
        </label>
      </div>
    </div>
  </div>
</div>
<div class="col-md-3">
  <div id="advertisement-mosaic-poll-inner">
    <div id="advertisement-mosaic-frame-poll-inner"></div>
    <img src="{$_template->getImageDir()}/advertisement/right1Ads.png" style="width:100%" usemap="#mapGallery" />
  </div>
</div>
