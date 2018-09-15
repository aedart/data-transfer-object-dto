<?php namespace Aedart\DTO\Providers;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @deprecated Use \Aedart\Container\IoC, in aedart/athenaeum package
 *
 * Class Bootstrap
 *
 * <br />
 *
 * Boots a Inversion of Control (IoC) Container, that is responsible
 * for dealing with dependency injection.
 *
 * <br />
 *
 * <b>Warning</b> This class is <b>ONLY</b> needed if you are using
 * this package <b>outside a Laravel Framework</b>.
 *
 * @see https://en.wikipedia.org/wiki/Inversion_of_control
 * @see http://laravel.com/docs/5.1/container#introduction
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\DTO\Providers
 */
class Bootstrap
{

    /**
     * The IoC Service
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected static $container = null;

    /**
     * Boots the Inversion of Control (IoC) Container
     */
    public static function boot() : void
    {
        $container = self::getContainer();
        $container->singleton('app', $container);

        Facade::setFacadeApplication($container);
    }

    /**
     * Destroy the Inversion of Control (IoC) Container
     */
    public static function destroy() : void
    {
        Facade::clearResolvedInstances();

        Facade::setFacadeApplication(null);

        self::setContainer(null);
    }

    /**
     * Get the IoC service container
     *
     * If no IoC was set, this method will set and
     * return a default container
     *
     * @see getDefaultContainer
     *
     * @return \Illuminate\Contracts\Container\Container|null
     */
    public static function getContainer() : ?ContainerInterface
    {
        if (is_null(self::$container)) {
            self::setContainer(self::getDefaultContainer());
        }

        return self::$container;
    }

    /**
     * Set the IoC service container
     *
     * <b>Info</b>: You should invoke `boot()` after setting a
     * new container
     *
     * @param \Illuminate\Contracts\Container\Container|null $container [optional]
     */
    public static function setContainer(?ContainerInterface $container = null) : void
    {
        self::$container = $container;
    }

    /**
     * Returns a default IoC service container
     *
     * @return \Illuminate\Contracts\Container\Container|null
     */
    public static function getDefaultContainer() : ?ContainerInterface
    {
        return new Container();
    }
}
