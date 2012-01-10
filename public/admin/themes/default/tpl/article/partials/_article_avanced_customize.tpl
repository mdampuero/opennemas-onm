<style>
.fuente_cuerpo table {
  width:90%;
}
</style>
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo"  width="90%" >
<tbody>
    <tr>
        <td>
            <fieldset> 
                <legend>Boletín</legend>
                <table><tbody>
                    <tr>
                        <td style="padding:4px;" valign="top" colspan="2" >		
                            <label>{t}Agency in Bulletin{/t}</label>
                            <input 	type="text" id="agencyWeb" name="params[agencyBulletin]" title="{t}Agency{/t}"
                             style="width:98%" tabindex="5"
                            {if is_object($article)}
                                value="{$article->params['agencyBulletin']|clearslash|escape:"html"}"
                            {else}
                                value="{setting name=site_agency}"
                            {/if}
                            />
                        </td>
                    </tr>
                </tbody></table>
            </fieldset>   
       </td>
    </tr>
    
    <tr>
        <td>
            <fieldset> 
                <legend>{t}Frontpage section{/t}</legend>
                <table><tbody>
                    <tr>
                        <td valign="top"  style="padding:4px;" >
                            <label for="description">{t}Image position in frontpage{/t}</label>			  
                            <select name="params[imagePosition]" id="img_pos">
                                <option value="left" {if $article->params['imagePosition'] eq "left" || !$article->params['imagePosition']} selected{/if}>Izquierda</option>
                                <option value="right" {if $article->params['imagePosition'] eq "right"} selected{/if}>Derecha</option>
                                <option value="none" {if $article->params['imagePosition'] eq "none"} selected{/if}>Justificada(300px)</option>
                            </select>
                        </td>
                            <td style="padding:4px;" valign="top" >
                            <label>{t}Size for title{/t}</label>

                                <select name="params[titleSize]" id="title_size"   >
                                        {html_options options=$availableSizes selected=$article->params['titleSize']|default:"24"}                                 
                                </select>
                        </td>
                    </tr>   
                </tbody></table>
            </fieldset>   
       </td>
    </tr>
    
    <tr>
        <td>                    
            <fieldset> 
                <legend>{t}Customize for home{/t} </legend>
                <table style="width:100%;"><tbody>
                    <tr>
                        <td style="padding:4px;" valign="top" >
                            <label for="params[titleHome]">{t}Title for Home Frontpage{/t}</label>                 
                            <input 	type="text" id="titleHome" name="params[titleHome]" title="{t}Title for Home{/t}"
                                    style="width:98%" tabindex="5"
                                    {if is_object($article)}
                                        value="{$article->params['titleHome']|clearslash|escape:"html"}"
                                    {else}
                                        value=""
                                    {/if}
                                />
                        </td>

                        <td style="padding:4px;" valign="top" >
                            <label>{t}Size for title in home{/t}</label>
                            <select name="params[titleHomeSize]" id="title_size">
                                 {html_options options=$availableSizes selected=$article->params['titleHomeSize']|default:"24"}                              
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:4px;" valign="top" colspan="2">
                            <label for="params[subtitleHomePosition]">{t}Home Subtitle{/t}</label>
                            <input 	type="text" id="subtitleHome" name="params[subtitleHome]" title="{t}Title for Home{/t}"
                                     style="width:98%" tabindex="5"
                                    {if is_object($article)}
                                        value="{$article->params['subtitleHome']|clearslash|escape:"html"}"
                                    {else}
                                        value=""
                                    {/if}
                                />
                        </td>
                    </tr>

                    <tr>
                        <td valign="top"   style="padding:4px;" colspan="2">
                            <label for="params[summaryHome]">{t}Home summary{/t}</label>
                            <textarea name="params[summaryHome]" id="sumary_home"
                                title="Resumen noticia para home" style="width:100%; height:8em;" tabindex="-1">{$article->params['summaryHome']|clearslash}</textarea>
                        </td>
                    </tr>
                     <tr>
                        <td valign="top"   style="padding:4px;" colspan="2">
                            <label for="params[imageHomePosition]">{t}Image position for Home{/t}</label>
                            <select name="params[imageHomePosition]" id="img_pos" >
                                <option value="left" {if $article->params['imageHomePosition'] eq "left" || !$article->params['imageHomePosition']} selected{/if}>Izquierda</option>
                                <option value="right" {if $article->params['imageHomePosition'] eq "right"} selected{/if}>Derecha</option>
                                <option value="none" {if $article->params['imageHomePosition'] eq "none"} selected{/if}>Justificada(300px)</option>
                           </select>
                        </td>
                    </tr>
                     <tr>
                        <td valign="top"  style="padding:4px;" colspan="2" >
                            <label for="description">{t}Image for home Frontpage{/t}</label>

                            {include file="article/partials/_load_image.tpl"}
                        </td>
                        <td>

                            <div id="photos-home" class="photos" style="float:right;">
                                {*AJAX imageGallery *}
                            </div>                
                            <script type="text/javascript" language="javascript">
                              /*  $('avanced-custom-button').observe('click', function() {

                                    loadGalleryImages('listByCategory','{$category}','','1', 'photos-home');
                                    makeDroppable();

                                });*/
                            </script>

                        </td>
                    </tr>
                    
                </tbody></table>
            </fieldset>   
       </td>
    </tr>   

</tbody>
</table>