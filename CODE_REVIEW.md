# Bluecadet Events — PHP Code Review

**Scope:** All PHP under `BluecadetEvents/` (45 files, ~9,500 lines) plus `global_functions/helpers.php` and the plugin bootstrap.
**Reviewer perspective:** Experienced PHP/WordPress developer. Goal is incremental, high-value improvement — **not** a rewrite.
**Date:** 2026-07-01

---

## 1. Executive summary

This is a well-structured plugin for its size. PSR-4 autoloading, typed properties in the newer files, a dedicated custom table with sensible indexes, `$wpdb->prepare()` used consistently in the data layer, and a clean filter/hook surface (`Plugin\Hooks`) all show real care. The recurring-event engine is genuinely complex and mostly holds together.

The improvements fall into a few themes, roughly in priority order:

1. **A handful of real correctness bugs** — most notably a duplicated `handle_always()` call that runs the save pipeline twice, and a broken `is_past()` helper. These are worth fixing first.
2. **Inconsistent class lifecycle patterns** — the codebase mixes *four* different "how do I bootstrap a class" idioms. This is the single biggest quality-of-life win and directly matches what you asked about. Standardizing here makes the whole codebase easier to reason about.
3. **Duplication that a base class / trait / config array would collapse** — the 3× `MetaKeys`, 3× `RegisterMeta`, and 3× `register_post_type` blocks are the clearest examples.
4. **Data objects using public mutable properties** where `readonly` + constructor promotion (getters/setters where behavior is needed) would be the modern PHP-standards approach.
5. **Performance** — mostly per-row / per-save repeated DB lookups that memoization or cache-priming fixes.
6. **Dead code** — two whole files are unreachable and should be deleted.

Nothing here requires a rewrite. Most of it is mechanical and can be done file-by-file.

---

## 2. What's already good (keep doing this)

- **`DatabaseHelpers.php`** is the strongest file: every query is `$wpdb->prepare()`d or uses `$wpdb->update/delete` with format specifiers, good docblocks, clear single-responsibility methods. This is the model for the rest of the data access.
- The **custom `bc_events` table** has thoughtful composite indexes (`event_start_parent`, `parent_update`) matched to the query patterns.
- **`Plugin\Hooks`** centralizes the filter surface with consistent naming and documentation — a good pattern.
- The **newer editor files** (`MetaBoxPatterns.php`, `Event.php`, `ChildEvent.php`, `RestRoutes.php`, `Gutenberg.php`) are typed, escape their output, use `MetaKeys`/`Settings` instead of magic strings, and set `permission_callback` on REST routes. They show the target quality bar — the older files just need to catch up to them.

---

## 3. Class patterns & standardization (your main question)

### 3.1 Standardize the class-bootstrap pattern — currently there are four

Right now the codebase uses **four different idioms** to initialize a class, which is exactly the "multiple Singleton-style patterns that are different" problem you flagged:

| Pattern | Used by | Notes |
|---|---|---|
| **Classic lazy singleton** `get_instance()` + private `__construct`/`__clone` | `DatabaseHelpers`, `MetaKeys` (×3), `TemplateHelpers` | The "real" singletons |
| **Static `__init()` guard flag** | `Settings`, `BackgroundProcesses` | Not a singleton — a static holder |
| **`new X` with constructor side-effects** (registers hooks) | `PostTypes`, `RegisterMeta` (×3), `Save\Events`, `Trash\Events`, `Gutenberg`, `EditorAssets`, `RestRoutes`, `AdminEventsViews`, `ICS\TemplateRedirect`, `Templates\Query`, `PublicEndpoints`, … | ~16 files |
| **Vestigial singleton** — full `get_instance()` scaffolding that is never used | `Plugin\Hooks` | See 3.2 |

Recommendations:

