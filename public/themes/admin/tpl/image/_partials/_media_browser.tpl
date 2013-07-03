<table class="table table-hover table-condensed">
    {if !($hideheaders)}
    <thead>
        <tr>
            <th style="width:15px"><input type="checkbox" class="toggleallcheckbox"></th>
            <th>{t}Preview{/t}</th>
            <th>{t}Information{/t}</th>
            <th>{t}Created{/t}</th>
            <th class="center" style="width:100px;">{t}Actions{/t}</th>
        </tr>
    </thead>
    {/if}
    <tbody>
    {foreach name=n from=$photos item=photo}
    {$allowed = 'false'}
    {if $photo->category_name eq 'publicidad'}
        {if $adsModule eq 'true'}
        {$allowed = 'true'}
        {/if}
    {else}
        {acl hasCategoryAccess=$photo->category}
            {$allowed = 'true'}
        {/acl}
    {/if}
    {if $allowed eq 'true'}
    <tr>
        <td>
            <input type="checkbox"  id="selected_{$smarty.section.n.iteration}" name="selected_fld[]" value="{$photo->id}" class ="minput" />
        </td>
        <td style="width:50px;" class="thumb">
            {if preg_match('/^swf$/i', $photo->type_img)}
                <object>
                    <param name="wmode" value="window"
                           value="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}" />
                    <embed wmode="window"
                           src="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}"
                           width="140" height="80" ></embed>
                </object>
                <img class="image-preview" style="width:16px;height:16px;border:none;"  src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/flash.gif" />
            {elseif preg_match('/^(jpeg|jpg|gif|png)$/i', $photo->type_img)}
                {dynamic_image src="{$photo->path_file}{$photo->name}" transform="thumbnail,160,160" class="image-preview" rel="#image-{$smarty.foreach.n.index}"}
            {/if}
            <div class="simple_overlay modal hide fade" id="image-{$smarty.foreach.n.index}">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                  <h3>{t}Image preview{/t}</h3>
                </div>
                <div class="modal-body">
                    <div class="resource">
                        {if preg_match('/^swf$/i', $photo->type_img)}
                            <object>
                                <param name="wmode" value="window" value="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}" />
                                <embed wmode="window" width="400" height="400"
                                       src="{$MEDIA_IMG_URL}{$photo->path_file}{$photo->name}"></embed>
                            </object>
                            <img style="width:16px;height:16px;border:none;"  src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/flash.gif" />
                        {elseif preg_match('/^(jpeg|jpg|gif|png)$/i', $photo->type_img)}
                            <img src='{$MEDIA_IMG_URL}{$photo->path_file}/{$photo->name}'/>
                        {/if}
                    </div>

                    <div class="details">
                        <h4>{if !empty($photo->description)}{$photo->description|clearslash}{else}{t}No available description{/t}{/if}</h4>
                        <div><strong>{t}Filename{/t}</strong> {$photo->title}</div>
                        <div><img src="{$params.IMAGE_DIR}tag_red.png" /> {if !empty($photo->metadata)}{$photo->metadata|clearslash}{else}{t}No tags{/t}{/if}</div>
                        {if !empty($photo->author_name)}
                            <div><span class="author"><strong>{t}Author:{/t}</strong> {$photo->author_name|clearslash|default:""}</span></div>
                        {/if}
                        <div><strong>{t}Created{/t}</strong> {$photo->date|date_format:"%Y-%m-%d %H:%M:%S"|default:""}</div>

                        <div><strong>{t}Resolution:{/t}</strong> {$photo->width} x {$photo->height} (px)</div>
                        <div><strong>{t}Size:{/t}</strong> {$photo->size} Kb</div>
                    </div>
                </div>
            </div>
        </td>
        <td>
            {if !empty($photo->description)}{$photo->description|clearslash}{else}{t}No available description{/t}{/if}
            <br>
            <span class="tags"><img src="{$params.IMAGE_DIR}tag_red.png" /> {if !empty($photo->metadata)}{$photo->metadata|clearslash}{else}{t}No tags{/t}{/if}</span>
            {if !empty($photo->author_name)}
                <span class="author"><strong>{t}Author:{/t}</strong> {$photo->author_name|clearslash|default:""}</span>
            {/if}
            <br>
            {if preg_match('@^/authors/@', $photo->path_file)}
                <span class="url">
                    <a href="{$MEDIA_IMG_URL}{$photo->path_file}/{$photo->name}" target="_blank">
                        {t}[Link]{/t}
                    </a>
                </span>
            {else}
                <span class="url">
                    <a href="{$MEDIA_IMG_URL}{$photo->path_file}/{$photo->name}" target="_blank">
                        {t}[Link]{/t}
                    </a>
                </span>
            {/if}
        </td>
        <td class="nowrap">
            {$photo->date|date_format:"%Y-%m-%d %H:%M:%S"|default:""}
        </td>
        <td class="right">
            <div class="btn-group">
                {acl isAllowed="IMAGE_UPDATE"}
                <a class="edit-button btn" href="{url name=admin_image_show}?id[]={$photo->pk_photo}">
                    <i class="icon-pencil"></i> {t}Edit{/t}
                </a>
                {/acl}
                {acl isAllowed="IMAGE_DELETE"}
                <a class="del btn btn-danger" data-title="{$photo->title}" data-url="{url name=admin_image_delete id=$photo->pk_photo page=$page}" href="{url name=admin_image_delete id=$photo->pk_photo page=$page}" title="{t}Delete{/t}">
                    <i class="icon-trash icon-white"></i>
                </a>
                {/acl}
            </div>
        </td>
    </tr>
    {/if}
    {foreachelse}
    <tr>
        <td class="empty">
            <p>
                <img src="{$params.IMAGE_DIR}/search/search-images.png">
            </p>
            {t escape=off}No available images<br> for this search{/t}
        </td>
    </tr>
    {/foreach}
    </tbody>
    <tfoot>
        <tr>
            <td class="center" colspan="5">
                <div class="pagination">{$pages->links}</div>
            </td>
        </tr>
    </tfoot>
</table>