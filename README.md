# Bluecadet Events

A WordPress events plugin: single and recurring events, a dedicated query table for fast date filtering, optional locations and series, ICS export, and a filter-first API for developers. Works in both the block (Gutenberg) and classic editors.

## Features

- **Events** custom post type with start/end date-times.
- **Recurring events** — daily, weekly, monthly, and consecutive frequencies; end on a date or after _N_ occurrences; plus specific added dates and exclusions. Each occurrence is a real child post; the parent acts as the canonical/SEO anchor.
- **Custom `bc_events` table** mirroring each occurrence for efficient upcoming/past/range queries and de-duplication of recurring sets.
- **Locations** and **Series** (each toggleable via a filter).
- **ICS** (`.ics`) export for events.
- **Editor parity** — the same data saves identically from Gutenberg or the classic editor.
- **Extensible** — a broad, guarded filter surface plus editor SlotFill extension points.

## Requirements

- WordPress (tested against the current release)
- PHP **8.1+**

## Installation

```bash
composer require bluecadet/bluecadet-events
```

Then activate the plugin in **Plugins**. Activation creates the custom `bc_events` table.

> Installed via Composer, the package ships only runtime code — tests, docs tooling, and CI config are excluded (see `.gitattributes`).

## Quick start

1. Activate the plugin.
2. Open **Events → Add New**, set the start/end date-time, and (optionally) enable recurrence.
3. Save. For a recurring event, the child occurrences are generated in the background and written to the `bc_events` table.

## Documentation

Full developer documentation is published to GitHub Pages:

- **Hooks reference** — every filter/action, grouped by object type and category, with defaults and examples.
- **API reference** — classes, methods, and global functions (generated from source docblocks).

> 📖 `https://bluecadet.github.io/bluecadet-events/` _(update to match the repo once Pages is enabled)_

### Extending the plugin

**Filters** control most behavior — a few common ones:

```php
// Turn locations/series on or off.
add_filter( 'bc_events/locations/post_type/use_event_locations', '__return_false' );

// Customize the archive layout / paging.
add_filter( 'bc_events/events/settings/archive', function ( $args ) {
    $args['per_page'] = 24;
    return $args;
} );

// Remove all plugin data when the plugin is uninstalled.
add_filter( 'bc_events/uninstall/delete_data', '__return_true' );
```

See the Hooks reference for the full list. All hooks guard their return values, so a bad filter return falls back to the documented default rather than breaking.

**Editor SlotFills** let you add your own fields into the event form (block editor):

```js
const { AfterEventDetailsFill } = window.BluecadetEvents;

// Render your own controls after the event details section.
<AfterEventDetailsFill>
  { ( { keys, postID, meta, setMeta } ) => ( /* your fields */ ) }
</AfterEventDetailsFill>
```

`AfterEventDetailsFill` and `AfterRecurringFill` are exposed on `window.BluecadetEvents`.

## Development

- **JS/blocks:** `npm run build` (see `package.json` scripts).
- **Tests:** a PHPUnit suite runs against a real WordPress via `wp-env`. See [`tests/README.md`](tests/README.md).
- **Docs:** the site is built in CI (`.github/workflows/docs.yml`) from phpDocumentor + a custom hooks generator (`bin/docs/generate-hooks.php`). To build the hooks page locally: `php bin/docs/generate-hooks.php`.

## License

© Bluecadet. _(Set your intended license here.)_