- **Rename `__init()` → `init()` or `boot()`.** The `__` double-underscore prefix is reserved by PHP for magic methods (`__construct`, `__get`, …). Using it for a normal static bootstrap (`Settings::__init()`, `MetaKeys::__init()`) is misleading. — `Settings.php:29`, `BackgroundProcesses.php:13`, `MetaKeys.php:19` (×3).
- **In `MetaKeys`, `__init()` just calls `get_instance()`** (`MetaKeys.php:19-21`). That's two bootstrap mechanisms for one class — drop `__init()` and call `get_instance()` (or better, static accessors — see 3.3).
- **Pick one convention for hook-registering classes.** The `new X` constructor-side-effect style is common in WP and is fine, but consider a shared static entry point (e.g. a small `Bootable` interface with `public static function init(): void`) so `Plugin\Init` reads as a uniform list. Even without an interface, being consistent about "constructor only assigns; a `register()` method adds hooks" makes every class testable and predictable.

### 3.2 `Plugin\Hooks` is a singleton that is never instantiated — remove the scaffolding

`Hooks.php:7-24` defines `private static $_instance`, a private `__construct`, `__clone`, and `get_instance()` — the full singleton machinery. But **every method in the class is `public static`** and every caller invokes them statically (e.g. `Hooks::hook_filter_use_event_locations()` in `Init.php:32`). The instance is never created. The singleton scaffolding is dead code.

**Fix:** delete `$_instance`, `__construct`, `__clone`, and `get_instance()`. The class is (correctly) a static utility; make that explicit. (`get_instance()` also uses `==` instead of `===` at line 19 — a moot point once removed.)

### 3.3 Collapse the 3× `MetaKeys` classes into one base class

`Admin/Meta/MetaKeys.php`, `Admin/Meta/Series/MetaKeys.php`, and `Admin/Meta/Locations/MetaKeys.php` are **byte-for-byte identical** for lines 6–35 (the `$_instance`, `__construct`, `__clone`, `__init`, `get_instance`, `get_keys`). Only `generate_keys()` differs (plus `get_event_save_keys()` on the events one).

Extract an abstract base:

```php
abstract class AbstractMetaKeys {
    private static array $instances = [];
    protected array $keys;

    final private function __construct() {
        Settings::init();
        $this->keys = $this->generate_keys();
    }

    abstract protected function generate_keys(): array;

    public static function get_instance(): static {
        return self::$instances[static::class] ??= new static();
    }

    public static function get_keys(): array {
        return static::get_instance()->keys;
    }
}
```

Each subclass then implements only `generate_keys(): array`. Note the `[static::class]` keying is required so the three subclasses don't share one `$_instance`. This removes ~25 duplicated lines per file and gives you one place to change the pattern.

### 3.4 Consider typed accessors / an enum for meta keys instead of a string-keyed array

Today callers index a runtime array with string literals: `$this->keys['freq']`, `$this->keys['is_parent']` (throughout the Save/ and Editor/ code). A typo like `$keys['freqx']` is a silent `null`/undefined-index bug with no IDE autocomplete.

Because the keys are built at runtime from `Settings::$events_meta_ns`, a plain `const`/`enum` of the *full* keys won't work. But you can define the **logical** suffixes as an enum and resolve the prefix once:

```php
enum EventMetaKey: string {
    case StartDate = 'start_date';
    case Frequency = 'frequency';
    // ...
}
// resolver:  Settings::$events_meta_ns . $key->value
```

That buys autocomplete + typo safety while keeping the dynamic namespace. This is optional polish; the base class in 3.3 is the higher-value change.

### 3.5 Collapse the 3× `RegisterMeta` classes with a config-driven base

`Admin/Meta/RegisterMeta.php`, `Series/RegisterMeta.php`, `Locations/RegisterMeta.php` share an identical constructor (`$this->keys = MetaKeys::get_keys(); add_action('init', …)`), an identical `$auth` closure, an identical `$string_args` block (`Meta:27-33`, `Series:24-30`, `Locations:24-30`), and the same `register_meta()`→`register_event_meta()` indirection. The events registrar then hand-writes ~40 nearly-identical `register_post_meta(... array_merge($string_args, ['description' => …]))` calls (`RegisterMeta.php:70-323`).

