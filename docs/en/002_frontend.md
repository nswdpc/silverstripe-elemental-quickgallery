## Frontend implementation

The module ships with a frontend implementation using Slick. You will need to provide your own jQuery requirement.

You don't have to use Slick. To implement your own frontend, use a service class via `Injector`:

```yml
---
Name: 'app-gallery-frontend'
---
SilverStripe\Core\Injector\Injector:
    NSWDPC\Elemental\Services\QuickGallery\Frontend:
      class: 'MyApp\MyGalleryFrontend'
```

Your class should extend `NSWDPC\Elemental\Services\QuickGallery\Frontend` and implement the following methods:

### addRequirements

Provided the current element instance as a parameter, add requirements needed to load your gallery/slideshow implementation.

This method should call `addLoader`.

### addLoader

Add Javascript function that will bootstrap/load the current element's images, image captions and any other features/options into your gallery/slideshow implementation.


Refer to the `Frontend` class for guidance.
