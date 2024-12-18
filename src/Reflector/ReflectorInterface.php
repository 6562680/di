<?php
/**
 * @noinspection PhpUndefinedNamespaceInspection
 * @noinspection PhpUndefinedClassInspection
 */

namespace Gzhegow\Di\Reflector;


interface ReflectorInterface
{
    /**
     * @return static
     */
    public function resetCache();

    /**
     * @return static
     */
    public function saveCache();

    /**
     * @return static
     */
    public function clearCache();


    /**
     * @param callable|object|array|string $callable
     */
    public function reflectArgumentsCallable($callable) : array;

    /**
     * @param object|class-string $objectOrClass
     */
    public function reflectArgumentsConstructor($objectOrClass) : array;
}