**Recommendation:** represent each meta field as *data* (key → type + description + optional sanitize/schema) and have a base `register()` loop over it using arg-template factories (`string_args()`, `int_args()`, `bool_args()`, `array_args()`). The events registrar becomes one config array; Series and Locations become a handful of lines each. Even a minimal step — a local helper `$reg = fn($key, $base, $extra = []) => register_post_meta($type, $this->keys[$key], array_merge($base, $extra));` — removes most of the repetition.

This also fixes a **latent bug**: the meta *type* is currently declared in two places that have drifted — `MetaKeys::get_event_save_keys()` says `start_timestamp` is `'string'` (`MetaKeys.php:86`) but `RegisterMeta` registers it as `integer` (`RegisterMeta.php:78`); `child_deny_override` is `'string'` vs `boolean` (`MetaKeys.php:110` / `RegisterMeta.php:326`). A single config array is one source of truth and removes this class of drift.

### 3.6 `register_post_type` is duplicated 3× in `PostTypes.php`

The events/locations/series `register_post_type` arg arrays (`PostTypes.php:51-168`) are ~90% identical. A helper building the shared defaults (menu args, `show_in_rest`, rewrite shape, the `apply_filters` wiring) parameterized by machine name / icon / labels would remove the bulk. Also note the confusing self-overwrite `$labels = new LabelMaker(...); $labels = $labels->labels;` (`PostTypes.php:48-49, 92-94, 135-137`) — use a distinct variable name.

### 3.7 Data objects: prefer `readonly` + constructor promotion over public mutable bags

The recurrence value objects expose public mutable properties that other classes reach in and mutate:
- `RecurringEvent` — all public (`RecurringEvent.php:16-121`)
- `EventClone` (`EventClone.php:6-15`), `RecurringEventDate` (`RecurringEventDate.php:8-10`), `EventPost` (already uses promotion — just add `readonly`)

For the pure DTOs (`EventPost`, `RecurringEventDate`, `EventClone`), use `readonly` constructor-promoted properties (PHP 8.1+) so they're immutable after construction:

```php
final class EventPost {
    public function __construct(
        public readonly string $modified,
        public readonly int    $post_id,
        public readonly string $post_slug,
        public readonly int    $event_start,
        // ...
    ) {}
}
```

For `RecurringEvent`, at minimum make the set-once fields (`parent_post_id`, `parent_post`, `timezone`, `keys`) `readonly`. This is the "PHP standards" answer to "should this have getters/setters": for immutable data, public `readonly` properties are the idiomatic modern choice — you only need getters/setters when there's actual behavior/validation on access.

### 3.8 `FrequencyArgs` is a setter-bag that's immediately flattened — pick one style

`FrequencyArgs.php` defines 13 private fields, 13 setters, and a `to_array()`. `EventsSaveAction.php:135-150` calls all 13 setters then immediately `->to_array()`, discards the object, and everything downstream uses the array (`$args['frequency']`). The object gives no encapsulation benefit. Either (a) use `readonly` promotion and pass the *object* into `RRuleBuilder` for type-safe `$args->frequency`, or (b) drop the class and build the array inline. It's currently the worst of both. Also note it mixes `setUseFrequency()` (camelCase) with `to_array()` (snake_case) — the rest of the codebase is snake_case.

### 3.9 `LabelMaker` — public mutable `$labels`, non-PSR method name

`LabelMaker.php`: `public $labels` (line 20) is untyped and mutated externally (`PostTypes.php:93,136`). Type it (`public array $labels`) or expose a `get()` method. Method `Create_Labels()` (line 37) is StudlyCase-with-underscore — rename to `create_labels()` to match the codebase.

### 3.10 `Logger` logs unconditionally in production

