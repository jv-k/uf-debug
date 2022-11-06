# Debug Sprinkle for UserFrosting

This Sprinkle provides a simple page which can be customised to show debug information for developing software with UserFrosting.

# Installation
To install this sprinkle, add `uf-debug` to the `require` and `base` sections of your `app/sprinkles.json` file in your UserFrosting installation:

```json
{
    "require": {
        ...
        "jv-k/uf-debug": "^1.0.0"
    },
    "base": [
        ...
        "uf-debug"
    ]
}
```

Download and install the new dependency to your sprinkles folder:
```bash
composer update
```

Add the `debug_access` permission to the database permissions:
```bash
php bakery seed DebugPermissions
```

Finally, add the sprinkle to your installing with the last step, which updates the autoloader:
```bash
php bakery bake
```