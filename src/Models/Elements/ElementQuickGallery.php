<?php

namespace NSWDPC\Elemental\Models\QuickGallery;

use NSWDPC\Elemental\Models\QuickGallery\ElementQuickGalleryController;
use Bummzack\SortableFile\Forms\SortableUploadField;
use DNADesign\Elemental\Models\ElementContent;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;

/**
 * ElementQuickGallery adds a gallery via a sortable upload field
 * All images can be added in one go!
 */
class ElementQuickGallery extends ElementContent {

    private static $icon = 'font-icon-thumbnails';

    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'ElementQuickGallery';

    private static $title = 'Quick Gallery';
    private static $description = "Display one or more images";

    private static $singular_name = 'Quick gallery';
    private static $plural_name = 'Quick galleries';

    private static $inline_editable = false;

    private static $controller_class = ElementQuickGalleryController::class;

    private static $db = [
        'GalleryType' => 'Varchar(64)',
        'Width' => 'Int',
        'Height' => 'Int',
        'ShowCaptions' => 'Boolean',
        'UseJS' => 'Boolean'
    ];
    private static $many_many = [
        'Images' => Image::class
    ];

    private static $many_many_extraFields = [
        'Images' => [
            'SortOrder' => 'Int'
        ]
    ];

    private static $owns = [
        'Images'
    ];

    private static $allowed_file_types = ["jpg","jpeg","gif","png","webp"];
    private static $default_thumb_width = 128;
    private static $default_thumb_height = 96;

    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Quick Gallery');
    }

    public function getThumbWidth() {
        $width = $this->Width;
        if($width <= 0) {
            $width = $this->config()->get('default_thumb_width');
        }
        return $width;
    }

    public function getThumbHeight() {
        $height = $this->Height;
        if($height <= 0) {
            $height = $this->config()->get('default_thumb_height');
        }
        return $height;
    }

    public function getAllowedFileTypes() {
        $types = $this->config()->get('allowed_file_types');
        if(empty($types)) {
            $types = ["jpg","jpeg","gif","png","webp"];
        }
        $types = array_unique($types);
        return $types;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->Width = $this->getThumbWidth();
        $this->Height = $this->getThumbHeight();
    }

    public function getCMSFields() {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName([
                'Images'
            ]);
            $fields->addFieldsToTab(
                'Root.Main', [
                    DropdownField::create(
                        'GalleryType',
                        _t(
                            __CLASS__ . '.TYPE',
                            'Gallery type'
                        ),
                        [
                            'grid' => 'Grid',
                            'Carousel' => 'Carousel'
                        ]
                    )->setEmptyString('none'),
                    CheckboxField::create(
                        'UseJS',
                        _t(
                            __CLASS__ . '.JAVASCRIPT',
                            'Use enhanced gallery'
                        )
                    ),
                    CheckboxField::create(
                        'ShowCaptions',
                        _t(
                            __CLASS__ . '.CAPTIONS',
                            'Show image captions'
                        )
                    ),
                    NumericField::create(
                        'Width',
                        _t(
                            __CLASS__ . 'WIDTH', 'Thumbnail width'
                        )
                    ),
                    NumericField::create(
                        'Height',
                        _t(
                            __CLASS__ . 'WIDTH', 'Thumbnail height'
                        )
                    ),
                    SortableUploadField::create(
                        'Images',
                        _t(
                            __CLASS__ . '.GALLERY_IMAGES',
                            'Gallery images'
                        )
                    )->setFolderName('quick-gallery/' . $this->ID)
                    ->setAllowedExtensions($this->getAllowedFileTypes())
                    ->setDescription(
                        sprintf(_t(
                            __CLASS__ . 'ALLOWED_FILE_TYPES',
                            'Allowed file types: %s'
                        ), implode(",", $this->getAllowedFileTypes()))
                    )
                ]
            );
        });
        return parent::getCMSFields();
    }

    /**
     * Return images in sorted order
     */
    public function SortedImages() {
        return $this->Images()->Sort('SortOrder');
    }
}
