<?php
namespace DS\ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Create a LESS service provider to generate CSS file from LESS files
 */
class LessServiceProvider implements ServiceProviderInterface
{
	/**
	 * Value for classic CSS generated from LESS source files.
	 *
	 * @var string
	 */
	const FORMATTER_CLASSIC = 'classic';

	/**
	 * Value for compressed CSS generated from LESS source files.
	 *
	 * @var string
	 */
	const FORMATTER_COMPRESSED = 'compressed';

	public function register(Application $app) {
	}

	public function boot(Application $app) {

		// Validate this params.
		$this->validate($app);

		// Define default formatter if not already set.
		$formatter = isset($app['less.formatter']) ? $app['less.formatter'] : self::FORMATTER_CLASSIC;
		$source = $app['less.source'];
		$target = $app['less.target'];
		$cache = $app['less.cache'];

		// Attempt to load the ".cache" file.
		if (file_exists($cache)) {

			// Store array contents for passing to cachedCompile()
			$cacheContents = unserialize(file_get_contents($cache));

		// If loading failed, we need to compile it
		} else {

			// Store source file for passing to cachedCompile()
			$cacheContents = $source;
		}

		$handle = new \lessc();
		$handle->setFormatter($formatter);

		// Use either array or ".less" file via cachedCompile
		$newCache = $handle->cachedCompile($cacheContents);

		if (!is_array($cacheContents) || $newCache["updated"] > $cacheContents["updated"]) {

			// Write cache file
			file_put_contents($cache, serialize($newCache));

			// Write CSS file
			file_put_contents($target, $newCache['compiled']);

			// Change CSS permisions
			if(isset($app['less.target_mode'])){
				chmod($target, $app['less.target_mode']);
			}
		}
	}

	/**
	 * Validate application settings.
	 *
	 * @param \Silex\Application $app
	 *   Application to validate
	 *
	 * @throws \Exception
	 *   If some params is not valid throw exception.
	 */
	private function validate(Application $app) {

		// Params must be defined.
		if (!isset($app['less.source'], $app['less.target'], $app['less.cache'])) {
			throw new \Exception("Application['less.source'] and ['less.target'] must be defined");
		}

		// Destination directory must be writable.
		$targetDir = dirname($app['less.target']);
		if (!is_writable($targetDir)) {
			throw new \Exception("Target file directory \"$targetDir\" is not writable");
		}

		// Cache directory must be writable.
		$cacheDir = dirname($app['less.cache']);
		if (!is_writable($cacheDir)) {
			throw new \Exception("Cache file directory \"$cacheDir\" is not writable");
		}

		// Validate formatter type.
		if (isset($app['less.formatter']) && !in_array($app['less.formatter'], array(self::FORMATTER_CLASSIC, self::FORMATTER_COMPRESSED))) {
			throw new \Exception("Application['less.formatter'] can be 'classic' or 'compressed'");
		}

		// Validate source file.
		$source = $app['less.source'];
		if (!file_exists($source)) {
			throw new \Exception('Could not find less source file "'.$source.'"');
		}
	}
}
