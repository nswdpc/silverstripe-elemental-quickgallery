# Documentation

## Configuration

You can set the following default width and height values in your project configuration.

To set a default 800x600 thumbnail dimension:

```yml
---
Name: 'app-gallery-defaults'
After:
    - '#nswdpc-elemental-quickgallery'
---
NSWDPC\Elemental\Models\QuickGallery\ElementQuickGallery:
  default_thumb_width: 800
  default_thumb_height: 600
```

## Options

The following options are available by default in the content block. Your project templates need to handle these options.

- Gallery Type: select a type of gallery. Default options are grid and slideshow.
- Width and Height - thumbnail dimensions
- 'Use enhanced gallery' - enable whatever frontend client gallery tool your project uses . Your frontend service class should handle this option.
- Show captions: show/hide captions

`ElementQuickGallery` extends the default `ElementContent` class and will inherit any fields from that element or extensions applied to it.

## Frontend implementation

The module ships with a service class to implement a frontend implementation via the Requirements API:

[Implementing a frontend view on images](./002_frontend.md)
