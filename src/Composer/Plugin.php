<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface, EventSubscriberInterface, Capable
{
    public function activate(Composer $composer, IOInterface $io)
    {
        $io->notice('Yireo Composer Replace Tools have been activated');
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
        $io->notice('Yireo Composer Replace Tools have been deactivated');
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        $io->notice('Yireo Composer Replace Tools have been uninstalled');
    }

    public static function getSubscribedEvents()
    {
        return [];
        /*return [
            PluginEvents::POST_FILE_DOWNLOAD => [

            ]
        ];*/
    }

    public function getCapabilities()
    {
        return array(
            CommandProviderCapability::class => CommandProvider::class,
        );
    }
}
