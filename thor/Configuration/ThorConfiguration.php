<?php

namespace Thor\Configuration;

use Thor\Env;

final class ThorConfiguration extends ConfigurationFromFile
{

    public function __construct(?string $thorKernel = null)
    {
        parent::__construct('config');
        if ($thorKernel === null) {
            global $thor_kernel;
            $thorKernel = $thor_kernel;
        }
        $this['thor_kernel'] = $thorKernel;
    }

    public function thorKernel(): ?string
    {
        return $this['thor_kernel'];
    }

    public function appVendor(): string
    {
        return $this['app_vendor'] ?? '';
    }

    public function appName(): string
    {
        return $this['app_name'] ?? '';
    }

    public function appVersion(): string
    {
        return $this['app_version'] ?? '';
    }

    public function appVersionName(): string
    {
        return $this['app_version_name'] ?? '';
    }

    public function env(): ?Env
    {
        return Env::tryFrom($this['env'] ?? null) ?? Env::DEV;
    }

    public function lang(): string
    {
        return $this['lang'] ?? 'fr';
    }

    public function timezone(): string
    {
        return $this['timezone'] ?? '';
    }

    public function logPath(): string
    {
        return $this['log_path'] ?? '';
    }

}
