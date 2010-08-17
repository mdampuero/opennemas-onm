{extends file="layout.tpl"}

{block name="body-content"}
    {$message}
    
    <ul id="list">
        {for $i=0;$i<count($posts);$i++}
        <li>
        {$posts[$i]}    
        </li>
        {/for}  
    </ul>
{/block}