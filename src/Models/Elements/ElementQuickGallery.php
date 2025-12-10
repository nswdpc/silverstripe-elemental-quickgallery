<?php

namespace NSWDPC\Elemental\Models\QuickGallery;

use NSWDPC\Elemental\Controllers\QuickGallery\ElementQuickGalleryController;
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
 * @property ?string $GalleryType
 * @property int $Width
 * @property int $Height
 * @property bool $ShowCaptions
 * @property bool $UseJS
 * @method \SilverStripe\ORM\ManyManyList<\SilverStripe\Assets\Image> Images()
 */
class ElementQuickGallery extends ElementContent {

    private static string $icon = 'font-icon-thumbnails';

    /**
     * Defines the database table name
     */
    private static string $table_name = 'ElementQuickGallery';

    private static string $title = 'Quick Gallery';

    private static string $description = "Display one or more images";

    private static string $singular_name = 'Quick gallery';

    private static string $plural_name = 'Quick galleries';

    private static bool $inline_editable = false;

    private static string $controller_class = ElementQuickGalleryController::class;

    private static array $db = [
        'GalleryType' => 'Varchar(64)',
        'Width' => 'Int',
        'Height' => 'Int',
        'ShowCaptions' => 'Boolean',
        'UseJS' => 'Boolean'
    ];

    private static array $many_many = [
        'Images' => Image::class
    ];

    private static array $many_many_extraFields = [
        'Images' => [
            'SortOrder' => 'Int'
        ]
    ];

    private static array $owns = [
        'Images'
    ];

    private static array $allowed_file_types = ["jpg","jpeg","gif","png","webp"];

    private static int $default_thumb_width = 375;

    private static int $default_thumb_height = 282;

    #[\Override]
    public function getType()
    {
        return _t(self::class . '.BlockType', 'Quick Gallery');
    }

    /**
     * Return the generated thumbnail width, use in templates if you want to rely on the configured default width value
     */
    public function getThumbWidth() {
        $width = $this->Width;
        if($width <= 0) {
            $width = self::config()->get('default_thumb_width');
        }

        return $width;
    }

    /**
     * Return the generated thumbnail height, use in templates if you want to rely on the configured default height value
     */
    public function getThumbHeight() {
        $height = $this->Height;
        if($height <= 0) {
            $height = self::config()->get('default_thumb_height');
        }

        return $height;
    }

    public function getAllowedFileTypes(): array {
        $types = self::config()->get('allowed_file_types');
        if(empty($types)) {
            $types = ["jpg","jpeg","gif","png","webp"];
        }
        return array_unique($types);
    }

    /**
     * Ensure a sane dimension is set
     */
    #[\Override]
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // if a new element, set dimensions to the defaults from config
        if(!$this->exists()) {
            if(is_null($this->Width)) {
                $this->Width = $this->getThumbWidth();
            }

            if(is_null($this->Height)) {
                $this->Height = $this->getThumbHeight();
            }
        }

        // Enforce dimensions >=0 values
        $this->Width = abs(intval($this->Width ?? 0));
        $this->Height = abs(intval($this->Height ?? 0));

    }

    #[\Override]
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName([
            'Images'
        ]);

        $fields->insertAfter(
            'HTML',
            SortableUploadField::create(
                'Images',
                _t(
                    self::class . '.GALLERY_IMAGES',
                    'Gallery images'
                )
            )->setFolderName('quick-gallery/' . $this->ID)
            ->setAllowedExtensions($this->getAllowedFileTypes())
            ->setDescription(
                sprintf(_t(
                    self::class . '.ALLOWED_FILE_TYPES',
                    'Allowed file types: %s'
                ), implode(",", $this->getAllowedFileTypes()))
            )
        );

        $fields->addFieldsToTab(
            'Root.Main',
            [
                DropdownField::create(
                    'GalleryType',
                    _t(
                        self::class . '.TYPE',
                        'Gallery type'
                    ),
                    [
                        'grid' => _t(self::class . '.GRID_OF_IMAGES','Grid of images'),
                        'slideshow' => _t(self::class . '.SLIDESHOW', 'Slideshow'),
                        'Carousel' => _t(self::class . '.CAROUSEL_DEPRECATED', 'Carousel - deprecated - (note: https://shouldiuseacarousel.com/)'),
                    ]
                )->setEmptyString('none'),
                CheckboxField::create(
                    'UseJS',
                    _t(
                        self::class . '.JAVASCRIPT',
                        'Use enhanced gallery'
                    )
                ),
                CheckboxField::create(
                    'ShowCaptions',
                    _t(
                        self::class . '.CAPTIONS',
                        'Show image captions'
                    )
                ),
                NumericField::create(
                    'Width',
                    _t(
                        self::class . '.WIDTH', 'Thumbnail width'
                    )
                ),
                NumericField::create(
                    'Height',
                    _t(
                        self::class . '.HEIGHT', 'Thumbnail height'
                    )
                )
            ]
        );
        return $fields;
    }

    /**
     * Return images in sorted order
     */
    public function SortedImages() : DataList {
        return $this->Images()->Sort('SortOrder');
    }
}
