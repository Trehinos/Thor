<?php

namespace Thor\Security;

use Exception;
use Thor\Http\Session;
use JetBrains\PhpStorm\Pure;
use Thor\Http\Routing\Router;
use Thor\Http\Response\Response;
use JetBrains\PhpStorm\Immutable;
use Thor\Http\Response\ResponseInterface;
use Thor\Http\Request\ServerRequestInterface;
use Thor\Http\Server\RequestHandlerInterface;

class Security
{

    public const SOURCE_DB = 'database';
    public const SOURCE_LDAP = 'ldap';
    public const SOURCE_FILE = 'file';
    public const SOURCE_INTERNAL = 'internal';

    public const TYPE_USERPWD = 'username-password';
    public const TYPE_TOKEN = 'token';
    public const TYPE_BOTH = 'both';

    public const AUTH_SESSION = 'session';
    public const AUTH_HEADER = 'header_token';

    /**
     * Security constructor.
     *
     * @param bool        $isEnabled
     * @param string      $identitySource
     * @param string      $identityType
     * @param string|null $pdoRowClass
     * @param string|null $fileName
     * @param array|null  $fileFields
     * @param string|null $fileSeparator
     * @param string|null $filePhpStructure
     * @param string|null $ldapHost
     * @param string|null $ldapUser
     * @param string|null $ldapPassword
     * @param string      $authentication
     * @param string      $tokenKey
     * @param int|null    $tokenExpire
     * @param Firewall[]  $firewalls
     */
    public function __construct(
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public bool $isEnabled = false,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public string $identitySource = self::SOURCE_INTERNAL,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public string $identityType = self::TYPE_USERPWD,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $pdoRowClass = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $fileName = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?array $fileFields = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $fileSeparator = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $filePhpStructure = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $ldapHost = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $ldapUser = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $ldapPassword = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public string $authentication = self::AUTH_SESSION,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public string $tokenKey = 'user.token',
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?int $tokenExpire = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public array $firewalls = []
    ) {
    }

    public static function createFromConfiguration(array $config): self
    {
        $firewalls = [];
        foreach ($config['firewall'] ?? [] as $firewallConfig) {
            $firewalls[] = new Firewall(
                pattern:     $firewallConfig['pattern'] ?? '/',
                redirect:    $firewallConfig['redirect'] ?? 'login',
                loginRoute:  $firewallConfig['login-route'] ?? 'login',
                logoutRoute: $firewallConfig['logout-route'] ?? 'logout',
                checkRoute:  $firewallConfig['check-route'] ?? 'check',
                exclude:     $firewallConfig['exclude'] ?? [],
            );
        }

        return new self(
            isEnabled: match ($config['security'] ?? 'disable') {
                'enable' => true,
                default  => false
            },
            identitySource:   $config['identity-source'] ?? self::SOURCE_INTERNAL,
            identityType:     $config['identity-type'] ?? self::TYPE_USERPWD,
            pdoRowClass:      $config['database-pdo-row'] ?? null,
            fileName:         $config['file-name'] ?? null,
            fileFields:       $config['file-fields'] ?? null,
            fileSeparator:    $config['file-separator'] ?? null,
            filePhpStructure: $config['file-php-structure'] ?? null,
            ldapHost:         $config['ldap-host'] ?? null,
            ldapUser:         $config['ldap-user'] ?? null,
            ldapPassword:     $config['ldap-password'] ?? null,
            authentication:   $config['authentication'] ?? self::AUTH_SESSION,
            tokenKey:         $config['authentication-key'] ?? 'token',
            tokenExpire:      $config['authentication-expire'] ?? 60,
            firewalls:        $firewalls
        );
    }

    /**
     * Returns null if no redirect or redirect Response.
     */
    public function protect(
        ServerRequestInterface $request,
        Router $router,
        RequestHandlerInterface $handler
    ): ?ResponseInterface {
        if (!$this->isActive()) {
            return null;
        }
        foreach ($this->firewalls as $firewall) {
            $firewall->router = $router;
            $firewall->isAuthenticated = $this->isAuthenticated($request->getHeaderLine($this->tokenKey));
            if ($firewall->redirect($request)) {
                $request->getAttribute("firewall{$firewall->pattern}", 'FIREWALLED');
                return $firewall->process($request, $handler);
            }
        }

        return null;
    }

    public function isActive(): bool
    {
        return $this->isEnabled;
    }

    public function isAuthenticated(?string $requestToken = null): bool
    {
        if (!$this->isActive()) {
            return true;
        }

        $token = $this->getCurrentToken() ?? $requestToken;
        $tokenExpire = $this->getCurrentTokenExpire();
        $timeNow = self::now();
        if (null === $token || $tokenExpire <= $timeNow) {
            return false;
        }

        return true;
    }

    #[Pure]
    public function getCurrentToken(): ?string
    {
        return Session::read($this->tokenKey);
    }

    #[Pure]
    public function getCurrentTokenExpire(): ?string
    {
        return Session::read($this->tokenKey . '.expire');
    }

    private static function now(int $addMinutes = 0): int
    {
        return time() + 60 * $addMinutes;
    }

    /**
     * @param UserInterface $user
     * @param string|null   $clearPassword
     *
     * @return string|false
     */
    public function authenticate(UserInterface $user, ?string $clearPassword = null): string|false
    {
        if (!$this->isActive()) {
            return '';
        }
        if ($clearPassword !== null && !$user->hasPwdHashFor($clearPassword)) {
            return false;
        }

        $this->setUser($user);
        return $this->writeToken($user->getUsername());
    }

    public function setUser(UserInterface $user): void
    {
        Session::write("{$this->tokenKey}.user", $user);
    }

    public function writeToken(?string $username = null): string
    {
        Session::write("{$this->tokenKey}.token", $token = self::generateToken());
        Session::write("{$this->tokenKey}.username", $username ?? $token);
        Session::write("{$this->tokenKey}.expire", self::now(intval($this->tokenExpire)));
        return $token;
    }

    /**
     * @return string 64 random hexadecimal characters
     *
     * @throws Exception
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    #[Pure]
    public function getUser(): ?UserInterface
    {
        return Session::read("{$this->tokenKey}.user");
    }

    public function deleteToken(): void
    {
        Session::write("{$this->tokenKey}.token", null);
        Session::write("{$this->tokenKey}.username", null);
        Session::write("{$this->tokenKey}.expire", null);
    }

    #[Pure]
    public function getCurrentUsername(): ?string
    {
        return Session::read("{$this->tokenKey}.username");
    }

}
