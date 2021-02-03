<video controls width={$width} height="{$height}">
  {foreach $info["source"] as $type => $url}
    <source src="{$url}" type="video/{$type}">
  {/foreach}
</video>
