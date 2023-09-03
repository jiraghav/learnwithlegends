<?php











namespace Composer;

use Composer\Autoload\ClassLoader;
use Composer\Semver\VersionParser;






class InstalledVersions
{
private static $installed = array (
  'root' => 
  array (
    'pretty_version' => 'dev-main',
    'version' => 'dev-main',
    'aliases' => 
    array (
    ),
    'reference' => '85634e4e6cd256061cc52595ecb0f3ff8683459f',
    'name' => 'taiwo/legends',
  ),
  'versions' => 
  array (
    'doctrine/inflector' => 
    array (
      'pretty_version' => '1.4.4',
      'version' => '1.4.4.0',
      'aliases' => 
      array (
      ),
      'reference' => '4bd5c1cdfcd00e9e2d8c484f79150f67e5d355d9',
    ),
    'graham-campbell/result-type' => 
    array (
      'pretty_version' => 'v1.0.4',
      'version' => '1.0.4.0',
      'aliases' => 
      array (
      ),
      'reference' => '0690bde05318336c7221785f2a932467f98b64ca',
    ),
    'illuminate/container' => 
    array (
      'pretty_version' => 'v5.8.36',
      'version' => '5.8.36.0',
      'aliases' => 
      array (
      ),
      'reference' => 'b42e5ef939144b77f78130918da0ce2d9ee16574',
    ),
    'illuminate/contracts' => 
    array (
      'pretty_version' => 'v5.8.36',
      'version' => '5.8.36.0',
      'aliases' => 
      array (
      ),
      'reference' => '00fc6afee788fa07c311b0650ad276585f8aef96',
    ),
    'illuminate/database' => 
    array (
      'pretty_version' => 'v5.8.36',
      'version' => '5.8.36.0',
      'aliases' => 
      array (
      ),
      'reference' => 'ac9ae2d82b8a6137400f17b3eea258be3518daa9',
    ),
    'illuminate/support' => 
    array (
      'pretty_version' => 'v5.8.36',
      'version' => '5.8.36.0',
      'aliases' => 
      array (
      ),
      'reference' => 'df4af6a32908f1d89d74348624b57e3233eea247',
    ),
    'league/iso3166' => 
    array (
      'pretty_version' => '4.0.0',
      'version' => '4.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '1a3ec7e6f1e4f16fce68dc239bafae217fbdcfef',
    ),
    'nesbot/carbon' => 
    array (
      'pretty_version' => '2.58.0',
      'version' => '2.58.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '97a34af22bde8d0ac20ab34b29d7bfe360902055',
    ),
    'nikic/fast-route' => 
    array (
      'pretty_version' => 'v1.3.0',
      'version' => '1.3.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '181d480e08d9476e61381e04a71b34dc0432e812',
    ),
    'paypal/rest-api-sdk-php' => 
    array (
      'pretty_version' => '1.14.0',
      'version' => '1.14.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '72e2f2466975bf128a31e02b15110180f059fc04',
    ),
    'phpmailer/phpmailer' => 
    array (
      'pretty_version' => 'v6.6.0',
      'version' => '6.6.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'e43bac82edc26ca04b36143a48bde1c051cfd5b1',
    ),
    'phpoption/phpoption' => 
    array (
      'pretty_version' => '1.8.1',
      'version' => '1.8.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'eab7a0df01fe2344d172bff4cd6dbd3f8b84ad15',
    ),
    'psr/container' => 
    array (
      'pretty_version' => '1.1.1',
      'version' => '1.1.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '8622567409010282b7aeebe4bb841fe98b58dcaf',
    ),
    'psr/log' => 
    array (
      'pretty_version' => '1.1.4',
      'version' => '1.1.4.0',
      'aliases' => 
      array (
      ),
      'reference' => 'd49695b909c3b7628b6289db5479a1c204601f11',
    ),
    'psr/simple-cache' => 
    array (
      'pretty_version' => '1.0.1',
      'version' => '1.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '408d5eafb83c57f6365a3ca330ff23aa4a5fa39b',
    ),
    'stripe/stripe-php' => 
    array (
      'pretty_version' => 'v9.5.0',
      'version' => '9.5.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'e3ea7f8ea7d6e96ef4253e3c5290d548fb89835c',
    ),
    'symfony/deprecation-contracts' => 
    array (
      'pretty_version' => 'v2.5.1',
      'version' => '2.5.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'e8b495ea28c1d97b5e0c121748d6f9b53d075c66',
    ),
    'symfony/polyfill-ctype' => 
    array (
      'pretty_version' => 'v1.24.0',
      'version' => '1.24.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '30885182c981ab175d4d034db0f6f469898070ab',
    ),
    'symfony/polyfill-mbstring' => 
    array (
      'pretty_version' => 'v1.24.0',
      'version' => '1.24.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '0abb51d2f102e00a4eefcf46ba7fec406d245825',
    ),
    'symfony/polyfill-php80' => 
    array (
      'pretty_version' => 'v1.24.0',
      'version' => '1.24.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '57b712b08eddb97c762a8caa32c84e037892d2e9',
    ),
    'symfony/translation' => 
    array (
      'pretty_version' => 'v5.4.9',
      'version' => '5.4.9.0',
      'aliases' => 
      array (
      ),
      'reference' => '1639abc1177d26bcd4320e535e664cef067ab0ca',
    ),
    'symfony/translation-contracts' => 
    array (
      'pretty_version' => 'v2.5.1',
      'version' => '2.5.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '1211df0afa701e45a04253110e959d4af4ef0f07',
    ),
    'symfony/translation-implementation' => 
    array (
      'provided' => 
      array (
        0 => '2.3',
      ),
    ),
    'taiwo/legends' => 
    array (
      'pretty_version' => 'dev-main',
      'version' => 'dev-main',
      'aliases' => 
      array (
      ),
      'reference' => '85634e4e6cd256061cc52595ecb0f3ff8683459f',
    ),
    'vlucas/phpdotenv' => 
    array (
      'pretty_version' => 'v5.4.1',
      'version' => '5.4.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '264dce589e7ce37a7ba99cb901eed8249fbec92f',
    ),
  ),
);
private static $canGetVendors;
private static $installedByVendor = array();







public static function getInstalledPackages()
{
$packages = array();
foreach (self::getInstalled() as $installed) {
$packages[] = array_keys($installed['versions']);
}


if (1 === \count($packages)) {
return $packages[0];
}

return array_keys(array_flip(\call_user_func_array('array_merge', $packages)));
}









public static function isInstalled($packageName)
{
foreach (self::getInstalled() as $installed) {
if (isset($installed['versions'][$packageName])) {
return true;
}
}

return false;
}














public static function satisfies(VersionParser $parser, $packageName, $constraint)
{
$constraint = $parser->parseConstraints($constraint);
$provided = $parser->parseConstraints(self::getVersionRanges($packageName));

return $provided->matches($constraint);
}










public static function getVersionRanges($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

$ranges = array();
if (isset($installed['versions'][$packageName]['pretty_version'])) {
$ranges[] = $installed['versions'][$packageName]['pretty_version'];
}
if (array_key_exists('aliases', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['aliases']);
}
if (array_key_exists('replaced', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['replaced']);
}
if (array_key_exists('provided', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['provided']);
}

return implode(' || ', $ranges);
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['version'])) {
return null;
}

return $installed['versions'][$packageName]['version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getPrettyVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['pretty_version'])) {
return null;
}

return $installed['versions'][$packageName]['pretty_version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getReference($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['reference'])) {
return null;
}

return $installed['versions'][$packageName]['reference'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getRootPackage()
{
$installed = self::getInstalled();

return $installed[0]['root'];
}







public static function getRawData()
{
return self::$installed;
}



















public static function reload($data)
{
self::$installed = $data;
self::$installedByVendor = array();
}




private static function getInstalled()
{
if (null === self::$canGetVendors) {
self::$canGetVendors = method_exists('Composer\Autoload\ClassLoader', 'getRegisteredLoaders');
}

$installed = array();

if (self::$canGetVendors) {

foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
if (isset(self::$installedByVendor[$vendorDir])) {
$installed[] = self::$installedByVendor[$vendorDir];
} elseif (is_file($vendorDir.'/composer/installed.php')) {
$installed[] = self::$installedByVendor[$vendorDir] = require $vendorDir.'/composer/installed.php';
}
}
}

$installed[] = self::$installed;

return $installed;
}
}
