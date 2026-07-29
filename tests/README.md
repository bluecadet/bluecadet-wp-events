# Bluecadet Events — Tests

Automated tests for the plugin. The suite runs against a **real WordPress + MariaDB** (most of what this plugin does — writing post meta, the custom `bc_events` table, firing hooks, generating recurring children — only makes sense against a live WP), plus a thin unit layer for pure logic.

- **48 tests / ~438 assertions** across two suites: `unit` and `integration`.
- Environment is provided by [`@wordpress/env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) (Docker). Nothing WordPress-related lives in the repo or in a Composer install.
- All test tooling is `require-dev` + `export-ignore`d, so a production `composer require` of this plugin ships **none** of it.

---

## Requirements

- **Docker** (running) — `wp-env` spins up WordPress + MySQL containers.
- **Node** — to run `wp-env`.
- **Composer** — for the PHP dev dependencies (PHPUnit, `wp-phpunit/wp-phpunit`, `yoast/phpunit-polyfills`).

## One-time setup

```bash
composer install                      # installs PHPUnit + WP test scaffolding (require-dev)
npm install --legacy-peer-deps        # installs @wordpress/env (repo has a pre-existing peer-dep quirk)
npm run env:start                     # boots the WP + MySQL containers (first run pulls images)
```

> The custom `bc_events` table is created automatically by the test bootstrap (`Activate::activate()`), because `register_activation_hook` does not fire under the test runner.

## Running the tests

Via npm (runs PHPUnit inside the `wp-env` test container):

```bash
npm run test:php            # everything (unit + integration)
npm run test:unit           # unit suite only
npm run test:integration    # integration suite only
```

Handy variations (run PHPUnit directly in the container):

```bash
# a single test class
npx wp-env run tests-cli --env-cwd=wp-content/plugins/bluecadet-events vendor/bin/phpunit --filter RecurringEventTest

# a single test method
npx wp-env run tests-cli --env-cwd=wp-content/plugins/bluecadet-events vendor/bin/phpunit --filter test_weekly_on_date_creates_a_child_per_occurrence

# by @group (e.g. recurring, trash-delete, hooks, uninstall, db-table)
npx wp-env run tests-cli --env-cwd=wp-content/plugins/bluecadet-events vendor/bin/phpunit --group recurring

# readable list of every test
npx wp-env run tests-cli --env-cwd=wp-content/plugins/bluecadet-events vendor/bin/phpunit --testdox
```

Stop the environment when done:

```bash
npm run env:stop
```

---

## Layout

```
tests/
  bootstrap.php            # loads the WP test lib, the plugin, and creates the bc_events table
  TestCase.php             # base class: helpers, factories, and both editor "save drivers"
  Unit/
    RRuleBuilderTest.php
  Integration/
    ActivationTest.php
    EventSaveTest.php
    RecurringEventTest.php
    CustomOccurrencesTest.php
    RecurrenceEditTest.php
    TrashDeleteTest.php
    EventsTableTest.php
    HooksTest.php
    UninstallTest.php
```

Suites are defined in `phpunit.xml.dist` at the project root (`unit` → `tests/Unit`, `integration` → `tests/Integration`).

---

## The base `TestCase`

Most integration tests extend `BluecadetEvents\Tests\TestCase`, which provides:

- **Synchronous background processing** — recurring child events are normally created by an async `WP_Background_Process` queue. `set_up()` flips the `bc_events/background/sync` filter on, so the queue drains inline and children exist immediately after a save. (That filter/seam is default-off in production.)
- **An authenticated administrator** — both save paths gate on capabilities.
- **Per-test plugin registration** — `WP_UnitTestCase` resets global registration (post types, registered meta) between tests, and the plugin only registers on `init` (once at bootstrap). `TestCase` re-registers the post types + meta each test so the REST/Gutenberg path keeps its `show_in_rest` meta.
- **Two editor "save drivers"** that let a test push the *same* event through each editor and assert identical results:
  - `save_via_post($id, $meta, $title, $content)` — the **Classic** path: sets `$_POST` + a valid nonce and fires `save_post`.
  - `save_via_rest($id, $meta, $title)` — the **Gutenberg** path: dispatches a real REST request so the `register_post_meta` sanitize callbacks run.
- **Factories:** `new_event()`, `make_single_event()`, `make_weekly_recurring_event()`.
- **Custom-table + queue helpers:** `event_row($post_id)`, `child_event_ids($master)`, `db()`, `assertQueuesDrained()`.

---

## What each test file covers

### `Unit/RRuleBuilderTest.php`
Pure RRULE logic (no DB): consecutive-count generation, the `MAX_OCCURRENCES` safety cap for open-ended rules, and the guard that a missing/non-numeric start timestamp yields no dates.

### `Integration/ActivationTest.php`
The `bc_events` custom table is created on activation, with the expected columns and indexes.

### `Integration/EventSaveTest.php`
A single (non-recurring) event lands identically whether saved via **Classic** or **Gutenberg** — same meta, same `bc_events` row, no background work. (Editor parity.)

### `Integration/RecurringEventTest.php`
The recurrence matrix: weekly / daily / monthly / consecutive frequencies, both end types (`on_date`, `after_x`), classic-vs-REST parity for a recurring event, and child slug generation (200-char trim preserving the date suffix). Asserts the master row is `is_parent`, and one correctly-dated child per occurrence.

### `Integration/CustomOccurrencesTest.php`
Specific added dates (custom occurrences) create children on those dates; omissions (exclusions) remove dates from the frequency set.

### `Integration/RecurrenceEditTest.php`
Editing a recurring event: shortening the range regenerates and deletes orphaned children (posts + rows); an identical-config re-save updates children **in place**; `child_deny_override` preserves a hand-edited child's content.

### `Integration/TrashDeleteTest.php`
Trashing a parent cascades to children; trash → untrash restores them **without duplicating** (the historical "trash duping" regression); permanent parent delete removes every child post + row; single-event delete removes its row; trashing one child leaves siblings/parent untouched.

### `Integration/EventsTableTest.php`
Focused custom-table checks: inserts write the expected columns; a re-save updates the **same** row in place (no duplicate); recurring writes one master row + one row per child (master + children = the full set).

### `Integration/HooksTest.php`
The `Hooks` filter surface is defensive: a user filter returning the wrong type or garbage never fatals or returns an invalid value — it falls back to the documented default. Also verifies valid overrides are honored and the enforcement logic (`archive_settings` clamping, `frequency_options` allow-list, `taxonomy_query_field` clamp).

### `Integration/UninstallTest.php`
Uninstall preference resolution (default keep; saved option; `bc_events/uninstall/delete_data` filter override; a non-bool filter return can't trigger deletion) and the `delete_all_data()` routine (removes posts, drops the table, deletes the option).

---

## Writing a new test

Extend `BluecadetEvents\Tests\TestCase`, put it in `tests/Integration` (or `tests/Unit` for pure logic), name the file `*Test.php` and methods `test_*`, and add a `@group` tag. Example:

```php
namespace BluecadetEvents\Tests\Integration;

use BluecadetEvents\Tests\TestCase;

/** @group recurring */
class MyThingTest extends TestCase {
    public function test_it_does_the_thing(): void {
        $master = $this->make_weekly_recurring_event();
        $this->assertCount( 5, $this->child_event_ids( $master ) );
    }
}
```

---

## Gotchas worth knowing

- **Transactional isolation.** `WP_UnitTestCase` wraps each test in a DB transaction that is rolled back on teardown, so row writes (posts, meta, `bc_events` rows) undo automatically. **DDL breaks this** — `CREATE/DROP TABLE` implicitly commits. The table is therefore created once in `bootstrap.php`, and `UninstallTest`'s drop test disables the harness's temporary-table query filters and recreates the real table in `tear_down` (and runs last so its commit can't affect other tests).
- **Async is made synchronous.** Anything asserting recurring children relies on the `bc_events/background/sync` seam being on (handled in `TestCase::set_up`). Without it, `dispatch()` would fire a loopback that never runs under PHPUnit.
- **Not covered here.** The deactivate **modal UI** (JS/DOM) and the AJAX nonce/cap wrapper aren't exercised by PHPUnit — that would need a browser/E2E layer. The server-side persistence and preference resolution *are* covered. Give the modal a quick manual smoke test after changes.
- **Timezone.** Expected occurrence timestamps are computed with `wp_timezone()` the same way the engine does, so assertions hold regardless of the site timezone.
