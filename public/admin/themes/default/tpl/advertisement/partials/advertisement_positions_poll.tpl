<table border="0" cellpadding="4" cellspacing="6" id="ads_type_gallery">
<tbody>
    <tr>
        <td align="right" colspan="2">
            <label>
                Banner Intersticial - Gallery (800X600)
                <input type="radio" name="type_advertisement" value="650" {if isset($advertisement) && $advertisement->type_advertisement == 650}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="7" align="right" width="340">
            <div id="advertisement-mosaic-poll">
                <div id="advertisement-mosaic-frame-poll"></div>
                <img src="{$smarty.const.SITE_URL_ADMIN}/images/advertisement/right2Ads.png" width="240" border="0" usemap="#mapGallery" />
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
                <input type="radio" name="type_advertisement" value="601" {if isset($advertisement) && $advertisement->type_advertisement == 601}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                Banner Top Right (234X90)
                <input type="radio" name="type_advertisement" value="602" {if isset($advertisement) && $advertisement->type_advertisement == 602}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="603" {if isset($advertisement) && $advertisement->type_advertisement == 603}checked="checked" {/if}/>
            </label>
        </td>
    </tr>


    <tr>
        <td height="50" align="right" colspan="2">
            <label>
                Banner2 Column Right(II) (300X*)
                <input type="radio" name="type_advertisement" value="605" {if isset($advertisement) && $advertisement->type_advertisement == 605}checked="checked" {/if}/>
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
                <input type="radio" name="type_advertisement" value="609" {if isset($advertisement) && $advertisement->type_advertisement == 609}checked="checked" {/if}/>
            </label>
        </td>
        <td align="right">
            <label>
                Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="610" {if isset($advertisement) && $advertisement->type_advertisement == 610}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
</tbody>
</table>

