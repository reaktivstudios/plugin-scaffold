<?php
/**
 * Adds CLI package comand for custom plugin.
 *
 * @package WP_CLI
 */

namespace WP_CLI;

use WP_CLI;
use WP_CLI\Process;
use WP_CLI\Utils;

/**
 * Class ScaffoldCustomPluginCommand
 *
 * @package WP_CLI
 */
class ScaffoldCustomPluginCommand {

	/**
	 * Generates starter code for a plugin.
	 *
	 * The following files are always generated:
	 *
	 * * `plugin-slug.php` is the main PHP plugin file.
	 * * `readme.txt` is the readme file for the plugin.
	 * * `package.json` needed by NPM holds various metadata relevant to the project. Packages: `grunt`, `grunt-wp-i18n` and `grunt-wp-readme-to-markdown`.
	 * * `Gruntfile.js` is the JS file containing Grunt tasks. Tasks: `i18n` containing `addtextdomain` and `makepot`, `readme` containing `wp_readme_to_markdown`.
	 * * `.editorconfig` is the configuration file for Editor.
	 * * `.gitignore` tells which files (or patterns) git should ignore.
	 * * `.distignore` tells which files and folders should be ignored in distribution.
	 *
	 * The following folders are always created:
	 *
	 * * `assets/js`
	 * * `assets/css`
	 * * `assets/images`
	 * * `inc/classes`
	 * * `inc/functions`
	 *
	 * The following files are also included unless the `--skip-tests` is used:
	 *
	 * * `phpunit.xml.dist` is the configuration file for PHPUnit.
	 * * `.travis.yml` is the configuration file for Travis CI. Use `--ci=<provider>` to select a different service.
	 * * `bin/install-wp-tests.sh` configures the WordPress test suite and a test database.
	 * * `tests/bootstrap.php` is the file that makes the current plugin active when running the test suite.
	 * * `tests/test-sample.php` is a sample file containing test cases.
	 * * `phpcs.xml.dist` is a collection of PHP_CodeSniffer rules.
	 *
	 * ## OPTIONS
	 *
	 * <slug>
	 * : The internal name of the plugin.
	 *
	 * [--dir=<dirname>]
	 * : Put the new plugin in some arbitrary directory path. Plugin directory will be path plus supplied slug.
	 *
	 * [--plugin_name=<title>]
	 * : What to put in the 'Plugin Name:' header.
	 *
	 * [--plugin_description=<description>]
	 * : What to put in the 'Description:' header.
	 *
	 * [--plugin_author=<author>]
	 * : What to put in the 'Author:' header.
	 *
	 * [--plugin_author_uri=<url>]
	 * : What to put in the 'Author URI:' header.
	 *
	 * [--plugin_uri=<url>]
	 * : What to put in the 'Plugin URI:' header.
	 *
	 * [--skip-tests]
	 * : Don't generate files for unit testing.
	 *
	 * [--ci=<provider>]
	 * : Choose a configuration file for a continuous integration provider.
	 * ---
	 * default: travis
	 * options:
	 *   - travis
	 *   - circle
	 *   - gitlab
	 * ---
	 *
	 * [--activate]
	 * : Activate the newly generated plugin.
	 *
	 * [--activate-network]
	 * : Network activate the newly generated plugin.
	 *
	 * [--force]
	 * : Overwrite files that already exist.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp scaffold plugin sample-plugin
	 *     Success: Created plugin files.
	 *     Success: Created test files.
	 *
	 * @param array $args       The CLI arguments.
	 * @param array $assoc_args The CLI associative args array.
	 */
	public function custom_plugin( $args, $assoc_args ) {

		WP_CLI::run_command( array( 'scaffold', 'plugin', $args[0] ), $assoc_args );

		$plugin_slug = $args[0];

		if ( ! empty( $assoc_args['dir'] ) ) {
			if ( ! is_dir( $assoc_args['dir'] ) ) {
				WP_CLI::error( "Cannot create plugin in directory that doesn't exist." );
			}
			$plugin_dir = $assoc_args['dir'] . "/$plugin_slug";
		} else {
			$plugin_dir = WP_PLUGIN_DIR . "/$plugin_slug";
		}

		$this->create_directories( array(
			"{$plugin_dir}/assets",
			"{$plugin_dir}/assets/js",
			"{$plugin_dir}/assets/css",
			"{$plugin_dir}/assets/images",
			"{$plugin_dir}/inc/",
			"{$plugin_dir}/inc/classes",
			"{$plugin_dir}/inc/functions",
		) );

	}

	/**
	 * Creates directories.
	 *
	 * @param array $directories Array of directories to create.
	 */
	private function create_directories( $directories ) {
		foreach ( $directories as $directory ) {
			if ( ! is_dir( $directory ) ) {
				Process::create( Utils\esc_cmd( 'mkdir -p %s', $directory ) )->run();
			}
		}
	}

}

$custom_plugin_cli = new ScaffoldCustomPluginCommand();

WP_CLI::add_command( 'scaffold custom_plugin', array( $custom_plugin_cli, 'custom_plugin' ) );
