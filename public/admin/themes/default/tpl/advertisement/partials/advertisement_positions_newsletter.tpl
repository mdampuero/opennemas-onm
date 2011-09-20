<table border="0" cellpadding="4" cellspacing="6" id="ads_type_gallery">
<tbody>
    <tr>
        <td align="right" colspan="2">

        </td>
        <td rowspan="7" align="right" width="340">

            <div id="advertisement-mosaic-newsletter">
                <div id="advertisement-mosaic-frame-newsletter"></div>
                <img src="{$smarty.const.SITE_URL_ADMIN}/images/advertisement/newsletter.png" width="240" border="0" usemap="#mapGallery" />
            </div>
        </td>
    </tr>
     <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td align="right">
            <label>
                Big Banner Top (728X90)
                <input type="radio" name="type_advertisement" value="1001" {if isset($advertisement) && $advertisement->type_advertisement == 1001}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">

        </td>
    </tr>

    <tr>
        <td colspan="2"><hr /></td>
    </tr>

    <tr>
        <td align="right">
            <label>
                Big Banner Bottom (728X90)
                <input type="radio" name="type_advertisement" value="1009" {if isset($advertisement) && $advertisement->type_advertisement == 1009}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">

        </td>
    </tr>
</tbody>
</table>
 