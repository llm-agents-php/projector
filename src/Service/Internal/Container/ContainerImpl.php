<?php

declare(strict_types=1);

namespace LLM\Assistant\Service\Internal\Container;

use LLM\Assistant\Service\Container as AppContainerInterface;
use LLM\Assistant\Service\Destroyable;
use LLM\Assistant\Service\Factoriable;
use LLM\Assistant\Service\Internal\Container\Injection\ConfigLoader;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Injector\Injector;

/**
 * Simple container.
 *
 * @internal
 */
final class ContainerImpl implements AppContainerInterface, Destroyable
{
    /** @var array<class-string, object> */
    private array $cache = [];

    /** @var array<class-string, array|\Closure(ContainerImpl): object> */
    private array $factory = [];

    private readonly Injector $injector;

    /**
     * @psalm-suppress PropertyTypeCoercion
     */
    public function __construct()
    {
        $this->injector = (new Injector($this))->withCacheReflections(false);
        $this->cache[Injector::class] = $this->injector;
        $this->cache[self::class] = $this;
        $this->cache[ContainerInterface::class] = $this;
    }

    public function get(string $id, array $arguments = []): object
    {
        /** @psalm-suppress InvalidReturnStatement */
        return $this->cache[$id] ??= $this->make($id, $arguments);
    }

    public function has(string $id): bool
    {
        return \array_key_exists($id, $this->cache) || \array_key_exists($id, $this->factory);
    }

    public function set(object $service, ?string $id = null): void
    {
        \assert($id === null || $service instanceof $id, "Service must be instance of {$id}.");
        $this->cache[$id ?? \get_class($service)] = $service;
    }

    public function make(string $class, array $arguments = []): object
    {
        $binding = $this->factory[$class] ?? null;

        if ($binding instanceof \Closure) {
            $result = $this->injector->invoke($binding);
        } else {
            try {
                $result = $this->injector->make($class, \array_merge((array) $binding, $arguments));
            } catch (\Throwable $e) {
                throw new class("Unable to create object of class $class.", previous: $e) extends \RuntimeException implements NotFoundExceptionInterface {};
            }
        }

        \assert($result instanceof $class, "Created object must be instance of {$class}.");

        // Detect related types
        // Configs
        if (\str_starts_with($class, 'LLM\\Assistant\\Config\\')) {
            // Hydrate config
            /** @var ConfigLoader $configLoader */
            $configLoader = $this->get(ConfigLoader::class);
            $configLoader->hydrate($result);
        }

        return $result;
    }

    public function bind(string $id, \Closure|array|string|null $binding = null): void
    {
        if ($binding === null) {
            $this->factory[$id] = $this->getFactory($id) ?? throw new \InvalidArgumentException(
                "Class `$id` must have a factory or be a factory itself and implement `Factoriable`.",
            );
            return;
        }

        if (\is_string($binding)) {
            $this->factory[$id] = $this->getFactory($binding) ?? fn(): object => $this->make($binding);
            return;
        }

        $this->factory[$id] = $binding;
    }

    public function destroy(): void
    {
        unset($this->cache, $this->factory, $this->injector);
    }

    /**
     * @template T
     * @param class-string<T> $class
     * @return null|\Closure(): T
     * @psalm-suppress all
     */
    private function getFactory(string $class): ?\Closure
    {
        return \is_a($class, Factoriable::class, true) ? $class::create(...) : null;
    }
}
