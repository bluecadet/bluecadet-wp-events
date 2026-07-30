<?php
/**
 * Generate the Hooks reference page from the docblock tags in
 * BluecadetEvents\Plugin\Hooks.
 *
 * Dependency-free: reflects the class and parses each hook's summary, @param,
 * @return, @since, the custom @hook* grouping tags, and @example, then renders
 * a grouped, self-contained HTML page.
 *
 * Usage:
 *   php bin/docs/generate-hooks.php [output-file]
 * Default output: .docs-build/hooks/index.html
 *
 * @package BluecadetEvents
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use BluecadetEvents\Plugin\Hooks;

$output = $argv[1] ?? __DIR__ . '/../../.docs-build/hooks/index.html';

/**
 * Parse one method's docblock into a structured hook definition, or null if it
 * isn't a documented hook (no @hook tag).
 */
function bc_parse_hook_docblock( ReflectionMethod $method ) : ?array {
	$raw = $method->getDocComment();
	if ( ! $raw ) {
		return null;
	}

	$lines = preg_split( '/\R/', $raw );
	$lines = array_map(
		static function ( $line ) {
			$line = preg_replace( '#^\s*/\*\*?#', '', $line );
			$line = preg_replace( '#\s*\*/\s*$#', '', $line );
			$line = preg_replace( '#^\s*\*\s?#', '', $line );
			return rtrim( $line );
		},
		$lines
	);

	$hook = [
		'method'      => $method->getName(),
		'tag'         => null,
		'object_type' => 'Other',
		'category'    => 'General',
		'type'        => 'filter',
		'since'       => null,
		'return_type' => null,
		'return_desc' => null,
		'params'      => [],
		'description' => [],
		'example'     => [],
	];

	$in_example = false;

	foreach ( $lines as $line ) {
		$trimmed = trim( $line );

		if ( str_starts_with( $trimmed, '@hook ' ) ) {
			$in_example = false;
			$hook['tag'] = trim( substr( $trimmed, 6 ), " '\"" );
		} elseif ( str_starts_with( $trimmed, '@hook_object_type ' ) ) {
			$in_example = false;
			$hook['object_type'] = trim( substr( $trimmed, 18 ) );
		} elseif ( str_starts_with( $trimmed, '@hook_category ' ) ) {
			$in_example = false;
			$hook['category'] = trim( substr( $trimmed, 15 ) );
		} elseif ( str_starts_with( $trimmed, '@hook_type ' ) ) {
			$in_example = false;
			$hook['type'] = trim( substr( $trimmed, 11 ) );
		} elseif ( str_starts_with( $trimmed, '@since' ) ) {
			$in_example = false;
			$hook['since'] = trim( substr( $trimmed, 6 ) );
		} elseif ( str_starts_with( $trimmed, '@param ' ) ) {
			$in_example = false;
			if ( preg_match( '/^@param\s+(\S+)\s+\$(\S+)\s*(.*)$/', $trimmed, $m ) ) {
				$hook['params'][] = [ 'type' => $m[1], 'name' => $m[2], 'desc' => trim( $m[3] ) ];
			}
		} elseif ( str_starts_with( $trimmed, '@return' ) ) {
			$in_example = false;
			$rest  = trim( substr( $trimmed, 7 ) );
			$parts = preg_split( '/\s+/', $rest, 2 );
			$hook['return_type'] = $parts[0] ?? '';
			$hook['return_desc'] = trim( $parts[1] ?? '' );
		} elseif ( str_starts_with( $trimmed, '@example' ) ) {
			$in_example = true;
		} elseif ( str_starts_with( $trimmed, '@' ) ) {
			$in_example = false;
		} elseif ( $in_example ) {
			$hook['example'][] = $line;
		} elseif ( '' !== $trimmed && $trimmed !== $method->getName() ) {
			$hook['description'][] = $trimmed;
		}
	}

	return $hook['tag'] ? $hook : null;
}

