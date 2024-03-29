<?php

namespace Thor\Framework\Actions;

use PDOException;
use Thor\Globals;
use Thor\Web\WebController;
use Thor\Http\{Routing\Route, Response\Response, Request\HttpMethod};
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Framework\Configurations\{SecurityConfiguration, DatabasesConfiguration};
use Thor\Configuration\{ThorConfiguration, ConfigurationFromFile, Configuration as YmlConfiguration};
use Twig\Error\{LoaderError, RuntimeError, SyntaxError};

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
     * @see ThorConfiguration
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
     * @see ThorConfiguration
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
