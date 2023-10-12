<?php

namespace NSWDPC\Elemental\Tests\QuickGallery;

use NSWDPC\Elemental\Models\QuickGallery\ElementQuickGallery;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use Silverstripe\Assets\Dev\TestAssetStore;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Image;
use SilverStripe\View\SSViewer;

/**
 * Unit test to vverify quick gallery setup
 * @author James
 */
class QuickGalleryTest extends SapphireTest
{

    protected $usesDatabase = true;

    protected static $fixture_file = 'QuickGalleryTest.yml';

    public function setUp() : void {
        parent::setUp();

        TestAssetStore::activate('data');
        $files = File::get()->exclude('ClassName', Folder::class);
        foreach ($files as $image) {
            $source_path = __DIR__ . '/data/' . $image->Name;
            $image->setFromLocalFile($source_path, $image->Filename);
            $image->write();
        }
    }

    public function tearDown() : void
    {
        TestAssetStore::reset();
        parent::tearDown();
    }

    public function testGallery() {

        SSViewer::set_themes(['$public', '$default']);

        $default_thumb_width = 190;
        $default_thumb_height = 160;

        Config::modify()->set(ElementQuickGallery::class, 'default_thumb_width', $default_thumb_width);
        Config::modify()->set(ElementQuickGallery::class, 'default_thumb_height', $default_thumb_height);

        $record = [
            'Title' => 'Test gallery',
        ];
        $gallery =  ElementQuickGallery::create($record);
        $gallery->write();

        // assert that defaults kick in when incorrect values are provided
        $this->assertEquals($default_thumb_width, $gallery->Width, "Gallery width matches default value");
        $this->assertEquals($default_thumb_height, $gallery->Height, "Gallery height matches default value");

        $this->assertEquals($default_thumb_width, $gallery->getThumbWidth(), "Gallery width matches default value");
        $this->assertEquals($default_thumb_height, $gallery->getThumbHeight(), "Gallery height matches default value");

        $gallery->Width = 400;
        $gallery->write();

        // assert sensible width holds
        $this->assertEquals(400, $gallery->Width, "Gallery width should be 400");

        $gallery->Height = 360;
        $gallery->write();

        // assert sensible height holds
        $this->assertEquals(360, $gallery->Height, "Gallery height should be 360");

        // add some image to the gallery

        $image1 = $this->objFromFixture(Image::class, 'image1');
        $this->assertTrue($image1 instanceof Image);
        $image2 = $this->objFromFixture(Image::class, 'image2');
        $this->assertTrue($image2 instanceof Image);
        $image3 = $this->objFromFixture(Image::class, 'image3');
        $this->assertTrue($image3 instanceof Image);

        $gallery->Images()->add($image1);
        $gallery->Images()->add($image2);
        $gallery->Images()->add($image3);

        $images = $gallery->Images();

        $this->assertEquals(3, $images->count(), "Should be 3 images");

        $gallery->publishRecursive();

        $this->assertTrue($gallery->isPublished(), "Gallery should be published");

        $this->assertTrue($image1->isPublished(), "Image1 should be published");
        $this->assertTrue($image2->isPublished(), "Image2 should be published");
        $this->assertTrue($image3->isPublished(), "Image3 should be published");

        $template = $gallery->forTemplate();

        $this->assertTrue(strpos($template, $image1->Name) !== false, "{$image1->Name} is not in the template");
        $this->assertTrue(strpos($template, $image2->Name) !== false, "{$image2->Name} is not in the template");
        $this->assertTrue(strpos($template, $image3->Name) !== false, "{$image3->Name} is not in the template");

        $url1 = $image1->FillMax($gallery->Width, $gallery->Height)->Link();
        $url2 = $image2->FillMax($gallery->Width, $gallery->Height)->Link();
        $url3 = $image3->FillMax($gallery->Width, $gallery->Height)->Link();

        $this->assertTrue(strpos($template, $url1) !== false, "{$url1} is not in the template");
        $this->assertTrue(strpos($template, $url2) !== false, "{$url2} is not in the template");
        $this->assertTrue(strpos($template, $url3) !== false, "{$url3} is not in the template");

    }

    public function testNegativeDimensions() {

        $default_thumb_width = 80;
        $default_thumb_height = 80;

        Config::inst()->update(ElementQuickGallery::class, 'default_thumb_width', $default_thumb_width);
        Config::inst()->update(ElementQuickGallery::class, 'default_thumb_height', $default_thumb_height);

        $record = [
            'Title' => 'Test dimensions',
            'Width' => -100,
            'Height' => -80
        ];
        $gallery =  ElementQuickGallery::create($record);
        $gallery->write();


        $this->assertEquals(100, $gallery->Width, "Gallery width matches abs value");
        $this->assertEquals(80, $gallery->Height, "Gallery height matches abs value");

    }

    public function testPositiveDimensions() {

        $default_thumb_width = 140;
        $default_thumb_height = 140;

        Config::inst()->update(ElementQuickGallery::class, 'default_thumb_width', $default_thumb_width);
        Config::inst()->update(ElementQuickGallery::class, 'default_thumb_height', $default_thumb_height);

        $record = [
            'Title' => 'Test dimensions',
            'Width' => 400,
            'Height' => 400
        ];
        $gallery =  ElementQuickGallery::create($record);
        $gallery->write();


        $this->assertEquals(400, $gallery->Width, "Gallery width matches positive value");
        $this->assertEquals(400, $gallery->Height, "Gallery height matches positive value");

    }

    public function testZeroDimensions() {

        $default_thumb_width = 140;
        $default_thumb_height = 140;

        Config::inst()->update(ElementQuickGallery::class, 'default_thumb_width', $default_thumb_width);
        Config::inst()->update(ElementQuickGallery::class, 'default_thumb_height', $default_thumb_height);

        $record = [
            'Title' => 'Test dimensions',
            'Width' => 0,
            'Height' => 0
        ];
        $gallery =  ElementQuickGallery::create($record);
        $gallery->write();


        $this->assertEquals(0, $gallery->Width, "Gallery width matches zero value");
        $this->assertEquals(0, $gallery->Height, "Gallery height matches zero value");

    }

}
