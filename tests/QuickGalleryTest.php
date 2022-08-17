<?php

namespace  NSWDPC\Elemental\Tests\QuickGallery;

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

        Config::inst()->update(ElementQuickGallery::class, 'default_thumb_width', 190);
        Config::inst()->update(ElementQuickGallery::class, 'default_thumb_height', 160);

        $record = [
            'Title' => 'Test gallery',
            'Width' => -20,
            'Height' => -100,
        ];
        $gallery =  ElementQuickGallery::create($record);
        $gallery->write();

        // assert that defaults kick in when incorrect values are provided
        $this->assertEquals(0, $gallery->Width, "Gallery width should be 0");
        $this->assertEquals(0, $gallery->Height, "Gallery height should be 0");

        $this->assertEquals(190, $gallery->getThumbWidth(), "Gallery width should be 190");
        $this->assertEquals(160, $gallery->getThumbHeight(), "Gallery height should be 160");

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

        $gallery->doPublish();

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

}
