{*
    OpenNeMas project

    @theme      Lucidity
*}
<div class="layout-column span-8 third-column last">

    {include file="widgets/widget_opinions_author.tpl"}
    <hr class="new-separator"/>

    {render_widget name="OpinionAuthorList"}
    <hr class="new-separator" />

    {include file="ads/ad_in_column.tpl" type='103'}
    <hr class="new-separator" />

    {include file="widgets/facebook_stream_box.tpl"}
    <hr class="new-separator" />

    {*include file="widgets/widget_most_seeing_voted_commented_content.class.tpl"*}
    {render_widget name="MostSeeingVotedCommentedContent"}
    <hr class="new-separator"/>

    {include file="ads/ad_in_column.tpl" type='105'}
    <hr class="new-separator" />
</div>