`Utils/Logger.php` is `error_log(print_r($data, true))` gated by a hardcoded `$can_log = true` — so it logs in production. Either gate it on `WP_DEBUG`/`WP_DEBUG_LOG` (or a `Settings` flag) and add a `[BluecadetEvents]` prefix + log levels to justify the abstraction, or inline `error_log` at call sites. As-is it's an abstraction that doesn't abstract *and* leaks to prod logs.

---

## 4. Correctness bugs (verified — fix these first)

### 4.1 `handle_always()` runs twice for recurring events — **confirmed**

`EventsSaveAction.php:56` calls `$this->handle_always()` inside the recurring branch, and line 59 calls it again unconditionally. For any recurring (or previously-recurring) event the full method body — including a second `upsert_event` and duplicate `set_meta_update`s — executes twice per save. Remove the call at line 56 (or restructure so it runs exactly once). Note also `run()` always `return true` with a `// TODO - Handle Errors` (line 62) despite being typed `bool|\WP_Error`.

### 4.2 `TemplateHelpers::is_past()` is broken — **confirmed**

`TemplateHelpers.php:546-554`:
```php
$param = Hooks::hook_filter_archive_settings();  // returns an ARRAY
if ( $param ) {
    return isset($_GET[$param]);                  // isset($_GET[<array>]) — illegal offset, always false
}
```
`hook_filter_archive_settings()` returns the whole settings array (`Hooks.php:44`), not a scalar. `QuerySetters.php:34` does it correctly. Fix:
```php
$settings = Hooks::hook_filter_archive_settings();
return isset($_GET[$settings['past_parameter']]);
```

### 4.3 `clear_recurring` / `clear_dates_meta` omit the consecutive-frequency keys

`EventCloneBuilder::clear_recurring` (`:105-122`) and `clear_dates_meta` (`:134-158`) strip recurrence config from clones, but their key lists omit `freq_consecutive_buffer` / `freq_consecutive_count` — which the delete path in `EventsSaveAction::handle_child_events_delete` (`:271-290`) *does* include. So those two meta values get copied onto child clones. Centralize these lists on `MetaKeys` (e.g. `MetaKeys::recur_keys()`) so all three sites stay in sync (see 6.2).

### 4.4 Discarded `WP_Error`s / unbounded RRULE

`RRuleBuilder.php:47` constructs `new \WP_Error(...)` but never returns/throws it (missing `return`); line 213 assigns `$error` and never uses it. Consequently, when `end_type` is neither `on_date` nor `after_x` (`:208-214`), no `until`/`count` is added and `handle_frequency`'s `foreach ($date_period as $date)` (`RecurringEventsArray.php:57`) can iterate an effectively unbounded RRULE → timeout/OOM risk. Return/throw these errors and add a hard occurrence cap as a safety net.

### 4.5 Unvalidated timestamps → "January 1970" writes

`handle_always` (`EventsSaveAction.php:312-316`) does `$d->setTimestamp($start_timestamp)` with no check; empty meta coerces to epoch 0 and writes `start_month_year = "January 1970"`. Same unchecked access in `RecurringEventsArray.php:22,25` and `RecurringDatesArrayBuilder.php:24,27`. Validate non-empty/numeric before building `DateTime`.

### 4.6 Falsy-zero and loose-comparison patterns

- Throughout `TemplateHelpers` getters: `if ( $field = get_post_meta(...) )` (e.g. `:107,129,205`) treats a legitimate `0`/`'0'` as "no value." Low real-world risk for timestamps, but a latent pattern.
- `RecurringEvent.php:140-141` assign raw `get_post_meta()` (string) directly into `bool` typed props; `get_meta` (`:166`) uses `== 1`. Cast explicitly and use `===`.
- `DatabaseHelpers.php:37` `== null` → use `=== null`.
- `Trash/Events.php:91` `foreach ( $child_events as &$child_event )` uses an unnecessary reference (values are cast to int, never written back) — a classic dangling-reference footgun; drop the `&`.

---

## 5. Performance

### 5.1 Per-row relationship queries on the events list screen (biggest win)

