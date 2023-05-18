<?php

namespace Thor\Configuration;

use Thor\Env;

final class ThorConfiguration extends ConfigurationFromFile
{

    /**
     * @param string|null $thorKernel
     */
    public function __construct(?string $thorKernel = null)
    {
        parent::__construct('config');
        if ($thorKernel === null) {
            global $thor_kernel;
            $thorKernel = $thor_kernel;
        }
        $this['thor_kernel'] = $thorKernel;
    }

    /**
     * @return string|null
     */
    public function thorKernel(): ?string
    {
        return $this['thor_kernel'];
    }

    /**
     * @return string
     */
    public function appVendor(): string
    {
        return $this['app_vendor'] ?? '';
    }

    /**
     * @return string
     */
    public function appName(): string
    {
        return $this['app_name'] ?? '';
    }

    /**
     * @return string
     */
    public function appVersion(): string
    {
        return $this['app_version'] ?? '';
    }

    /**
     * @return string
     */
    public function appVersionName(): string
    {
        return $this['app_version_name'] ?? '';
    }

    /**
     * @return Env|null
     */
    public function env(): ?Env
    {
        return Env::tryFrom($this['env'] ?? null) ?? Env::DEV;
    }

    /**
     * @return string
     */
    public function lang(): string
    {
        return $this['lang'] ?? 'fr';
    }

    /**
     * @return string
     */
    public function timezone(): string
    {
        return $this['timezone'] ?? '';
    }

    /**
     * @return string
     */
    public function logPath(): string
    {
        return $this['log_path'] ?? '';
    }

}
