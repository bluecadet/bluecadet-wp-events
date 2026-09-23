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

### Abilities API / MCP

On WordPress 6.9+ the plugin registers `bc-events/*` [abilities](https://developer.wordpress.org/apis/abilities/). With the [WordPress MCP Adapter](https://github.com/WordPress/mcp-adapter) active, they show up as MCP tools, so an AI client can create and edit events.

| Ability | Does |
|---|---|
| `bc-events/get-settings` | What this site has enabled (see below). Clients should call it first. |
| `bc-events/list-events` / `get-event` | Look up events. Occurrences are hidden from the list unless `include_children` or `parent_id` is set. |
| `bc-events/create-event` / `update-event` | Write an event. Dates/times are site-timezone `YYYY-MM-DD` / `HH:MM`; the timestamps are calculated server-side and the input is checked with the editor's validation rules. The save goes through `wp_insert_post`, so recurrence runs exactly as it does from the editor. |
| `bc-events/list-terms` | Terms of a taxonomy attached to events. |
| `bc-events/list-locations` / `list-series` | Only registered when locations / series are on. |

Every ability checks the usual capabilities (`edit_posts`, `edit_post`, the post type's `create_posts`/`publish_posts`, the taxonomy's `assign_terms`). New events default to `draft`. An occurrence (child) only accepts content fields and `child_deny_override`; its dates and recurrence belong to the parent.

**What a site can switch off.** The ability schemas are built from these filters, so a disabled option is never offered or accepted:

| Filter | Removes |
|---|---|
| `bc_events/events/settings/frequency_options` | Frequencies from the `freq` enum. Returning an empty array keeps all four; it cannot turn recurrence off. |
| `bc_events/locations/post_type/use_event_locations` | `location_ids` and `list-locations` |
| `bc_events/series/post_type/use_event_series` | `series_ids` and `list-series` |
| `bc_events/events/display/use_event_recurring_description` | `recur_desc` |
| `bc_events_events_post_type_settings` (`supports`) | `excerpt` / `featured_media`, when `excerpt` / `thumbnail` support is removed |
| `bc_events/abilities/mcp_public` | Return `false` to hide every `bc-events/*` ability from MCP (they stay registered) |

`virtual_event` and `virtual_url` are registered meta but have no editor UI yet, so they're not exposed.

**Taxonomies.** The plugin registers no taxonomies. Whatever the site attaches to `bc_events` is discovered when an ability runs: `get-settings` lists them, `list-terms` reads them, and `create-event`/`update-event` accept `terms: { "<taxonomy>": [ids or slugs] }`. Terms must already exist; they are never created. Occurrences inherit the parent's terms.

**How to check a site's settings.** Ask the site rather than reading its theme:

```bash
# What the abilities will offer on this site
wp --user=<admin> eval 'print_r( wp_get_ability( "bc-events/get-settings" )->execute() );'

# The full create-event schema, after the filters
wp eval 'echo wp_json_encode( wp_get_ability( "bc-events/create-event" )->get_input_schema(), JSON_PRETTY_PRINT );'
```

The block editor reads the same values from `GET /wp-json/bc-events/v1/get-support-settings`.

## Development

- **JS/blocks:** `npm run build` (see `package.json` scripts).
- **Tests:** a PHPUnit suite runs against a real WordPress via `wp-env`. See [`tests/README.md`](tests/README.md).
- **Docs:** the site is built in CI (`.github/workflows/docs.yml`) from phpDocumentor + a custom hooks generator (`bin/docs/generate-hooks.php`). To build the hooks page locally: `php bin/docs/generate-hooks.php`.

## License

© Bluecadet. _(Set your intended license here.)_
