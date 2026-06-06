# Module Template

This is a basic Drupal module template. To use it:

1. Copy this directory to your Drupal installation: `modules/custom/yourmodulename/`
2. Rename files from `MODULENAME.*` to `yourmodulename.*`
3. Replace all instances of `MODULENAME` with your module's machine name (lowercase, underscores)
4. Replace all instances of `MODULELABEL` with your module's human-readable name
5. Customize the module as needed

## Files Included

- `MODULENAME.info.yml` - Module metadata
- `MODULENAME.module` - Hook implementations
- `MODULENAME.routing.yml` - Route definitions
- `src/Controller/ExampleController.php` - Example controller

## Directory Structure

```
MODULENAME/
├── MODULENAME.info.yml
├── MODULENAME.module
├── MODULENAME.routing.yml
└── src/
    └── Controller/
        └── ExampleController.php
```

## Next Steps

After creating your module:

1. Enable it: `ddev drush en MODULENAME -y`
2. Clear cache: `ddev drush cr`
3. Visit your route: `/MODULENAME/example`
