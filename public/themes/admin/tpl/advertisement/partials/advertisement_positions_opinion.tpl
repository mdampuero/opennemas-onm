<div class="col-md-9">
  <div class="row">
    <div class="col-md-12">
      <div class="radio">
        <input id="opition-frontpage-banner-intersticial-800x600" name="type_advertisement" type="radio" value="650" {if isset($advertisement) && $advertisement->type_advertisement == 650}checked="checked" {/if}/>
        <label for="opition-frontpage-banner-intersticial-800x600">
          Banner Intersticial - Opinion Frontpage (800x600)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="opinion-frontpage-left-skyscraper-160x600" name="type_advertisement" type="radio" value="691" {if isset($advertisement) && $advertisement->type_advertisement == 691}checked="checked" {/if}/>
        <label for="opinion-frontpage-left-skyscraper-160x600">
          Left Skyscraper (160x600)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="opinion-frontpage-right-skyscraper-160x600" name="type_advertisement" type="radio" value="692" {if isset($advertisement) && $advertisement->type_advertisement == 692}checked="checked" {/if}/>
        <label for="opinion-frontpage-right-skyscraper-160x600">
          Right Skyscraper (160x600)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="opinion-frontpage-big-banner-top-728x90" name="type_advertisement" type="radio" value="601" {if isset($advertisement) && $advertisement->type_advertisement == 601}checked="checked" {/if}/>
        <label for="opinion-frontpage-big-banner-top-728x90">
          Big Banner Top (728x90)
        </label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="opinion-frontpage-banner-top-right-234x90" name="type_advertisement" type="radio" value="602" {if isset($advertisement) && $advertisement->type_advertisement == 602}checked="checked" {/if}/>
        <label for="opinion-frontpage-banner-top-right-234x90">
          Banner Top  Right (234X90)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6 col-md-offset-6">
      <div class="radio">
        <input id="opinion-frontpage-banner-column-right-300x*" name="type_advertisement" type="radio" value="603" {if isset($advertisement) && $advertisement->type_advertisement == 603}checked="checked" {/if}/>
        <label for="opinion-frontpage-banner-column-right-300x*">
          Banner Column Right (300x*)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6 col-md-offset-6">
      <div class="radio">
        <input id="opinion-frontpage-banner-column-right-2-300x*" name="type_advertisement" type="radio" value="605" {if isset($advertisement) && $advertisement->type_advertisement == 605}checked="checked" {/if}/>
        <label for="opinion-frontpage-banner-column-right-2-300x*">
          Banner Column Right 2 (300x*)
        </label>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-md-6">
      <div class="radio">
        <input id="opinion-frontpage-big-banner-bottom-728x90" name="type_advertisement" type="radio" value="609" {if isset($advertisement) && $advertisement->type_advertisement == 609}checked="checked" {/if}/>
        <label for="opinion-frontpage-big-banner-bottom-728x90">
          Big Banner Bottom (728X90)
        </label>
        </div>
    </div>
    <div class="col-md-6">
      <div class="radio">
        <input id="opinion-frontpage-banner-bottom-right-234x90" name="type_advertisement" type="radio" value="610" {if isset($advertisement) && $advertisement->type_advertisement == 610}checked="checked" {/if}/>
        <label for="opinion-frontpage-banner-bottom-right-234x90">
          Banner Bottom Right (234X90)
        </label>
      </div>
    </div>
  </div>
</div>
<div class="col-md-3">
  <div id="advertisement-mosaic-opinion" style="">
    <div id="advertisement-mosaic-frame-opinion"></div>
    <img src="{$_template->getImageDir()}/advertisement/right2Ads.png" style="width:100%" usemap="#mapOpinion" />
  </div>
</div>