`AdminEventsViews.php` — `post_states()` (`:71`), `columns_content()` (`:142`), and `remove_cpt_trash_action()` (`:494`) each call **both** `is_recurring_child()` and `is_recurring_parent()` for the same post, per row. That's up to ~6 uncached custom-table queries × N rows (≈120 queries on a 20-row screen), plus `get_last_child_event(_end)` in `columns_content`. `is_recurring_parent()` also `SELECT`s *all* child ids just to test existence.

**Fix:** add a request-scoped memo in `DatabaseHelpers` (e.g. `$this->child_of = []`, `$this->parent_of = []`) or a single `get_relationship($post_id)` returning both facts in one query; use `SELECT EXISTS(...)` / `LIMIT 1` for existence checks. This also helps the editor screen (see 5.3).

### 5.2 Repeated relationship + meta queries per save

`RecurringEvent::__construct` (`:132-145`) fires 5 round-trips on **every** save of any `bc-events` post (3× `get_post_meta` + `is_recurring_parent` + `is_recurring_child`), including non-recurring ones. Then `compile_event_meta` (`:106`) bulk-loads all meta anyway — so the 3 individual `get_post_meta` calls are redundant. Load all meta once and read the flags from it; defer the child lookup until recurrence is actually indicated.

### 5.3 DB queries before the screen guard (editor)

`EditorScreen.php:44-47` runs `is_recurring_child()`/`is_recurring_parent()` *before* the post-type/screen check on line 49 — so two custom-table queries fire on **every** post/page edit screen. Move the guard to the top and `return` early. The same recurrence facts are also recomputed in `RegisterMetaBoxes.php:58` and `Event.php:32` within one page load — memoize (5.1) and reuse.

### 5.4 `posts_per_page => -1` on unbounded sets

- `Rest/PublicEndpoints.php:82` — public datepicker endpoint loads *all* events, then does 2× `get_post_meta` per post in a loop (`:109-110`) = 2N meta reads on the cold path. Prime the cache (`update_meta_cache('post', $ids)`) before the loop, or fetch timestamps in one `$wpdb` join. (See also 7.x — this endpoint has security issues.)
- `QuerySetters.php:248` — month view forces `-1`. A calendar month can load thousands of posts; cap or paginate.

### 5.5 N+1 in `TemplateHelpers::get_child_posts`

`:433-442` calls `get_post()` (and 2× `get_post_meta`) per child id. Prime with `_prime_post_caches($child_ids)` (or one `get_posts(['post__in' => $child_ids])`) before mapping. Also `[...$child_post]` object-spread of a `WP_Post` (`:434-439`) is fragile — the result isn't a `WP_Post` and may surprise callers.

### 5.6 Uncached distinct-meta scan on every list render

