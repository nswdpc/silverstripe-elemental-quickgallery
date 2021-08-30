<?php

namespace NSWDPC\Elemental\Models\QuickGallery;

use Bummzack\SortableFile\Forms\SortableUploadField;
use DNADesign\Elemental\Models\ElementContent;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataList;

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

    private static $db = [
        'GalleryType' => 'Varchar(64)',
        'Width' => 'Int',
        'Height' => 'Int',
        'ShowCaptions' => 'Boolean'
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

    /**
     * @var int
     */
    private static $default_thumb_width = 375;

    /**
     * @var int
     */
    private static $default_thumb_height = 282;

    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Quick Gallery');
    }

    /**
     * Return the generated thumbnail width, use in templates if you want to rely on the configured default width value
     */
    public function getThumbWidth() {
        return $this->Width;
        if($width <= 0) {
            $width = $this->config()->get('default_thumb_width');
        }
        return $width;
    }

    /**
     * Return the generated thumbnail height, use in templates if you want to rely on the configured default height value
     */
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

    /**
     * Ensure a sane dimension is set
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // integers only
        $this->Width = round($this->Width);
        $this->Height = round($this->Height);

        if($this->Width < 0) {
            $this->Width = 0;
        }
        if($this->Height < 0) {
            $this->Height = 0;
        }
    }

    public function getCMSFields() {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName([
                'Images'
            ]);
            $fields->addFieldsToTab(
                'Root.Settings', [
                    DropdownField::create(
                        'GalleryType',
                        _t(
                            __CLASS__ . '.TYPE',
                            'Gallery type'
                        ),
                        [
                            'grid' => _t(__CLASS__ . '.GRID_OF_IMAGES','Grid of images'),
                            'slideshow' => _t(__CLASS__ . '.SLIDESHOW', 'Slideshow'),
                            'Carousel' => _t(__CLASS__ . '.CAROUSEL_DEPRECATED', 'Carousel - deprecated - (note: https://shouldiuseacarousel.com/)'),
                        ]
                    )->setEmptyString('none'),
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
                    )
                ]
            );

            $fields->addFieldsToTab(
                'Root.Main', [
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
    public function SortedImages() : DataList {
        return $this->Images()->Sort('SortOrder');
    }
}
