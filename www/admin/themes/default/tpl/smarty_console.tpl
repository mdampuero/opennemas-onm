
<style type="text/css">
#debugbar-smarty-console h1,#debugbar-smarty-console h2,#debugbar-smarty-console td,#debugbar-smarty-console th,#debugbar-smarty-console p {
    font-family: sans-serif;
    font-weight: normal;
    font-size: 0.9em;
    margin: 1px;
    padding: 0;
}

#debugbar-smarty-console h1 {
    margin: 0;
    text-align: left;
    padding: 2px;
    background-color: #f0c040;
    color:  black;
    font-weight: bold;
    font-size: 1.2em;
 }

#debugbar-smarty-console h2 {
    background-color: #9B410E;
    color: white;
    text-align: left;
    font-weight: bold;
    padding: 2px;
    border-top: 1px solid black;
}

#debugbar-smarty-console {
    background: black; 
}

#debugbar-smarty-console p,#debugbar-smarty-console table,#debugbar-smarty-console div {
    background: #f0ead8;
} 

#debugbar-smarty-console p {
    margin: 0;
    font-style: italic;
    text-align: center;
}

#debugbar-smarty-console table {
    width: 100%;
}

#debugbar-smarty-console th,#debugbar-smarty-console td {
    font-family: monospace;
    vertical-align: top;
    text-align: left;
    width: 50%;
}

#debugbar-smarty-console td {
    color: green;
}

#debugbar-smarty-console .odd {
    background-color: #eeeeee;
}

#debugbar-smarty-console .even {
    background-color: #fafafa;
}

#debugbar-smarty-console .exectime {
    font-size: 0.8em;
    font-style: italic;
}

#debugbar-smarty-console #table_assigned_vars th {
    color: blue;
}

#debugbar-smarty-console #table_config_vars th {
    color: maroon;
}
</style>

<div id="debugbar-smarty-console">
    <h1>Smarty Debug Console  -  Total Time {$execution_time|string_format:"%.5f"}</h1>
    
    <h2>included templates &amp; config files (load time in seconds)</h2>
    
    <div>
    {foreach $template_data as $template}
      <font color=brown>{$template.name}</font>
      <span class="exectime">
       (compile {$template['compile_time']|string_format:"%.5f"}) (render {$template['render_time']|string_format:"%.5f"}) (cache {$template['cache_time']|string_format:"%.5f"})
      </span>
      <br>
    {/foreach}
    </div>
    
    <h2>assigned template variables</h2>
    
    <table id="table_assigned_vars">
        {foreach $assigned_vars as $vars}
           <tr class="{if $vars@iteration % 2 eq 0}odd{else}even{/if}">   
           <th>${$vars@key|escape:'html'}</th>
           <td>{$vars|debug_print_var}</td></tr>
        {/foreach}
    </table>
    
    <h2>assigned config file variables (outer template scope)</h2>
    
    <table id="table_config_vars">
        {foreach $config_vars as $vars}
           <tr class="{if $vars@iteration % 2 eq 0}odd{else}even{/if}">   
           <th>{$vars@key|escape:'html'}</th>
           <td>{$vars|debug_print_var}</td></tr>
        {/foreach}
    </table>
</div>