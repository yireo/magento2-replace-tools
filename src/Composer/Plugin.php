<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\IO\IOInterface;
use Composer\Package\BasePackage;
use Composer\Package\Package;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PrePoolCreateEvent;

class Plugin implements PluginInterface, Capable, EventSubscriberInterface
{
    /**
     * @param Composer $composer
     * @param IOInterface $io
     * @return void
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $io->notice('Yireo Composer Replace Tools have been activated');
    }

    /**
     * @param Composer $composer
     * @param IOInterface $io
     * @return void
     */
    public function deactivate(Composer $composer, IOInterface $io)
    {
        $io->notice('Yireo Composer Replace Tools have been deactivated');
    }

    /**
     * @param Composer $composer
     * @param IOInterface $io
     * @return void
     */
    public function uninstall(Composer $composer, IOInterface $io)
    {
        $io->notice('Yireo Composer Replace Tools have been uninstalled');
    }

    /**
     * @return string[]
     */
    public function getCapabilities()
    {
        return [
            CommandProviderCapability::class => CommandProvider::class,
        ];
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            'pre-pool-create' => 'prePoolCreate',
        ];
    }

    public function prePoolCreate(PrePoolCreateEvent $event)
    {
        $newPackages = [];
        foreach ($event->getPackages() as $package) {
            /*if (false === $this->hasReplaceSource($package, '')) {
                $newPackages[] = $package;
                continue;
            }

            $package->setReplaces([]);*/
            $newPackages[] = $package;
        }

        $event->setPackages($newPackages);
    }

    private function hasReplaceSource(BasePackage $package, string $replaceSource):bool
    {
        foreach ($package->getReplaces() as $replace) {
            if ($replace->getSource() === $replaceSource) {
                return true;
            }
        }

        return false;
    }
}
