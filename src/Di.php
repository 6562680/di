<?php
/**
 * @noinspection PhpUndefinedNamespaceInspection
 * @noinspection PhpUndefinedClassInspection
 */

namespace Gzhegow\Di;

use Gzhegow\Di\Struct\Id;
use Gzhegow\Di\Lazy\LazyService;
use Gzhegow\Di\Exception\RuntimeException;
use Gzhegow\Di\Injector\InjectorInterface;
use Gzhegow\Di\Reflector\ReflectorInterface;
use Gzhegow\Di\Exception\Runtime\NotFoundException;
use Gzhegow\Di\Reflector\Struct\ReflectorCacheRuntime;


class Di implements DiInterface
{
    /**
     * @var DiFactoryInterface
     */
    protected $factory;
    /**
     * @var InjectorInterface
     */
    protected $injector;
    /**
     * @var ReflectorInterface
     */
    protected $reflector;


    public function __construct(
        DiFactoryInterface $factory,
        InjectorInterface $injector,
        ReflectorInterface $reflector
    )
    {
        $this->factory = $factory;
        $this->injector = $injector;
        $this->reflector = $reflector;
    }


    /**
     * @param array{
     *     injectorResolveUseTake: string|null,
     * }|null $settings
     */
    public function setSettings(array $settings = null) // : static
    {
        $resolveUseTake = $settings[ 'injectorResolveUseTake' ] ?? $settings[ 0 ] ?? null;

        $this->injector->setSettings(
            $resolveUseTake
        );

        return $this;
    }


    public function resetCache() // : static
    {
        $this->reflector->resetCache();

        return $this;
    }

    public function loadCache(bool $readData = null) // : static
    {
        $this->reflector->loadCache($readData);

        return $this;
    }

    public function clearCache() // : static
    {
        $this->reflector->clearCache();

        return $this;
    }

    public function flushCache() // : static
    {
        $this->reflector->flushCache();

        return $this;
    }


    /**
     * @param array{
     *     reflectorCacheMode: string|null,
     *     reflectorCacheAdapter: object|\Psr\Cache\CacheItemPoolInterface|null,
     *     reflectorCacheDirpath: string|null,
     *     reflectorCacheFilename: string|null,
     * }|null $settings
     */
    public function setCacheSettings(array $settings = null) // : static
    {
        $cacheMode = $settings[ 'reflectorCacheMode' ] ?? $settings[ 0 ] ?? null;
        $cacheAdapter = $settings[ 'reflectorCacheAdapter' ] ?? $settings[ 1 ] ?? null;
        $cacheDirpath = $settings[ 'reflectorCacheDirpath' ] ?? $settings[ 2 ] ?? null;
        $cacheFilename = $settings[ 'reflectorCacheFilename' ] ?? $settings[ 3 ] ?? null;

        $this->reflector->setCacheSettings(
            $cacheMode,
            $cacheAdapter,
            $cacheDirpath,
            $cacheFilename
        );

        return $this;
    }


    /**
     * @return static
     */
    public function merge(InjectorInterface $di) // : static
    {
        $this->injector->merge($di);

        return $this;
    }


    /**
     * @param string $id
     */
    public function has($id, Id &$result = null) : bool
    {
        $status = $this->injector->has($id, $result);

        return $status;
    }


    public function bind($id, $mixed = null, bool $isSingleton = null) // : static
    {
        $isSingleton = $isSingleton ?? false;

        $id = Id::from($id);

        $this->injector->bindItemAuto($id, $mixed, $isSingleton);

        return $this;
    }

    public function bindSingleton($id, $mixed = null) // : static
    {
        $this->bind($id, $mixed, true);

        return $this;
    }


    public function bindAlias($id, $aliasId, bool $isSingleton = null) // : static
    {
        $isSingleton = $isSingleton ?? false;

        $id = Id::from($id);
        $aliasId = Id::from($aliasId);

        $this->injector->bindItemAlias($id, $aliasId, $isSingleton);

        return $this;
    }

    /**
     * @param class-string $classId
     */
    public function bindClass($id, $classId, bool $isSingleton = null) // : static
    {
        $isSingleton = $isSingleton ?? false;

        $id = Id::from($id);
        $classId = Id::from($classId);

        $this->injector->bindItemClass($id, $classId, $isSingleton);

        return $this;
    }

    /**
     * @param callable $fnFactory
     */
    public function bindFactory($id, $fnFactory, bool $isSingleton = null) // : static
    {
        $isSingleton = $isSingleton ?? false;

        $id = Id::from($id);

        $this->injector->bindItemFactory($id, $fnFactory, $isSingleton);

        return $this;
    }

    public function bindInstance($id, object $instance, bool $isSingleton = null) // : static
    {
        $isSingleton = $isSingleton ?? false;

        $id = Id::from($id);

        $this->injector->bindItemInstance($id, $instance, $isSingleton);

        return $this;
    }


    /**
     * @param callable $fnExtend
     */
    public function extend($id, $fnExtend) // : static
    {
        $id = Id::from($id);

        $this->injector->extendItem($id, $fnExtend);

        return $this;
    }


    /**
     * @template-covariant T
     *
     * @param class-string<T>|null $contractT
     *
     * @return T|null
     */
    public function ask($id, string $contractT = null, bool $forceInstanceOf = null, array $parametersWhenNew = null) // : ?object
    {
        $parametersWhenNew = $parametersWhenNew ?? [];
        $contractT = $contractT ?? '';
        $forceInstanceOf = $forceInstanceOf ?? false;

        $id = Id::from($id);

        $instance = $this->injector->askItem($id, $contractT, $forceInstanceOf, $parametersWhenNew);

        return $instance;
    }

