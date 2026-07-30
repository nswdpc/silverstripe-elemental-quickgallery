<?php

declare(strict_types=1);

namespace NSWDPC\Elemental\Extensions\QuickGallery;

use NSWDPC\Elemental\Models\QuickGallery\ElementQuickGallery;
use SilverStripe\Core\Extension;

/**
 * Provide reverse association with galleries
 * @method \SilverStripe\ORM\ManyManyList<\NSWDPC\Elemental\Models\QuickGallery\ElementQuickGallery> QuickGalleries()
 * @extends \SilverStripe\Core\Extension<static>
 */
class ImageExtension extends Extension
{
    private static array $belongs_many_many = [
        'QuickGalleries' => ElementQuickGallery::class
    ];

}
