{* *****************************************************************************
 * {include file="panes/publishing.tpl" value=$content legend="Publishing"}
 * Pane name:
 *    panes/publishing.tpl
 *    
 * Params:
 *    $value
 *    $legend (optional)
 ***************************************************************************** *}

<fieldset id="pane-publishing" class="{$className}">
    
    {if isset($legend)}<legend>{t}{$legend}{/t}</legend>{/if}
    
    <dl>
        <dt>
            <label>{t}Status{/t}</label>
        </dt>
        <dd>
            <div id="status">                
                <input type="radio" name="status" id="status-available" value="AVAILABLE"
                        {if $value->status == 'AVAILABLE'}checked="checked"{/if}/>
                <label for="status-available">{t}Available{/t}</label>
                
                
                <input type="radio" name="status" id="status-pending" value="PENDING"
                        {if $value->status == 'PENDING'}checked="checked"{/if}/>
                <label for="status-pending">{t}Pending{/t}</label>
            </div>
        </dd>
        
        <dt>
            <label for="starttime">{t}Start publishing{/t}</label>
        </dt>
        <dd>
            <input type="text" name="starttime" id="starttime" value="{$value->starttime}"/>
        </dd>
        
        <dt>
            <label for="endtime">{t}End publishing{/t}</label>
        </dt>
        <dd>
            <input type="text" name="endtime" id="endtime" value="{$value->endtime}"/>
        </dd>
        
        <dt>
            <label for="published">{t}Published date{/t}</label>
        </dt>
        <dd>
            <input type="text" name="published" id="published" value="{$value->published}"/>
        </dd>
    </dl>
</fieldset>

{literal}
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function() {
    var dtOptions = {
        askSecond: false,
        askEra: false
    };
    
    $("#starttime").AnyTime_picker(dtOptions);    
    $("#endtime").AnyTime_picker(dtOptions);    
    $("#published").AnyTime_picker(dtOptions);
    
    $("#status").buttonset();
});
/* ]]> */
</script>
{/literal}