    /**
     * @template-covariant T
     *
     * @param class-string<T>|null $contractT
     *
     * @return T
     *
     * @throws NotFoundException
     */
    public function get($id, string $contractT = null, bool $forceInstanceOf = null, array $parametersWhenNew = null) // : object
    {
        $parametersWhenNew = $parametersWhenNew ?? [];
        $contractT = $contractT ?? '';
        $forceInstanceOf = $forceInstanceOf ?? false;

        $id = Id::from($id);

        $instance = $this->injector->getItem($id, $contractT, $forceInstanceOf, $parametersWhenNew);

        return $instance;
    }

    /**
     * @template-covariant T
     *
     * @param class-string<T>|null $contractT
     *
     * @return T
     */
    public function make($id, array $parameters = null, string $contractT = null, bool $forceInstanceOf = null) // : object
    {
        $parameters = $parameters ?? [];
        $contractT = $contractT ?? '';
        $forceInstanceOf = $forceInstanceOf ?? false;

        $id = Id::from($id);

        $instance = $this->injector->makeItem($id, $parameters, $contractT, $forceInstanceOf);

        return $instance;
    }

    /**
     * @template-covariant T
     *
     * @param class-string<T>|null $contractT
     *
     * @return T
     */
    public function take($id, array $parametersWhenNew = null, string $contractT = null, bool $forceInstanceOf = null) // : object
    {
        $parametersWhenNew = $parametersWhenNew ?? [];
        $contractT = $contractT ?? '';
        $forceInstanceOf = $forceInstanceOf ?? false;

        $id = Id::from($id);

        $instance = $this->injector->takeItem($id, $parametersWhenNew, $contractT, $forceInstanceOf);

        return $instance;
    }


    /**
     * @template-covariant T
     *
     * @param class-string<T>|T|null $contractT
     *
     * @return LazyService<T>|T
     *
     * @throws NotFoundException
     */
    public function getLazy($id, string $contractT = null, array $parametersWhenNew = null) // : LazyService
    {
        $parametersWhenNew = $parametersWhenNew ?? [];
        $contractT = $contractT ?? '';

        $id = Id::from($id);

        $lazyService = $this->getItemLazy($id, $contractT, $parametersWhenNew);

        return $lazyService;
    }

    /**
     * @template-covariant T
     *
     * @param class-string<T>|T|null $contractT
     *
     * @return LazyService<T>|T
     */
    public function makeLazy($id, array $parameters = null, string $contractT = null) // : LazyService
    {
        $parameters = $parameters ?? [];
        $contractT = $contractT ?? '';

        $id = Id::from($id);

        $lazyService = $this->makeItemLazy($id, $parameters, $contractT);

        return $lazyService;
    }


    /**
     * @template T
     *
     * @param T|object $instance
     *
     * @return T
     */
    public function autowire(object $instance, array $methodArgs = null, string $methodName = null) // : object
    {
        $methodArgs = $methodArgs ?? [];
        $methodName = $methodName ?? '';

        $this->injector->autowireItem($instance, $methodArgs, $methodName);

        return $instance;
    }


    /**
     * @param callable $fn
     *
     * @return mixed
     */
    public function callUserFunc($fn, ...$args) // : mixed
    {
        $result = $this->injector->autowireUserFunc($fn, ...$args);

        return $result;
    }

    /**
     * @param callable $fn
     *
     * @return mixed
     */
    public function callUserFuncArray($fn, array $args = null) // : mixed
    {
        $args = $args ?? [];

        $result = $this->injector->autowireUserFuncArray($fn, $args);

        return $result;
    }


    /**
     * @template-covariant T
     *
     * @param class-string<T>|T $contractT
     *
     * @return LazyService<T>|T
     *
     * @throws NotFoundException
     *
     * @noinspection PhpUnusedParameterInspection
     */
    protected function getItemLazy(Id $id, string $contractT = '', array $parametersWhenNew = []) : LazyService
    {
        $lazyService = $this->factory->newLazyServiceGet($id, $parametersWhenNew);

        return $lazyService;
    }

    /**
     * @template-covariant T
     *
     * @param class-string<T>|T $contractT
     *
     * @return LazyService<T>|T
     *
     * @noinspection PhpUnusedParameterInspection
     */
    protected function makeItemLazy(Id $id, array $parameters = [], string $contractT = '') : LazyService
    {
        $lazyService = $this->factory->newLazyServiceMake($id, $parameters);

        return $lazyService;
    }


    public static function getInstance() // : static
    {
        $instance = static::$instances[ static::class ];

        if (! is_a($instance, static::class)) {
            throw new RuntimeException(
                'No instance bound. Please, call Di::setInstance() first.'
            );
        }

        return $instance;
    }

    /**
     * @param static $di
     *
     * @return void
     */
    public static function setInstance($di) : void
    {
        if (! is_a($di, static::class)) {
            throw new RuntimeException(
                'The `di` should be instance of: ' . static::class
                . ' / ' . _php_dump($di)
            );
        }

        static::$instances[ get_class($di) ] = $di;
    }

    /**
     * @var array<class-string, static>
     */
    protected static $instances = [];
}
