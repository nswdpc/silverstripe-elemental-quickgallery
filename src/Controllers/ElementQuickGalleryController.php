<?php
namespace NSWDPC\Elemental\Controllers\QuickGallery;

use DNADesign\Elemental\Controllers\ElementController;
use NSWDPC\Elemental\Services\QuickGallery\Frontend;

class ElementQuickGalleryController extends ElementController
{
    #[\Override]
    public function init() {
        parent::init();
        // @phpstan-ignore argument.type
        Frontend::create()->addRequirements($this->getElement());
    }

}
