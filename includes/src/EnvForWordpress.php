<?php 

namespace EnvForWordpress;

class EnvForWordpress {
    private static bool $initialized = false;
    private static array $providers = [];

    public static function init(): void 
    {
        if (static::$initialized) return;

        static::$initialized = true;

        static::$providers = apply_filters('env_for_wordpress_providers', static::$providers);

        static::validateProviders();
        static::initProviders();
    }

    public static function get(string $key, $default = null)
    {
        foreach (static::$providers as $provider) {
            $value = $provider->get($key);

            if (!is_null($value)) return $value;
        }

        return $default;
    }

    private static function validateProviders(): void
    {
        foreach (static::$providers as $provider) {
            if (!$provider instanceof EnvProvider) {
                throw new \InvalidArgumentException(
                    'Provider must be of type ' . EnvProvider::class . ', ' . get_class($provider) . ' given'
                );
            }
        }
    }

    private static function initProviders(): void
    {
        foreach (static::$providers as $provider) {
            $provider->init();
        }
    }
}
