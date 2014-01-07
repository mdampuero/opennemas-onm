<div id="photo-{$photo->id}" class="form-vertical clearfix photo-edit">

    <div style="width:330px; display:inline-block;" class="pull-left clearfix">
        <div class="thumbnail">
            {if preg_match('/^swf$/i', $photo->type_img)}
                <object width="" height="">
                    <param name="wmode" value="window" value="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}"/>
                    <embed wmode="window" src="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}"></embed>
                </object>
            {else}
                {dynamic_image src="{$photo->path_file}/{$photo->name}" transform="thumbnail,330,330" onunload="GUnload()"}
            {/if}
        </div>

        <br>

        <div class="well well-small">
            <div><strong>{t}Original filename:{/t}</strong> {$photo->title}</div>
            <div><strong>{t}Resolution:{/t}</strong> {$photo->width}px x {$photo->height}px</div>
            <div><strong>{t}Size:{/t}</strong> {$photo->size} Kb</div>
        </div>

    </div>

    <div class="photo-basic-information">

        <div>
            <div class="control-group">
                <label class="control-label">{t}Link{/t}</label>
                <div class="controls">
                    <a href="{$MEDIA_IMG_URL}{$photo->path_file}/{$photo->name}" target="_blank">{$smarty.const.SITE_URL}{$MEDIA_IMG_URL}{$photo->path_file}/{$photo->name}</a>
                </div>
            </div>
            <div class="control-group">
                <label for="description-{$photo->id}" class="control-label">{t}Description{/t}</label>
                <div class="controls">
                    <textarea required="required" id="description-{$photo->id}" name="description[{$photo->id}]"  class="input-xxlarge"
                        rows="2">{$photo->description|clearslash}</textarea>
                </div>
                <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery('#description-{$photo->id}').on('change', function(e, ui) {
                        fill_tags(jQuery('#description-{$photo->id}').val(),'#metadata-{$photo->id}', '{url name=admin_utils_calculate_tags}');
                    });
                });
                </script>
            </div>
            <div class="control-group">
                <label for="metadata-{$photo->id}" class="control-label">{t}Keywords{/t}</label>
                <div class="controls">
                    <textarea id="metadata-{$photo->id}" name="metadata[{$photo->id}]" rows="2"  class="input-xxlarge" required="required">{$photo->metadata|strip}</textarea>
                    <div class="help-block">{t}Used for searches and automated suggestions.{/t}</div>
                </div>
            </div>

            <div class="control-group">
                <label for="author_name[{$photo->id}]" class="control-label">{t}Copyright{/t}</label>
                <div class="controls">
                    <input type="text" id="author_name[{$photo->id}]" name="author_name[{$photo->id}]"
                        value='{$photo->author_name|clearslash}'/>
                </div>
            </div>

            <div class="control-group">
                <label for="date-{$photo->id}" class="control-label">{t}Date{/t}</label>
                <div class="controls">
                    <input class="date" type="text" id="date-{$photo->id}" name="date[{$photo->id}]"
                        value='{$photo->date|date_format:"%Y-%m-%d %H:%M:%S"}'/>
                </div>
            </div>

            <div class="control-group">
                <label for="geolocation" class="control-label">{t}Location{/t}</label>
                <div class="controls">
                    <div class="input-append">
                        <input type="text" id="address_{$photo->id}" name="address[{$photo->id}]" value="{$photo->address}" class="photo_address">
                        <button class="locate btn btn-default" data-image-id="{$photo->id}" data-toggle="modal" href="#modal-image-location"><i class="icon-screenshot"></i></button>
                    </div>

                </div>
            </div>
        </div>
        <div class="iptc-exif">
            <h5 class="toggler" title=""><i class="icon-plus"></i> {t escape=off}View advanced data{/t}</h5>
            <div class="info">
                {if is_null($photo->exif) neq true}
                <h6>{t}EXIF Data{/t}</h6>
                <div id="exif" class="photo-static-info">
                    {foreach $photo->exif as $name => $value}
                        {foreach $value as $d => $dato}
                            <p>
                                <strong>{$d}</strong> : {$dato}
                            </p>
                        {/foreach}
                    {/foreach}
                </div>
                {else}
                <div id="exif" class="photo-static-info">
                    {t}No available EXIF data.{/t}
                </div>
                {/if}

                {if isset($photo->myiptc)}
                <h6>{t}IPTC Data{/t}</h6>
                <div id="iptc" class="photo-static-info">
                    {foreach $photo->myiptc as $name => $value}
                    {if $name}
                        <p>
                            <strong>{$name}</strong> : {$value}
                        </p>
                    {/if}
                    {/foreach}
                  <br />
                </div>
                {else}
                <div id="iptc" class="photo-static-info">
                    {t}No available IPTC data.{/t}
                </div>
                {/if}
            </div><!-- /additional-info -->
        </div>
    </div><!-- /basic -->

    <input type="hidden" name="resolution[{$photo->id}]" value="{$photo->resolution}" />
    <input type="hidden" name="title[{$photo->id}]" value="{$photo->name}" />
    <input type="hidden" name="category[{$photo->id}]" value="{$photo->category}" />

</div><!-- /photo-{$rnf} -->
<hr>

