<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function baseUrl()
    {
        return 'http://app:80';
    }


    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        // if (! static::runningInSail()) {
        //     static::startChromeDriver();
        // }
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions())->addArguments([
            '--disable-gpu',
            '--headless',
        ]);

        // return RemoteWebDriver::create(
        //     $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:4444/wd/hub',
        //     DesiredCapabilities::chrome()->setCapability(
        //         ChromeOptions::CAPABILITY, $options
        //     )
        // );
        return RemoteWebDriver::create(
            'http://selenium-hub:4444/wd/hub',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     *
     * @return bool
     */
    protected function hasHeadlessDisabled()
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
               isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }
}
