<div class="col-md-9">
  <div class="row">
    <div class="col-md-12">
      <div class="radio">
        <input id="poll-frontpage-banner-intersticial-800x600"name="type_advertisement" type="radio" value="850" {if isset($advertisement) && $advertisement->type_advertisement == 850}checked="checked" {/if}/>
        <label for="poll-frontpage-banner-intersticial-800x600">
          Banner Intersticial - {t}Polls{/t} (800x600)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="poll-frontpage-left-skyscraper-160x600"name="type_advertisement" type="radio" value="891" {if isset($advertisement) && $advertisement->type_advertisement == 891}checked="checked" {/if}/>
        <label for="poll-frontpage-left-skyscraper-160x600">
          Left Skyscraper (160x600)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="poll-frontpage-right-skyscraper-160x600"name="type_advertisement" type="radio" value="892" {if isset($advertisement) && $advertisement->type_advertisement == 892}checked="checked" {/if}/>
        <label for="poll-frontpage-right-skyscraper-160x600">
          Right Skyscraper (160x600)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="poll-frontpage-big-banner-top-728x90"name="type_advertisement" type="radio" value="801" {if isset($advertisement) && $advertisement->type_advertisement == 801}checked="checked" {/if}/>
        <label for="poll-frontpage-big-banner-top-728x90">
          Big Banner Top (728x90)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="poll-frontpage-banner-top-right-234x90"name="type_advertisement" type="radio" value="802" {if isset($advertisement) && $advertisement->type_advertisement == 802}checked="checked" {/if}/>
        <label for="poll-frontpage-banner-top-right-234x90">
          Banner Top Right (234x90)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6 col-md-offset-6">
      <div class="radio" colspan="2">
        <input id="poll-frontpage-banner1-column-right-I-300x*"name="type_advertisement" type="radio" value="803" {if isset($advertisement) && $advertisement->type_advertisement == 803}checked="checked" {/if}/>
        <label for="poll-frontpage-banner1-column-right-I-300x*">
          Banner1 Column Right (I) (300x*)
        </label>
      </div>
    </div>
    <div class="col-md-6 col-md-offset-6">
      <div class="radio" colspan="2">
        <input id="poll-frontpage-banner2-column-right-II-300x*"name="type_advertisement" type="radio" value="805" {if isset($advertisement) && $advertisement->type_advertisement == 805}checked="checked" {/if}/>
        <label for="poll-frontpage-banner2-column-right-II-300x*">
          Banner2 Column Right(II) (300x*)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="poll-frontpage-big-banner-bottom-728x90"name="type_advertisement" type="radio" value="809" {if isset($advertisement) && $advertisement->type_advertisement == 809}checked="checked" {/if}/>
        <label for="poll-frontpage-big-banner-bottom-728x90">
          Big Banner Bottom (728x90)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="poll-frontpage-banner-bottom-right-234x90"name="type_advertisement" type="radio" value="810" {if isset($advertisement) && $advertisement->type_advertisement == 810}checked="checked" {/if}/>
        <label for="poll-frontpage-banner-bottom-right-234x90">
          Banner Bottom Right (234x90)
        </label>
      </div>
    </div>
  </div>
</div>
<div class="col-md-3">
  <div id="advertisement-mosaic-poll">
    <div id="advertisement-mosaic-frame-poll"></div>
    <img src="{$_template->getImageDir()}/advertisement/right2Ads.png" style="width:100%" usemap="#mapGallery" />
  </div>
</div>
