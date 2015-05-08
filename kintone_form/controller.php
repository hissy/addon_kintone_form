<?php defined('C5_EXECUTE') or die('Access Denied');

class KintoneFormPackage extends Package
{
    protected $pkgHandle = 'kintone_form';
    protected $appversionRequired = '5.6';
    protected $pkgVersion = '0.1';

    public function getPackageDescription()
    {
        return t('Kintone Form Package');
    }

    public function getPackageName()
    {
        return t('Kintone Form Package');
    }

    public function install()
    {
        $pkg = parent::install();
        $ci = new ContentImporter();
        $ci->importContentFile($pkg->getPackagePath() . '/install/blocktypes.xml');
    }

    public function on_start()
    {
        // register autoloading
        $this->registerAutoload();
    }

    protected function registerAutoload()
    {
        require_once(__DIR__ . '/vendor/autoload.php');
    }
}