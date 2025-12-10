<?php

namespace NSWDPC\Elemental\Extensions\QuickGallery;

use NSWDPC\Elemental\Models\QuickGallery\ElementQuickGallery;
use SilverStripe\ORM\DataExtension;

/**
 * Provide reverse association with galleries
 * @method \SilverStripe\ORM\ManyManyList<\NSWDPC\Elemental\Models\QuickGallery\ElementQuickGallery> QuickGalleries()
 * @extends \SilverStripe\ORM\DataExtension<static>
 */
class ImageExtension extends DataExtension
{
    private static array $belongs_many_many = [
        'QuickGalleries' => ElementQuickGallery::class
    ];

}
