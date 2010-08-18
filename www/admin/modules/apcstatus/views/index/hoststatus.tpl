{extends file='layout.tpl'}

{block name='body-content'}

<div class="info div1"><h2>General Cache Information</h2>
<table cellspacing=0>
    <tbody>
        <tr class=tr-0>
            <td class=td-0>APC Version</td>
            <td>{$data['apcversion']}</td>
        </tr>
        <tr class=tr-1>
            <td class=td-0>PHP Version</td>
            <td>{$data['phpversion']}</td>
        </tr>
        {if $_SERVER['SERVER_NAME']}
        <tr class=tr-0>
            <td class=td-0>APC Host</td>
            <td>{$_SERVER['SERVER_NAME']} {$host}</td>
        </tr>
        {/if}
        {if $_SERVER['SERVER_SOFTWARE']}
            <tr class=tr-1>
                <td class=td-0>Server Software</td>
                <td>{$_SERVER['SERVER_SOFTWARE']}</td>
            </tr>
        {/if}
        <tr class=tr-0>
            <td class=td-0>Shared Memory</td>
            <td>{$mem['num_seg']} Segment(s) with {$data['seg_size']} <br/> 
                ({$cache['memory_type']} memory, {$cache['locking_type']} locking)</td>
        </tr>
        <tr class=tr-1><td class=td-0>Start Time</td><td>date(DATE_FORMAT,{$cache['start_time']})</td></tr>
        <tr class=tr-0><td class=td-0>Uptime</td><td>function duration {$cache['start_time']}</td></tr>
        <tr class=tr-1><td class=td-0>File Upload Support</td><td>{$cache['file_upload_progress']}</td></tr>
    </tbody>
