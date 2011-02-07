{extends file='base/frontpage_layout.tpl'}

{block name="meta" append}
<title>{$article->title|clearslash} - {$category_real_name|clearslash|capitalize} {$subcategory_real_name|clearslash|capitalize} - {$smarty.const.SITE_TITLE} </title>
<meta name="keywords" content="{$article->metadata|clearslash}" />
<meta name="description" content="{$article->summary|strip_tags|clearslash}" />

<meta property="og:title" content="{$article->title|clearslash}" />
<meta property="og:description" content="{$article->summary|strip_tags|clearslash}" />
<meta property="og:image" content="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photoInt->path_file}{$photoInt->name}" />
{/block}

{block name='header-css' append}
{$smarty.block.parent}
<link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="{$params.CSS_DIR}video-js.css" type="text/css" media="screen,projection" />
{/block}

{block name="footer-js"}
{$smarty.block.parent}
<script charset="utf-8" type="text/javascript">
    $(function(){
      VideoJS.setup();
    })
</script>
{/block}

{block name="content"}
    <div class="container_ads">
        {include file="ads/ad_in_header.tpl" type1='1' type2='2' nocache}
    </div>
    <div class="wrapper clearfix">
        <div class="container container_with_border">
            
            <div id="header">
               {include file="base/partials/_frontpage_header.tpl"}
               {include file="base/partials/_frontpage_menu.tpl"}
            </div><!-- #header -->
            
            <div id="main_content" class="wrapper_content_inside_container span-24">

                <div class="span-24 search-results">
                    <div id="cse-search-results" style="padding:20px"></div>
    
                    <script type="text/javascript">
                        var googleSearchIframeName = "cse-search-results";
                        var googleSearchFormName = "cse-search-box";
                        var googleSearchFrameWidth = 950;
                        var googleSearchDomain = "www.google.es";
                        var googleSearchPath = "/cse";
                    </script>
    
                    <script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>
                </div>
                 
            </div><!-- fin #main_content -->

        </div><!-- fin .container -->

    </div><!-- fin .wrapper -->
{/block}


{block name="footer"}
<div id="wrapper-footer" class="wrapper clearfix">
    <div class="container clearfix span-24 last">
        <div id="footer" class="">
             {include file="base/partials/_frontpage_footer.tpl"}
        </div><!-- fin .footer -->
    </div><!-- fin .container -->
</div>
{/block}