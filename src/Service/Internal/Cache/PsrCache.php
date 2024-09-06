<?php

declare(strict_types=1);

namespace LLM\Assistant\Service\Internal\Cache;

use LLM\Assistant\Config\Source;
use LLM\Assistant\Service\Cache;
use Psr\SimpleCache\CacheInterface;
use Yiisoft\Cache\File\FileCache;

final class PsrCache implements Cache
{
    private readonly CacheInterface $cache;

    public function __construct(
        Source $config,
    ) {
        // Hardcoded FileCache
        $this->cache = new FileCache(
            $config->cacheDir,
        );
    }

    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cache->get($key, $default);
    }

    public function getOrSet(
        string $key,
        \Closure $callable,
        \DateInterval|int|null $ttl = null,
    ): mixed {
        $value = $this->cache->get($key, null);

        // Simple implementation
        if ($value === null) {
            $value = $callable();
            $this->cache->set($key, $value, $ttl);
        }

        return $value;
    }

    public function set(string $key, mixed $value, \DateInterval|int|null $ttl = null): bool
    {
        return $this->cache->set($key, $value, $ttl);
    }

    public function delete(string $key): bool
    {
        return $this->cache->delete($key);
    }

    public function clear(): bool
    {
        return $this->cache->clear();
    }
}