</table>
</div>

		<div class="info div1"><h2>File Cache Information</h2>
            <table cellspacing=0><tbody>
                <tr class=tr-0><td class=td-0>Cached Files</td><td>{$data['number_files']} ({$data['size_files']})</td></tr>
                <tr class=tr-1><td class=td-0>Hits</td><td>{$cache['num_hits']}</td></tr>
                <tr class=tr-0><td class=td-0>Misses</td><td>{$cache['num_misses']}</td></tr>
                <tr class=tr-1><td class=td-0>Request Rate (hits, misses)</td><td>{$data['req_rate']} cache requests/second</td></tr>
                <tr class=tr-0><td class=td-0>Hit Rate</td><td>{$data['hit_rate']} cache requests/second</td></tr>
                <tr class=tr-1><td class=td-0>Miss Rate</td><td>{$data['miss_rate']} cache requests/second</td></tr>
                <tr class=tr-0><td class=td-0>Insert Rate</td><td>{$data['insert_rate']} cache requests/second</td></tr>
                <tr class=tr-1><td class=td-0>Cache full count</td><td>{$cache['expunges']}</td></tr>
            </tbody></table>
        </div>

		<div class="info div1"><h2>User Cache Information</h2>
            <table cellspacing=0><tbody>
                <tr class=tr-0><td class=td-0>Cached Variables</td><td>{$data['number_vars']} ({$data['size_vars']})</td></tr>
                <tr class=tr-1><td class=td-0>Hits</td><td>{$cache_user['num_hits']}</td></tr>
                <tr class=tr-0><td class=td-0>Misses</td><td>{$cache_user['num_misses']}</td></tr>
                <tr class=tr-1><td class=td-0>Request Rate (hits, misses)</td><td>{$data['req_rate_user']} cache requests/second</td></tr>
                <tr class=tr-0><td class=td-0>Hit Rate</td><td>{$data['hit_rate_user']} cache requests/second</td></tr>
                <tr class=tr-1><td class=td-0>Miss Rate</td><td>{$data['miss_rate_user']} cache requests/second</td></tr>
                <tr class=tr-0><td class=td-0>Insert Rate</td><td>{$data['insert_rate_user']} cache requests/second</td></tr>
                <tr class=tr-1><td class=td-0>Cache full count</td><td>{$cache_user['expunges']}</td></tr>    
            </tbody></table>
		</div>

		<div class="info div2"><h2>Runtime Settings</h2><table cellspacing=0><tbody>
        {$j=0}
        {foreach $data['ini_apc'] as $k}
            <tr class=tr-{$j}>
                <td class=td-0>{$k@key}</td>
                <td>{str_replace(",",",<br />",$k['local_value'])}</td>
            </tr>
            {$j = 1 - $j}
        {/foreach}
        
        {if ($mem['num_seg'] gt 1)
            or ($mem['num_seg'] eq 1)
            and ($mem['block_lists'][0]|@count gt 1)}
            {$mem_note = "Memory Usage<br /><font size=-2>(multiple slices indicate fragments)</font>"}
        {else}
            {$mem_note = "Memory Usage"}
        {/if}
    
        </tbody></table>
        </div>

        <div class="graph div3"><h2>Host Status Diagrams</h2>
            <table cellspacing=0>
            <tbody>
                <tr>
                    <td class=td-0>{$mem_note}</td>
                    <td class=td-1>Hits &amp; Misses</td>
                </tr>
    
                {if $data['graphics_avail'] eq true}
                <tr>
                  <td class=td-0><img alt="" width=300 height=400 src="/{url route="apcstatus-index-renderimages" num=1}?IMG=1&{$time}"></td>
                  <td class=td-1><img alt="" width=300 height=400 src="/{url route="apcstatus-index-renderimages" num=2}?IMG=2&{$time}"></td>
                </tr>
                {else}
                <tr>
                    <td class=td-0>
                        <span class="green box">&nbsp;</span>
                        Free: {$data['mem_avail']} {sprintf(" (%.1f%%)",$data['mem_avail']*100/$data['mem_size'])}
                    </td>
                    <td class=td-1>
                        <span class="green box">&nbsp;</span>
                        Hits: {$cache['num_hits']}{sprintf(" (%.1f%%)",$cache['num_hits']*100/($cache['num_hits']+$cache['num_misses']))}
                    </td>
                    </tr>
                <tr>
                    <td class=td-0>
                        <span class="red box">&nbsp;</span>
                        Used: {$data['mem_used']}{sprintf(" (%.1f%%)",$data['mem_used'] *100/$data['mem_size'])}
                    </td>
                    <td class=td-1>
                        <span class="red box">&nbsp;</span>
                        Misses: {$cache['num_misses']}{sprintf(" (%.1f%%)",$cache['num_misses']*100/($cache['num_hits']+$cache['num_misses']))}
                    </td>
                </tr>
            </tbody>
            </table>
            {/if}
    
            <br/>
            <h2>Detailed Memory Usage and Fragmentation</h2>
            <table cellspacing=0><tbody>
            <tr>
            <td class=td-0 colspan=2><br/>
                {$nseg = 0}{$freeseg = 0}{$fragsize = 0}{$freetotal = 0}{$i=0}
                {while $i lt $mem['num_seg']}
                    {$ptr = 0}
                    {foreach $mem['block_lists'][$i] as $block}
                        {if ($block['offset'] != $ptr)}
                            {$nseg++}
                        {/if}
                        {$ptr = $block['offset'] + $block['size']}
                        {if $block['size'] lt (5*1024*1024)}
                            {$fragsize= $fragsize + $block['size']}
                        {/if}
                        {$freetotal= $freetotal + $block['size']}
                    {/foreach}
                    {$freeseg = $freeseg + count($mem['block_lists'][$i])}
                    {$i++}
                {/while}

	
            {if ($freeseg > 1)}
                {$frag = sprintf("%.2f%% (%s out of %s in %d fragments)", ($fragsize/$freetotal)*100,$fragsize,$freetotal,$freeseg)}
            {else}
                {$frag = "0%"}
            {/if}
        
            {if $data['graphics_avail']}
                {$size='width='+(2*GRAPH_SIZE+150)+' height='+(GRAPH_SIZE+10)}
                <img alt="" $size src="{url route="apcstatus-index-renderimages" num=3}?IMG=3&$time">
            {/if}
            </br>Fragmentation: {$frag}
            </td>
            </tr>

            {if isset($mem['adist'])}
              {foreach $mem['adist'] as $adist}
                {$cur = pow(2,$i)}{$nxt = pow(2,$adist@key+1)-1}
                {if ($i eq 0)}
                    {$range = "1"}
                {else}
                    {$range = "$cur - $nxt"}
                {/if}
                <tr><th align=right>{$range}</th><td align=right>{$adist}</td></tr>
              {/foreach}
            {/if}
        </tbody>
    </table>
    </div>

{/block}