/** Build a realistic add_filter/add_action usage snippet from the params. */
function bc_hook_usage_snippet( array $hook ) : string {
	$fn      = 'filter' === $hook['type'] ? 'add_filter' : 'add_action';
	$params  = $hook['params'];
	$arglist = $params
		? implode( ', ', array_map( static fn( $p ) => '$' . $p['name'], $params ) )
		: '$value';
	$count   = max( 1, count( $params ) );

	$body = 'filter' === $hook['type']
		? "\n    // ...\n    return " . ( $params ? '$' . $params[0]['name'] : '$value' ) . ";\n"
		: "\n    // ...\n";

	$priority = $count > 1 ? ", 10, {$count}" : '';

	return "{$fn}( '{$hook['tag']}', function ( {$arglist} ) {{$body}}{$priority} );";
}

// --- Collect + group -------------------------------------------------------

$class   = new ReflectionClass( Hooks::class );
$methods = $class->getMethods( ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC );

$grouped = [];
foreach ( $methods as $method ) {
	$hook = bc_parse_hook_docblock( $method );
	if ( ! $hook ) {
		continue;
	}
	$grouped[ $hook['object_type'] ][ $hook['category'] ][] = $hook;
}

ksort( $grouped );
foreach ( $grouped as &$cats ) {
	ksort( $cats );
}
unset( $cats );

$total = array_sum( array_map(
	static fn( $cats ) => array_sum( array_map( 'count', $cats ) ),
	$grouped
) );

// --- Render ----------------------------------------------------------------

$e      = static fn( $s ) => htmlspecialchars( (string) $s, ENT_QUOTES );
$anchor = static fn( $s ) => strtolower( preg_replace( '/[^a-z0-9]+/i', '-', (string) $s ) );

