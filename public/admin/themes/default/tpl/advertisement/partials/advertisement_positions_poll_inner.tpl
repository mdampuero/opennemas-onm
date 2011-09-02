<table border="0" cellpadding="4" cellspacing="6" id="ads_type_gallery">
<tbody>
    <tr>
        <td align="right" colspan="2">
            <label>
                Banner Intersticial - Gallery (800X600)
                <input type="radio" name="type_advertisement" value="750" {if isset($advertisement) && $advertisement->type_advertisement == 750}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="7" align="right" width="340">
             <div id="advertisement-mosaic-poll-inner">
                <div id="advertisement-mosaic-frame-poll-inner"></div>
                <img src="{$smarty.const.SITE_URL_ADMIN}/images/advertisement/right1Ads.png" width="240" border="0" usemap="#mapGallery" />
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
                <input type="radio" name="type_advertisement" value="701" {if isset($advertisement) && $advertisement->type_advertisement == 701}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                Banner Top Right (234X90)
                <input type="radio" name="type_advertisement" value="702" {if isset($advertisement) && $advertisement->type_advertisement == 702}checked="checked" {/if}/>
            </label>
        </td>
    </tr>

    <tr>
        <td colspan="2"><hr /></td>
    </tr>

    <tr>
        <td align="right" colspan="2">
            <label>
                Banner1 Column Right (I) (300X*)
                <input type="radio" name="type_advertisement" value="703" {if isset($advertisement) && $advertisement->type_advertisement == 703}checked="checked" {/if}/>
            </label>
        </td>
    </tr>


    <tr>
        <td colspan="2"><hr /></td>
    </tr>

    <tr>
        <td align="right">
            <label>
                Big Banner Bottom (728X90)
                <input type="radio" name="type_advertisement" value="709" {if isset($advertisement) && $advertisement->type_advertisement == 709}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="710" {if isset($advertisement) && $advertisement->type_advertisement == 710}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
</tbody>
</table>

