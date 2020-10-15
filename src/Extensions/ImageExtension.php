<?php

namespace NSWDPC\Elemental\Extensions\QuickGallery;

use NSWDPC\Elemental\Models\QuickGallery\ElementQuickGallery;
use SilverStripe\ORM\DataExtension;

/**
 * Provide reverse association with galleries
 */
class ImageExtension extends DataExtension {

    private static $belongs_many_many = [
        'QuickGalleries' => ElementQuickGallery::class
    ];

}
