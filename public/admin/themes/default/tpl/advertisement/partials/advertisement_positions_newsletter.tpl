<table id="ads_type_newsletter">
<tbody>
    <tr>
        <td colspan="2">

        </td>
        <td rowspan="5">
            <div id="advertisement-mosaic-newsletter">
                <div id="advertisement-mosaic-frame-newsletter"></div>
                <img src="{$params.IMAGE_DIR}/advertisement/newsletter.png" width="240" usemap="#mapGallery" />
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td>
            <label>
                Big Banner Top (728X90)
                <input type="radio" name="type_advertisement" value="1001" {if isset($advertisement) && $advertisement->type_advertisement == 1001}checked="checked" {/if}/>
            </label>
        </td>
        <td>

        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td>
            <label>
                Big Banner Bottom (728X90)
                <input type="radio" name="type_advertisement" value="1009" {if isset($advertisement) && $advertisement->type_advertisement == 1009}checked="checked" {/if}/>
            </label>
        </td>
        <td>

        </td>
    </tr>
</tbody>
</table>
