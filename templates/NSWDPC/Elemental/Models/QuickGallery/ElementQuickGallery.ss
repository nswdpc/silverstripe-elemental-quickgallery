<% include ElementQuickGalleryTitle %>
<div class="{$ElementStyles}" data-type="gallery">
    <% loop $SortedImages %>
    <div>
        <a<% if $Title %> title="{$Title.XML}"<% end_if %> href="{$Link}">
        <% include ElementQuickGalleryImage Width=$Up.ThumbWidth, Height=$Up.ThumbHeight %>
        </a>
        <% if $Up.ShowCaptions %>
        <p class="caption">
            <% if $AltText %>{$AltText}<% else %>{$Title}<% end_if %>
        </p>
        <% end_if %>
    </div>
    <% end_loop %>
</div>
