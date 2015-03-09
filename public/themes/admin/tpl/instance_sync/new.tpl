{extends file="base/admin.tpl"}

{block name="header-css" append}
{stylesheets src="@AdminTheme/js/jquery/jquery_colorpicker/css/colorpicker.css" filters="cssrewrite"}
<link rel="stylesheet" href="{$asset_url}">
{/stylesheets}
<style>
  #connect {
    cursor:pointer;
  }
  #connect button {
    background: none;
    border:0 none;
  }
  #loading {
    display: none
  }
</style>
{/block}

{block name="footer-js" append}
{javascripts src="@AdminTheme/js/jquery/jquery_colorpicker/js/colorpicker.js"}
<script type="text/javascript" src="{$asset_url}"></script>
{/javascripts}

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#connect').on('click',function(e){
      e.preventDefault();
      var url = $('#site_url').serialize();

      $.ajax({
        type: 'POST',
        url: '{url name=admin_instance_sync_fetch_categories}',
        data: url,
        dataType: 'html',
        beforeSend: function() {
          $('#loading').show();
        }
      }).success(function(data) {
        $('#categories').html(data).show();

        $('#loading').hide();
        $('#colorDiv').show();
      });
  });



  //   jQuery('#color-picker').ColorPicker({
  //     onSubmit: function(hsb, hex, rgb, el) {
  //       jQuery(el).val(hex);
  //       jQuery(el).ColorPickerHide();
  //     },
  //     onChange: function (hsb, hex, rgb) {
  //       jQuery('.colorpicker_viewer').css('background-color', '#' + hex);
  //     },
  //     onBeforeShow: function () {
  //       jQuery(this).ColorPickerSetColor(this.value);
  //     }
  //   }).bind('keyup', function(){
  //     jQuery(this).ColorPickerSetColor(this.value);
  //   });

  // });

    $(document).ready(function($) {
      var color = $('.colorpicker_viewer');
      var inpt  = $('#color');
      var btn   = $('.reset-button');

      inpt.ColorPicker({
        onSubmit: function(hsb, hex, rgb, el) {
          $(el).val(hex);
          $(el).ColorPickerHide();
        },
        onChange: function (hsb, hex, rgb) {
          inpt.val(hex);
          color.css('background-color', '#' + hex);
          color.css('border-color', '#' + hex);
        },
        onBeforeShow: function () {
          $(this).ColorPickerSetColor(this.value);
        }
      })
      .bind('keyup', function(){
        $(this).ColorPickerSetColor(this.value);
      });

      btn.on('click', function(e, ui){
        inpt.val( '{setting name="site_color"}' );
        color.css('background-color', '#' + '{setting name="site_color"}');
        color.css('border-color', '#' + '{setting name="site_color"}');
        e.preventDefault();
      });
    });
});
</script>
{/block}

{block name="content"}
<form action="{url name=admin_instance_sync_create}" method="POST" name="formulario" id="formulario">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-exchange"></i>
              <span class="hidden-xs">{t}Instance Synchronization{/t}</span>
              <span class="visible-inline-xs">{t}Ins. Sync.{/t}</span>
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs">
            <h5>{t}Adding site{/t}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_instance_sync}" title="{t}Go back to list{/t}">
                <i class="fa fa-reply"></i>
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <button class="btn btn-primary" type="submit">
                <i class="fa fa-save"></i>
                {t}Save{/t}
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    {render_messages}
    <div class="grid simple ">
      <div class="grid-body">
        <div class="form-group">
          <label for="site_url" class="form-label">{t}Site URL{/t}</label>
          <div class="controls">
            <div class="input-group">
              <input type="text" required="required" name="site_url" id="site_url" value="{$site_url}" placeholder="{t}http://example.com{/t}" class="form-control">
              <span class="input-group-addon primary" id="connect">
                <span class="arrow"></span>
                <i class="fa fa-plug"></i>
                <button class="link" type="button">{t}Connect{/t}</button>
              </span>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="site_color" class="form-label">{t}Site color{/t}</label>
          <div class="input-group">
            <span class="colorpicker_viewer input-group-addon" id="colorpicker_viewer" style="background-color:#{$site_color|default:"#000"|trim}">
              &nbsp;&nbsp;&nbsp;&nbsp;
            </span>
            <input class="form-control" size="6" type="text" id="color" name="site_color" value="{$site_color|default:"#000"|trim}">
            <span class="input-group-btn">
              <button class="btn btn-default reset-button">
                {t}Reset color{/t}
              </button>
            </span>
          </div>
        </div>

        <div id="categories">
          {include file="instance_sync/partials/_list_categories.tpl"}
          <div class="spinner-wrapper" id="loading">
            <div class="loading-spinner"></div>
            <div class="spinner-text">{t}Loading{/t}...</div>
          </div>
        </div>

      </div>

    </div>
  </div>
</form>
{/block}
