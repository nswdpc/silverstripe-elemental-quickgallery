<?php

namespace NSWDPC\Elemental\Services\QuickGallery;

use NSWDPC\Elemental\Models\QuickGallery\ElementQuickGallery;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\View\Requirements;

/**
 * Provides a basic frontend using Slick
 * Apply your own frontend and/or frontend loader using Injector
 * @author James
 */
class Frontend {

    use Configurable;

    use Injectable;

    /**
     * @var ElementQuickGallery
     */
    protected $element;

    /**
     * Add all requirements
     * @return void
     * @param ElementQuickGallery $element
     */
    public function addRequirements(ElementQuickGallery $element) {
        $this->element = $element;
        if ($this->element->UseJS == 1) {

            Requirements::javascript(
                'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js',
                [
                    "integrity" => "sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg==",
                    "crossorigin" => "anonymous"
                ]
            );

            Requirements::javascript(
                'https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.min.js',
                [
                    "integrity" => "sha512-iSVaq9Huv1kxBDAOH7Il1rwIJD+uspMQC1r4Y73QquhbI2ia+PIXUoS5rBjWjYyD03S8t7gZmON+Dk6yPlWHXw==",
                    "crossorigin" => "anonymous"
                ]
            );

            Requirements::css(
                'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css',
                'screen',
                [
                    "integrity" => "sha512-yHknP1/AwR+yx26cB1y0cjvQUMvEa2PFzt1c9LlS4pRQ5NOTZFWbhBig+X9G9eYW/8m0/4OXNx8pxJ6z57x0dw==",
                    "crossorigin" => "anonymous"
                ]
            );
            Requirements::css(
                'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css',
                'screen',
                [
                    "integrity" => "sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A==",
                    "crossorigin" => "anonymous"
                ]
            );
            Requirements::css(
                'https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.css',
                'screen',
                [
                    "integrity" => "sha512-GPEZI1E6wle+Inl8CkTU3Ncgc9WefoWH4Jp8urbxZNbaISy0AhzIMXVzdK2GEf1+OVhA+MlcwPuNve3rL1F9yg==",
                    "crossorigin" => "anonymous"
                ]
            );

            $this->addLoader();

        }

    }

    /**
     * Adds a loader to apply the frontend
     * @return void
     */
    public function addLoader() {

        if(!$this->element) {
            // Cannot load if no element defined
            return;
        }

        // Apply loaded to this element via its anchor
        $anchor = $this->element->getAnchor();
        $script = <<<JS
$(document).ready(function(){
    $('#{$anchor} [data-type="gallery"]').slickLightbox({
        itemSelector: 'a',
        caption: function(element, info) {
            return $(element).next('p.caption').text()
        },
        captionPosition: 'dynamic',
        lazy: true
    });
});
JS;
        Requirements::customScript($script, "gallery-{$anchor}");

    }

}
