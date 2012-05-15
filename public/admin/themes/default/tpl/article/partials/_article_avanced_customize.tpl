<style>
 .custom-data {
     width:90%;
 }
 .custom-data td {
      padding-right:5px;
 }
</style>
<table class="custom-data" >
<tbody>
    <tr>
        <td>
            <fieldset>
                <legend>{t}Customize for section Frontpage{/t}</legend>
                <table><tbody>
                    <tr>
                        <td>
                            <label>{t}Size for title{/t}</label>
                            {if isset($article->params['titleSize'])}
                                {assign var=defaultValue value=$article->params['titleSize']}
                            {else}
                                {assign var=defaultValue value=24}
                            {/if}
                            <select name="params[titleSize]">
                                {html_options values=$availableSizes options=$availableSizes selected=$defaultValue}
                            </select>
                        </td>
                        <td></td>
                        <td >
                            <label for="description">{t}Image position{/t}</label>
                            <select name="params[imagePosition]" id="img_home_pos">
                                <option value="right" {if $article->params['imagePosition'] eq "right" || !$article->params['imagePosition']} selected{/if}>Derecha</option>
                                <option value="left" {if $article->params['imagePosition'] eq "left"} selected{/if}>Izquierda</option>
                                <option value="none" {if $article->params['imagePosition'] eq "none"} selected{/if}>Justificada(300px)</option>
                            </select>
                        </td>
                    </tr>

                </tbody></table>
            </fieldset>
       </td>
    </tr>
     <tr><td><hr></td></tr>
    <tr>
        <td>
            <fieldset>
                <legend>{t}Customize for Home Frontpage{/t} </legend>
                <table style="width:100%;">
                    <tbody>
                        <tr>
                            <td>
                                <label for="titleHome">{t}Title{/t}</label>
                                <input 	type="text" id="titleHome" name="params[titleHome]" title="{t}Title for Home frontpage{/t}"
                                        style="width:90%" tabindex="5"
                                        {if is_object($article)}
                                            value="{$article->params['titleHome']|clearslash|escape:"html"}"
                                        {else}
                                            value=""
                                        {/if}
                                    />
                            </td>
                            <td>
                                <label>{t}Size{/t}</label>
                                <select name="params[titleHomeSize]">
                                    {if isset($article->params['titleHomeSize'])}
                                        {assign var=defaultValue value=$article->params['titleHomeSize']}
                                    {else}
                                        {assign var=defaultValue value=26}
                                    {/if}
                                     {html_options values=$availableSizes options=$availableSizes selected=$defaultValue}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label for="subtitleHome">{t}Subtitle{/t}</label>
                                <input 	type="text" id="subtitleHome" name="params[subtitleHome]" title="{t}Title for Home{/t}"
                                         style="width:90%" tabindex="5"
                                        {if is_object($article)}
                                            value="{$article->params['subtitleHome']|clearslash|escape:"html"}"
                                        {else}
                                            value=""
                                        {/if}
                                    />
                            </td>
                        </tr>

                        <tr>
                            <td  colspan="2">
                                <label for="sumary_home">{t}Summary{/t}</label>
                                <textarea name="params[summaryHome]" id="sumary_home"
                                    title="Resumen noticia para home" style="width:100%; height:8em;" tabindex="-1">{$article->params['summaryHome']|clearslash}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td  colspan="2">
                                <label for="img_pos">{t}Image position{/t}</label>
                                <select name="params[imageHomePosition]" id="img_pos" >
                                    <option value="right" {if $article->params['imageHomePosition'] eq "right" || !$article->params['imageHomePosition']} selected{/if}>Derecha</option>
                                    <option value="left" {if $article->params['imageHomePosition'] eq "left"} selected{/if}>Izquierda</option>
                                    <option value="none" {if $article->params['imageHomePosition'] eq "none"} selected{/if}>Justificada(300px)</option>
                               </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
       </td>
    </tr>

</tbody>
</table>