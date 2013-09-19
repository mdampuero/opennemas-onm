{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script type="text/javascript">
function toggleAdvanced() {
    var results = $$('tr.advanced');
    results.each(function(elem){
        elem.toggleClassName('nodisplay');
    });
}
</script>
{/block}



{block name="footer-js" append}
    {script_tag src="/jquery/jquery-ui-timepicker-addon.js"}
    <script>
    jQuery(document).ready(function() {
        jQuery('#starttime').datetimepicker({
            hourGrid: 4,
            showAnim: "fadeIn",
            dateFormat: 'yy-mm-dd',
            timeFormat: 'hh:mm:ss',
            minuteGrid: 10
        });
        jQuery('#endtime').datetimepicker({
            hourGrid: 4,
            showAnim: "fadeIn",
            dateFormat: 'yy-mm-dd',
            timeFormat: 'hh:mm:ss',
            minuteGrid: 10
        });

        jQuery('#ui-datepicker-div').css('clip', 'auto');

    });
</script>
{/block}

{block name="content"}
<form action="{url name=admin_images_search}" method="GET">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{if is_null($smarty.get.string_search)} {t}Search images{/t}{else}{t}Search result{/t}{/if} </h2></div>
            <ul class="old-button">
                <li>
                    <a class="admin_add" href="{url name=admin_images}" name="submit_mult">
                        <img border="0" style="width:50px;"  src="{$params.IMAGE_DIR}previous.png" alt="Información"><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        <div class="search clearfix">
            <div class="search-results">
                {if !is_null($smarty.get.string_search)}
                    {include file="image/_partials/_media_browser.tpl" hideheaders=false}
                {else}
                    <div class="empty">
                        <p>
                            <img src="{$params.IMAGE_DIR}/search/search-images.png">
                        </p>
                        {t escape="off"}Please fill the form of<br> the side to search images{/t}
                    </div><!-- / -->
                {/if}
            </div><!-- /search -->
            <div class="search-form">
                <div>
                    <input type="search" name="string_search" value="{$smarty.request.string_search}" style="width:95%;" placeholder="{t}Image name{/t}">
                    &nbsp;
                    <button type="submit" class="onm-button red submit" style="width:100%;"><i class="icon-search icon-white"></i></button>

                    <label for="starttime">{t}Date period:{/t}</label>
                    <input type="datetime" id="starttime" name="starttime" value="{$search_criteria['starttime']}"   placeholder="{t}From{/t}"  class="inline"/>
                    <input type="datetime" id="endtime" name="endtime" value="{$search_criteria['endtime']}"  placeholder="{t}To{/t}"  class="inline"/>


                    <label for="min_width">{t}Width (px.){/t}</label>
                    <input type="number" id="min_width" name="min_width" placeholder="{t}Min{/t}" value="{$search_criteria['minWidth']}" class="inline"/>
                    <input type="number" id="max_width" name="max_width" placeholder="{t}Max{/t}" value="{$search_criteria['maxWidth']}" class="inline"/>

                    <label for="min_height">{t}Height (px.){/t}</label>
                    <input type="number" id="min_height" name="min_height" placeholder="{t}Min{/t}" value="{$search_criteria['minHeight']}" class="inline"/>
                    <input type="number" id="max_height" name="max_height" placeholder="{t}Max{/t}" value="{$search_criteria['maxHeight']}" class="inline"/>

                    <label for="min_weight">{t}Weight (kB){/t}</label>
                    <input type="number" id="min_weight" name="min_weight" placeholder="{t}Min{/t}" value="{$search_criteria['maxWeight']}" class="inline"/>
                    <input type="number" id="max_weight" name="max_weight" placeholder="{t}Max{/t}" value="{$search_criteria['maxWeight']}" class="inline"/>

                    <label for="type">{t}Type:{/t}</label>
                    <select name="type" id="type" />
                        <option value="" selected >{t}-- All --{/t}</option>
                        <option value="jpg" >JPG</option>
                        <option value="gif" >GIF</option>
                        <option value="png" >PNG</option>
                        <option value="svg" >SVG</option>
                        <option value="swf" >SWF</option>
                        <option value="otros" >{t}Others{/t}</option>
                    </select>
                    <label for="color">{t}Color:{/t}</label>
                    <select name="color" id="color" />
                        <option value="" {if empty($search_criteria['color'])}selected{/if}>{t} - All types - {/t}</option>
                        <option value="bn" {if $search_criteria['color'] == 'bn'}selected{/if}>{t}Black and white{/t}</option>
                        <option value="color" {if $search_criteria['color'] == 'color'}selected{/if}>{t}Color{/t}</option>
                    </select>

                    <label for="author">{t}Author:{/t}</label>
                    <input type="text" id="author" name="author" value="{$search_criteria['author']}" size="15" />
                </div><!-- /search-form -->
            </div>
        </div><!-- /search -->
    </div>
</form>

{/block}
