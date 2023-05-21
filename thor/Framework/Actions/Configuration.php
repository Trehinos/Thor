<?php

namespace Thor\Framework\Actions;

use Thor\Framework\Globals;
use Thor\Common\Configuration\{Configuration as YmlConfiguration};
use Thor\Http\Web\WebController;
use Thor\Framework\Configurations\ThorConfiguration;
use Thor\Common\Configuration\ConfigurationFromFile;
use Twig\Error\{LoaderError, SyntaxError, RuntimeError};
use Thor\Http\{Routing\Route, Response\Response, Request\HttpMethod};

/**
 * Configuration edition controller.
 */
final class Configuration extends WebController
{

    private const CONFIG_YML = Globals::CONFIG_DIR . 'config.yml';
    private const DATABASE_YML = Globals::CONFIG_DIR . 'database.yml';
    private const SECURITY_YML = Globals::CONFIG_DIR . 'security.yml';

    /**
     * GET /config/general
     * Displays the general configuration view.
     *
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @see \Thor\Framework\ThorConfiguration
     *
     */
    #[Route('config-config', '/config/general')]
    public function configView(): Response
    {
        $config = ThorConfiguration::get();

        return $this->twigResponse('thor/configuration/config.html.twig', [
            'configuration' => $config,
            'writable'      => is_writeable(self::CONFIG_YML),
            'filename'      => realpath(self::CONFIG_YML),
            'info'          => [
                'app_vendor'       => 'Marque, société, auteur',
                'app_name'         => 'Nom du projet',
                'app_version'      => '',
                'app_version_name' => '',
                'env'              => 'DEV / DEBUG / PROD : affichage des erreurs et logs',
                'lang'             => 'fr / en : langue du système',
                'timezone'         => 'Fuseau horaire par défaut pour les opérations de date',
                'log_path'         => 'Relatif à <code>' . realpath(Globals::VAR_DIR) . '/</code>',
                'thor_kernel'      => 'Kernel actuel <small><i class="fas fa-info-circle text-info"></i> informatif, ne peut pas être changé</small>',
            ],
        ]);
    }

    /**
     * POST /config-save/general
     * Save the general configuration.
     *
     * @return Response
     *
     * @see \Thor\Framework\Configurations\ThorConfiguration
     *
     */
    #[Route('config-save', '/config-save/general', HttpMethod::POST)]
    public function saveConfig(): Response
    {
        $config = $this->post('config');
        ConfigurationFromFile::writeTo(new YmlConfiguration($config), 'config');

        return $this->redirect('index', query: ['menuItem' => 'config-config']);
    }

}
