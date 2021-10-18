<% include ElementQuickGalleryTitle %>
<div class="{$ElementStyles}" data-type="gallery">
    <% loop $SortedImages %>
    <div>
        <a<% if $Title %> title="{$Title.XML}"<% end_if %> href="{$Link}">
        <% include ElementQuickGalleryImage Width=$Up.ThumbWidth, Height=$Up.ThumbHeight %>
        </a>
    </div>
    <% end_loop %>
</div>