ob_start();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Bluecadet Events — Hooks Reference</title>
<style>
  :root { --fg:#1e1e1e; --muted:#646970; --accent:#2271b1; --border:#e0e0e0; --bg:#fff; --code:#f6f7f7; }
  * { box-sizing: border-box; }
  body { margin:0; font:16px/1.6 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif; color:var(--fg); background:var(--bg); }
  .wrap { max-width:960px; margin:0 auto; padding:32px 20px 80px; }
  header h1 { margin:0 0 4px; }
  header p { color:var(--muted); margin:0 0 24px; }
  nav { border:1px solid var(--border); border-radius:6px; padding:16px 20px; margin-bottom:40px; background:var(--code); }
  nav h2 { margin:0 0 8px; font-size:14px; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); }
  nav ul { margin:0; padding-left:18px; }
  nav a { color:var(--accent); text-decoration:none; }
  nav a:hover { text-decoration:underline; }
  h2.group { margin:48px 0 8px; padding-bottom:6px; border-bottom:2px solid var(--fg); }
  h3.cat { margin:28px 0 8px; color:var(--muted); font-size:14px; text-transform:uppercase; letter-spacing:.05em; }
  .hook { border:1px solid var(--border); border-radius:6px; padding:18px 20px; margin:14px 0; }
  .hook code.tag { font-size:16px; font-weight:700; color:var(--fg); }
  .badge { display:inline-block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; padding:2px 8px; border-radius:10px; margin-left:8px; vertical-align:middle; }
  .badge.filter { background:#e6f0f7; color:#0a4b78; }
  .badge.action { background:#eef7e6; color:#28660a; }
  .badge.since { background:#f0f0f1; color:#646970; }
  .hook .byline { color:var(--muted); font-size:13px; margin:6px 0 0; }
  .hook p.desc { margin:10px 0; }
  h4.sub { margin:16px 0 4px; font-size:12px; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); }
  table.params { border-collapse:collapse; width:100%; margin:4px 0 6px; font-size:14px; }
  table.params th, table.params td { text-align:left; padding:6px 10px; border-bottom:1px solid var(--border); vertical-align:top; }
  table.params th { color:var(--muted); font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.04em; }
  table.params td.nm code, table.params td.ty code { white-space:nowrap; }
  .ret code { white-space:nowrap; }
  pre { background:var(--code); border:1px solid var(--border); border-radius:6px; padding:12px 14px; overflow-x:auto; font-size:13px; }
  code { font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace; }
</style>
</head>
<body>
<div class="wrap">
<header>
  <h1>Hooks Reference</h1>
  <p>Bluecadet Events — <?php echo (int) $total; ?> filters &amp; actions. Generated from <code>Plugin\Hooks</code>.</p>
</header>

<nav>
  <h2>Contents</h2>
  <ul>
  <?php foreach ( $grouped as $object_type => $cats ) : ?>
    <li><a href="#<?php echo $e( $anchor( $object_type ) ); ?>"><?php echo $e( $object_type ); ?></a></li>
  <?php endforeach; ?>
  </ul>
</nav>

<?php foreach ( $grouped as $object_type => $cats ) : ?>
  <h2 class="group" id="<?php echo $e( $anchor( $object_type ) ); ?>"><?php echo $e( $object_type ); ?></h2>
  <?php foreach ( $cats as $category => $hooks ) : ?>
    <h3 class="cat"><?php echo $e( $category ); ?></h3>
    <?php foreach ( $hooks as $hook ) : ?>
      <div class="hook" id="<?php echo $e( $anchor( $hook['tag'] ) ); ?>">
        <code class="tag"><?php echo $e( $hook['tag'] ); ?></code>
        <span class="badge <?php echo $e( $hook['type'] ); ?>"><?php echo $e( $hook['type'] ); ?></span>
        <?php if ( $hook['since'] ) : ?><span class="badge since">since <?php echo $e( $hook['since'] ); ?></span><?php endif; ?>
        <p class="byline"><code>Hooks::<?php echo $e( $hook['method'] ); ?>()</code></p>

        <?php if ( $hook['description'] ) : ?>
          <p class="desc"><?php echo $e( implode( ' ', $hook['description'] ) ); ?></p>
        <?php endif; ?>

        <?php if ( $hook['params'] ) : ?>
          <h4 class="sub">Parameters</h4>
          <table class="params">
            <tr><th>Type</th><th>Name</th><th>Description</th></tr>
            <?php foreach ( $hook['params'] as $i => $p ) : ?>
              <tr>
                <td class="ty"><code><?php echo $e( $p['type'] ); ?></code></td>
                <td class="nm"><code>$<?php echo $e( $p['name'] ); ?></code><?php if ( 'filter' === $hook['type'] && 0 === $i ) : ?> <em>(value)</em><?php endif; ?></td>
                <td><?php echo $e( $p['desc'] ); ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        <?php endif; ?>

        <?php if ( 'filter' === $hook['type'] && ( $hook['return_type'] || $hook['return_desc'] ) ) : ?>
          <p class="ret"><strong>Returns</strong> <code><?php echo $e( $hook['return_type'] ); ?></code><?php echo $hook['return_desc'] ? ' — ' . $e( $hook['return_desc'] ) : ''; ?></p>
        <?php endif; ?>

        <h4 class="sub">Usage</h4>
        <pre><code><?php echo $e( bc_hook_usage_snippet( $hook ) ); ?></code></pre>

        <?php if ( $hook['example'] ) : ?>
          <h4 class="sub">Example value</h4>
          <pre><code><?php echo $e( rtrim( implode( "\n", $hook['example'] ) ) ); ?></code></pre>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endforeach; ?>
<?php endforeach; ?>

</div>
</body>
</html>
<?php
$html = ob_get_clean();

if ( ! is_dir( dirname( $output ) ) ) {
	mkdir( dirname( $output ), 0755, true );
}
file_put_contents( $output, $html );

fwrite( STDOUT, "Hooks reference written to {$output} ({$total} hooks).\n" );
