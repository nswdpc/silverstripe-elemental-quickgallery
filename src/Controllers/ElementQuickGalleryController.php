<?php
namespace NSWDPC\Elemental\Models\QuickGallery;

use DNADesign\Elemental\Controllers\ElementController;
use SilverStripe\View\Requirements;
use SilverStripe\View\ThemeResourceLoader;

class ElementQuickGalleryController extends ElementController
{
    public function init() {

        parent::init();
        if ($this->owner->UseJS) {
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


            Requirements::css('https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css');
            Requirements::css('https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css');
            Requirements::css('https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.css');



            Requirements::customScript(
<<<JS
    $(document).ready(function(){
        $('.gallery').slickLightbox({
            itemSelector: 'a.gallery-item'
        });
    });
JS
            );

        }


    }
}

