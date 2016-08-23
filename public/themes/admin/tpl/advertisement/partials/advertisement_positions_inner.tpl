<div class="row">
  <div class="col-md-9">
    <div class="row">
      <div class="col-md-12">
        <div class="radio">
          <input id="inner-banner-intersticial-inner-800x600" name="type_advertisement" type="radio" value="150" {if $advertisement->type_advertisement == 150}checked="checked" {/if}/>
          <label for="inner-banner-intersticial-inner-800x600">
            Banner Intersticial - Inner (800X600)
          </label>
        </div>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-md-6">
        <div class="radio">
          <input id="inner-left-skyscraper-160x600" name="type_advertisement" type="radio" value="191" {if isset($advertisement) && $advertisement->type_advertisement == 191}checked="checked" {/if}/>
          <label for="inner-left-skyscraper-160x600">
            Left Skyscraper (160x600)
          </label>
        </div>
      </div>
      <div class="col-md-6">
        <div class="radio">
          <input id="inner-right-skysraper-160x600" name="type_advertisement" type="radio" value="192" {if isset($advertisement) && $advertisement->type_advertisement == 192}checked="checked" {/if}/>
          <label for="inner-right-skysraper-160x600">
            Right Skyscraper (160x600)
          </label>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="radio">
          <input id="inner-inbody-skyscraper-120x600" name="type_advertisement" type="radio" value="193" {if isset($advertisement) && $advertisement->type_advertisement == 193}checked="checked" {/if}/>
          <label for="inner-inbody-skyscraper-120x600">
            InBody Skyscraper (120x600)
          </label>
        </div>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-md-6">
        <div class="radio">
          <input id="inner-big-banner-top-I-728x90" name="type_advertisement" type="radio" value="101" {if $advertisement->type_advertisement == 101}checked="checked" {/if}/>
          <label for="inner-big-banner-top-I-728x90">
            Big  Banner Top(I) (728X90)
          </label>
        </div>
      </div>
      <div class="col-md-6">
        <div class="radio">
          <input id="inner-banner-top-right-II-234x90" name="type_advertisement" type="radio" value="102" {if $advertisement->type_advertisement == 102}checked="checked" {/if}/>
          <label for="inner-banner-top-right-II-234x90">
            Banner Top Right(I) (234X90)
          </label>
        </div>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-md-6">
        <div class="radio">
          <input id="inner-robapagina-650x*" name="type_advertisement" type="radio" value="104" {if $advertisement->type_advertisement == 104}checked="checked" {/if}/>
          <label for="inner-robapagina-650x*">
            Robapágina (650x*)
          </label>
        </div>
      </div>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-12">
            <div class="radio">
              <input id="inner-banner1-column-right-I-300x*" name="type_advertisement" type="radio" value="103" {if $advertisement->type_advertisement == 103}checked="checked" {/if}/>
              <label for="inner-banner1-column-right-I-300x*">
                Banner1 Column Right(I) (300x*)
              </label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="radio">
              <input id="inner-banner2-column-right-I-300x*" name="type_advertisement" type="radio" value="105" {if $advertisement->type_advertisement == 105}checked="checked" {/if}/>
              <label for="inner-banner2-column-right-I-300x*">
                Banner2 Column Right(I) (300x*)
              </label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="radio">
              <input id="inner-banner3-column-right-I-300x*" name="type_advertisement" type="radio" value="106" {if $advertisement->type_advertisement == 106}checked="checked" {/if}/>
              <label for="inner-banner3-column-right-I-300x*">
                Banner3 Column Right(I) (300x*)
              </label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="radio">
              <input id="inner-banner4-column-right-I-300x*" name="type_advertisement" type="radio" value="107" {if $advertisement->type_advertisement == 107}checked="checked" {/if}/>
              <label for="inner-banner4-column-right-I-300x*">
                Banner4 Column Right(I) (300x*)
              </label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="radio">
              <input id="inner-banner5-column-right-I-300x*" name="type_advertisement" type="radio" value="108" {if $advertisement->type_advertisement == 108}checked="checked" {/if}/>
              <label for="inner-banner5-column-right-I-300x*">
                Banner5 Column Right(I) (300x*)
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-md-6">
        <div class="radio">
          <input id="inner-big-banner-bottom-I-728x90" name="type_advertisement" type="radio" value="109" {if $advertisement->type_advertisement == 109}checked="checked" {/if}/>
          <label for="inner-big-banner-bottom-I-728x90">
            Big Banner Bottom(I) (728X90)
          </label>
        </div>
      </div>
      <div class="col-md-6">
        <div class="radio">
          <input id="inner-banner-bottom-right-I-234x90" name="type_advertisement" type="radio" value="110" {if $advertisement->type_advertisement == 110}checked="checked" {/if}/>
          <label for="inner-banner-bottom-right-I-234x90">
            Banner Bottom Right(I) (234X90)
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div id="advertisement-mosaic-interior" style="">
      <div id="advertisement-mosaic-interior-frame"></div>
      <img src="{$_template->getImageDir()}/advertisement/ArticleAds.png" style="width:100%" usemap="#mapInterior" />
    </div>
  </div>
</div>
