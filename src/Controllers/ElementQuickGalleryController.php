<?php
namespace NSWDPC\Elemental\Controllers\QuickGallery;

use DNADesign\Elemental\Controllers\ElementController;
use NSWDPC\Elemental\Services\QuickGallery\Frontend;

class ElementQuickGalleryController extends ElementController
{
    public function init() {
        parent::init();
        Frontend::create()->addRequirements($this->getElement());
    }

}
