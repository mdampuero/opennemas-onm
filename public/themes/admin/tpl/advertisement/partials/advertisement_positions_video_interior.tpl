
<table id="ads_type_interior_video">
<tbody>
    <tr>
        <td colspan="2">
            <label>
                Banner Intersticial - Video Inner (800X600)
                <input type="radio" name="type_advertisement" value="350" {if isset($advertisement) && $advertisement->type_advertisement == 350}checked="checked" {/if}/>
            </label>
        </td>
        <td rowspan="7">
            <div id="advertisement-mosaic-videointerior" style=" ">
                <div id="advertisement-mosaic-videointerior-frame"></div>
                <img src="{$params.IMAGE_DIR}advertisement/videoAds.png" width="240" height="435" usemap="#mapVideoInterior" />
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td>
            <label>
                 Left Skyscraper (160 x 600)
                <input type="radio" name="type_advertisement" value="391" {if isset($advertisement) && $advertisement->type_advertisement == 391}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                 Right Skyscraper (160 x 600)
                <input type="radio" name="type_advertisement" value="392" {if isset($advertisement) && $advertisement->type_advertisement == 392}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td>
            <label>
                Big Banner Top (728X90)
                <input type="radio" name="type_advertisement" value="301" {if isset($advertisement) && $advertisement->type_advertisement == 301}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                 Banner Top Right (234X90)
                <input type="radio" name="type_advertisement" value="302" {if isset($advertisement) && $advertisement->type_advertisement == 302}checked="checked" {/if}/>
            </label>
        </td>
    </tr>
     <tr>
        <td colspan="2"><hr /></td>
    </tr>
    <tr>
        <td colspan="2">
            <label>
                Button Column (265x95)
                <input type="radio" name="type_advertisement" value="303" {if isset($advertisement) && $advertisement->type_advertisement == 303}checked="checked" {/if}/>
            </label>
        </td>
    </tr>

     <tr>
        <td colspan="2"><hr /></td>
    </tr>
      <tr>
        <td>
            <label>
                Big Banner Bottom (728X90)
                <input type="radio" name="type_advertisement" value="309" {if isset($advertisement) && $advertisement->type_advertisement == 309}checked="checked" {/if}/>
            </label>
        </td>
        <td>
            <label>
                 Banner Bottom Right (234X90)
                <input type="radio" name="type_advertisement" value="310" {if isset($advertisement) && $advertisement->type_advertisement == 310}checked="checked" {/if}/>
            </label>
        </td>
    </tr>

</tbody>
</table>