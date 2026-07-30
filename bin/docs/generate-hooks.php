<?php
/**
 * Generate the Hooks reference page from the custom docblock tags in
 * BluecadetEvents\Plugin\Hooks.
 *
 * Dependency-free: reflects the class and parses the @hook / @hook_object_type
 * / @hook_category / @hook_type / @example tags, then renders a grouped,
 * self-contained HTML page.
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

	// Strip the comment framing to clean text lines.
	$lines = preg_split( '/\R/', $raw );
	$lines = array_map(
		static function ( $line ) {
			$line = preg_replace( '#^\s*/\*\*?#', '', $line ); // opening /**
			$line = preg_replace( '#\s*\*/\s*$#', '', $line );  // closing */
			$line = preg_replace( '#^\s*\*\s?#', '', $line );    // leading  *
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
		'return'      => null,
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
		} elseif ( str_starts_with( $trimmed, '@return' ) ) {
			$in_example = false;
			$hook['return'] = trim( substr( $trimmed, 7 ) );
		} elseif ( str_starts_with( $trimmed, '@example' ) ) {
			$in_example = true;
		} elseif ( str_starts_with( $trimmed, '@' ) ) {
			$in_example = false; // some other tag we don't render
		} elseif ( $in_example ) {
			$hook['example'][] = $line;
		} elseif ( '' !== $trimmed && $trimmed !== $method->getName() ) {
			// Description: skip the redundant method-name line and blanks.
			$hook['description'][] = $trimmed;
		}
	}

	return $hook['tag'] ? $hook : null;
}

// --- Collect + group -------------------------------------------------------

$class   = new ReflectionClass( Hooks::class );
$methods = $class->getMethods( ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC );

$grouped = []; // object_type => category => [ hooks ]
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

$e = static fn( $s ) => htmlspecialchars( (string) $s, ENT_QUOTES );

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
  .hook .meta { color:var(--muted); font-size:13px; margin:6px 0; }
  .hook p { margin:8px 0; }
  pre { background:var(--code); border:1px solid var(--border); border-radius:6px; padding:12px 14px; overflow-x:auto; font-size:13px; }
  code { font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace; }
  .toplink { font-size:12px; }
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
        <div class="meta">
          <?php echo $e( ucfirst( $hook['type'] ) ); ?>
          <?php if ( $hook['return'] ) : ?> · returns <code><?php echo $e( $hook['return'] ); ?></code><?php endif; ?>
          · <code>Hooks::<?php echo $e( $hook['method'] ); ?>()</code>
        </div>
        <?php if ( $hook['description'] ) : ?>
          <p><?php echo $e( implode( ' ', $hook['description'] ) ); ?></p>
        <?php endif; ?>
        <?php
        $fn   = 'filter' === $hook['type'] ? 'add_filter' : 'add_action';
        $snip = "{$fn}( '{$hook['tag']}', function ( \$value ) {\n    // ...\n    return \$value;\n} );";
        ?>
        <pre><code><?php echo $e( $snip ); ?></code></pre>
        <?php if ( $hook['example'] ) : ?>
          <div class="meta">Example value:</div>
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