`AdminEventsViews.php:399-405` runs a `postmeta JOIN posts` `DISTINCT` scan (meta_value isn't indexed for `DISTINCT`) on every list-screen load to build the month/year filter. Wrap in a transient invalidated on event save/trash.

### 5.7 Redundant `get_post_meta` across `TemplateHelpers` getters

`get_formatted_start_date/time/date_time` (`:129,153,177`) each independently re-fetch the same `start_timestamp` meta and rebuild a `DateTime`. WP caches meta per-request so it's not DB N+1, but it's redundant work. Have the format methods delegate to `get_start_timestamp()` and a single `format_meta_date()` helper (also fixes duplication — 6.4).

---

## 6. Duplication & dead code

### 6.1 Delete two dead files

- **`MetaBoxPatternsOld.php`** — unreferenced anywhere, *and* unloadable: its namespace is `...ClassicEditor\Forms` (line 3) but it lives in `FormContent/`, so PSR-4 could never autoload it. Fully superseded by `MetaBoxPatterns.php`. It also carries unescaped-output XSS patterns and a broken `-1` query. **Delete it.**
- **`RecurringDatesArrayBuilder.php`** — referenced only by its own class definition; the live path uses `RecurringEventsArray` (`EventsSaveAction.php:196`). Confirm and delete. (The two are ~90% identical, so this is also a duplication fix.)

### 6.2 Recur-key lists copy-pasted 3–4×

The same recurrence meta-key lists appear (with drift — see 4.3) in `EventsSaveAction::handle_child_events_delete` (`:271-290`), `EventCloneBuilder::clear_recurring` (`:105-122`), and `clear_dates_meta` (`:134-158`). Define named groups on `MetaKeys` and reference them everywhere.

### 6.3 Duplicated post-type list

`[ Settings::$events_machine_name, Settings::$locations_machine_name, Settings::$series_machine_name ]` is rebuilt inline in `EditorAssets.php:16-20,78`, `RegisterMetaBoxes.php:39-41`, `EditorScreen.php:49-51`, `Gutenberg.php:82-84`. Add `Settings::event_post_types(): array` and reuse. (Also: some of these `in_array()` calls omit the strict `true` third arg — inconsistent.)

### 6.4 The 6 formatted-date getters

`TemplateHelpers.php:122-282` — `get_formatted_{start,end}_{date,time,date_time}` are copy-paste; only the meta key and format string differ. Collapse to one `format_meta_date(int $post_id, string $which, string $format)`. Similarly `date_from_timestamp`/`time_from_timestamp` (`:297-316`) duplicate the DateTime-from-timestamp block.

### 6.5 Duplicated `hook_filter_archive_settings()` calls, ICS link, day/week/month view logic

- `hook_filter_archive_settings()` is called in `Query.php:61,123`, `QuerySetters.php:27`, and `TemplateHelpers.php:547` with no per-request memoization.
- `get_ics_link` is identical in `TemplateHelpers.php:604-610` and `TemplateRedirect.php:73-75` — pick one canonical source.
- `set_day_view` / `set_week_view` / `set_month_view` (`QuerySetters.php`) repeat the same parse→clone-now→setTime→compute-range→set-decorators shape and could be one `set_range_view($period)`.

### 6.6 Commented-out dead code

Numerous stale commented blocks: `Hooks.php` (the "MAYBE DELETE?" section `:695-760`, commented methods), `Series/RegisterMeta.php:50-90`, `Locations/RegisterMeta.php:46-86`, `QuerySetters.php:139-195,432-451`, `EditorScreen.php:30-32`, commented `use` in `RegisterMetaBoxes.php:7`. Delete — git history preserves them.

### 6.7 Duplicate hook methods in `Hooks.php`

`hook_filter_locations_rewrite_slug()` (`:461`) and `hook_filter_rewrite_slug()` (`:520`) return the same filter (`bc_events/locations/post_type/rewrite_slug`) with the same default. One is dead — remove it.

---

## 7. Security (WordPress hardening)

The data layer is safe (`$wpdb->prepare()` everywhere). The gaps are in output escaping and REST input validation.

### 7.1 Unescaped admin output (stored XSS surface)

- `AdminEventsViews.php:157-197` — `columns_content()` echoes post-meta-derived dates and `get_edit_post_link()` raw. Wrap in `esc_html()` / `esc_url()` (and guard `get_edit_post_link()` against `null`).
- `AdminEventsViews.php:462-471` — `create_select()` echoes DB-sourced `$value`/`$option` into attributes and text unescaped. Use `esc_attr()` / `esc_html()`.
- `EditorScreen.php:92` — `$this->parent_link` and `get_the_title()` echoed without `esc_url()` / `esc_html()`. (`ChildEvent.php:58,69` does this correctly — follow it.)

### 7.2 Public datepicker endpoint needs input validation

`Rest/PublicEndpoints.php`:
- `$tax` from the request (`:53`) is passed into `WP_Query`'s `tax_query` (`:87-93`) with no `taxonomy_exists()` check — callers can probe arbitrary taxonomy names.
- `$cache_key` is built directly from request params (`:55`) — an attacker can generate unbounded distinct transients, each triggering an uncached `-1` query (amplification / options-table bloat). Hash the variable portion: `'bc-events-datepicker-' . md5($tax . '|' . implode(',', (array) $term))`.
- The response echoes back `$args` (`:96`) and `cache_key` — drop these from a public payload.

### 7.3 `$_GET`/`$_POST` handling

- `AdminEventsViews.php` reads many `$_GET` values; comparisons against known constants are fine, but `$date_val` (`:310`) flows into a `meta_query` value with no `wp_unslash()` + `sanitize_text_field()`. Sanitize before use.
- `Save/Events.php:28,76-104` — nonce and managed-key `$_POST` values are read without `wp_unslash()` before `sanitize_text_field()`; WP slashes superglobals, so backslashes in user content get corrupted. Add `wp_unslash()`.
- `Save/Events.php:119` (`handle_wp_after_insert_post`, priority 99) runs the full recurrence engine gated only by `current_user_can('edit_post')` — no nonce, and it fires on REST/programmatic saves too. Confirm this is intended and align its post-type guard (`'bc-events'` literal) with the nonce path (`Settings::$events_machine_name`).
- `TemplateHelpers.php:582-590` builds pagination URLs by concatenating raw `$_GET` keys/values with no `urlencode()`/`esc_url()` — reflected-XSS risk if printed unescaped, and breaks on array/`&`/space values. Use `add_query_arg()` + `esc_url()`.

### 7.4 ICS output escaping (`ICS/TemplateRedirect.php`)

`escape()` (`:113-115`) is applied to some fields but **not** to `PRODID` (`:95`), `URL` (`:105`), `ORGANIZER;CN` (`:106`), or `UID` (`:84,99`). A blog name / organizer containing `"`, `:`, or a newline can break or inject VCALENDAR properties. Also there's no RFC-5545 75-octet line folding, so long titles produce technically-invalid `.ics` that some clients reject. Escape every text value (and `"` in the quoted `CN`), and add line folding.

---

## 8. PHP standards & modernization

### 8.1 Add `declare(strict_types=1);`

**Zero** files declare strict types. Combined with the heavy `(int)`/`(string)` casting and `==` comparisons, this invites type-juggling bugs. Add `declare(strict_types=1);` to each file (do it as you touch files — it can surface latent coercion bugs, so not all-at-once).

### 8.2 Missing type & return declarations

Widespread. Highlights:
- Untyped properties: `RRuleBuilder.php:8-9` (`$args`, `$timezone`), `RegisterMeta.php:8` + Series/Locations (`$keys`), `Logger.php:13` (`$can_log`), `EditorScreen.php:18-21`, `EventsSaveAction.php:38` (`mixed $background_event_handler` — should be `BackgroundEventHandler`).
- Missing `: void`/return types on most render/handler/setter methods across `AdminEventsViews`, `MetaBoxPatterns`, the `RegisterMeta` trio, `FrequencyArgs`, `RRuleBuilder`, `EventCloneBuilder`, `Gutenberg`, `EditorAssets`.
- Missing visibility keywords: several `AdminEventsViews` methods (`:65,116,140,213,229,370,462,480`) default to public implicitly; `create_select` is only used internally → mark `private`.
- `Hooks.php` return types are inconsistent — some methods use `: mixed` where the value is always `bool`/`string`/`array` (e.g. `hook_filter_use_event_series() : mixed`), others have no return type at all (`hook_filter_archive_settings`, `hook_filter_use_event_locations`). Tighten to the real type.

### 8.3 Magic strings & numbers → constants/enums

- Post type `'bc-events'` hardcoded in `AdminEventsViews.php:67,378`, `Save/Events.php:26`, `PublicEndpoints` meta keys `'_bc_events_start_timestamp'` (`:109-110`) — all while `Settings`/`MetaKeys` exist and are used correctly *elsewhere in the same files*. Route these through `Settings`/`MetaKeys`.
- Nonce strings `'bc_save_meta'` / `'bc_meta_nonce'` are inline in both the form (`Event.php:46`, `ChildEvent.php:39`) and the save handler (`Save/Events.php:27-28`) — a typo silently breaks saving. Define once (e.g. `Settings::NONCE_ACTION` / `NONCE_FIELD`).
- View names (`'list'|'week'|'month'|'day'`), query modes (`'upcoming'|'past'|'range'`), frequency strings (`'daily'|'weekly'|'monthly'|'consecutive'`), weekday/schedule names, and pagination params (`'day-of'|'week-of'|'month-of'`) are bare strings duplicated across `Query`, `QuerySetters`, `TemplateHelpers`, `RRuleBuilder`. Enums or class constants would centralize and type-check them.
- Magic numbers: `posts_per_page` `100`/`-1` (`QuerySetters.php:496,248`), slug length `200` (`EventCloneBuilder.php:200-201`).

### 8.4 Type/coercion nits

- `QuerySetters.php:17,33,71` — `private int $now_ts` assigned `->format('U')` (a **string**). Use `->getTimestamp()` or `(int)`.
- `is_past` computed with string-numeric comparison of `->format('U')` across views (`QuerySetters.php:210,298,366`) — cast to `int` (PHP 8 changed string-numeric semantics).
- `TemplateHelpers.php:55-57` — `esc_html()` applied to *date-format strings* passed to `DateTime::format()`. Escaping a format pattern is wrong and can corrupt formats containing `<`/`>`/quotes. Escape at output, not on the pattern.
- `QuerySetters.php:58-60` sets arbitrary **dynamic properties** on `WP_Query` (`$this->query->$arg`), which PHP 8.2 deprecates for non-`#[AllowDynamicProperties]` classes. Store in `$query->query_vars` or a single namespaced array instead.

### 8.5 Naming / docblock cleanup

- Class/file mismatch: `Templates/Query.php` defines class `Queries` (`:7`).
- Serialized meta key typo `'occurences'` (`EventsSaveAction.php:162`) is now load-bearing (it's diffed) — change carefully with a migration.
- Placeholder docblocks: `TemplateRedirect.php:67-72` ("Undocumented function"), `TemplateHelpers.php:73` (`@param [type]`), typos ("Taxonomied" `Query.php:23`, "Plural for of" `LabelMaker`).
- `admin_queries()` is a `pre_get_posts` callback but is annotated to return `\WP_Query` (`AdminEventsViews.php:229`); WP ignores the return value — the annotation misleads.

---

## 9. Suggested order of work

**Tier 1 — correctness (small, high impact):**
1. Remove the duplicate `handle_always()` call (§4.1).
2. Fix `is_past()` (§4.2).
3. Add the missing consecutive keys / centralize recur-key lists (§4.3, §6.2).
4. Return/throw the discarded `WP_Error`s and cap RRULE size (§4.4).
5. Delete the two dead files (§6.1).

**Tier 2 — the standardization you asked about:**
6. Rename `__init()` → `init()`; strip the vestigial singleton from `Hooks` (§3.1, §3.2).
7. Extract `AbstractMetaKeys` and a config-driven `RegisterMeta` base — collapses the 3×+3× duplication and fixes the meta-type drift (§3.3, §3.5).
8. Make the DTOs `readonly` with constructor promotion (§3.7); resolve `FrequencyArgs` (§3.8).

**Tier 3 — performance:**
9. Memoize relationship lookups in `DatabaseHelpers`; move the editor screen guard above the queries (§5.1, §5.3).
10. Prime meta/post caches in the datepicker and `get_child_posts`; cache the month/year scan (§5.4, §5.5, §5.6).

**Tier 4 — hardening & polish (ongoing, as you touch files):**
11. Escape admin/ICS output; validate the public endpoint input (§7).
12. Add `declare(strict_types=1)`, type declarations, and route magic strings through `Settings`/`MetaKeys` (§8).

Each tier is independently shippable; none requires touching the overall architecture.
</content>
</invoke>
