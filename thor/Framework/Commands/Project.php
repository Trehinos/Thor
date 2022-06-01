<?php

namespace Thor\Framework\Commands;

use Thor\Globals;
use Thor\Cli\Command;
use Thor\Tools\Strings;
use Thor\FileSystem\File;
use Thor\Cli\Console\Mode;
use Thor\Cli\Console\Color;
use Thor\FileSystem\Folder;
use Thor\FileSystem\FileSystem;

/**
 *
 */

/**
 *
 */
final class Project extends Command
{

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function document(): void
    {
        $folder = ($this->get('folder') ?? 'thor') . '/';
        $namespace = ($this->get('namespace') ?? 'Thor') . '\\';
        $verbose = $this->get('verbose') ?? false;

        $classes = $this->getClasses(Globals::CODE_DIR . $folder, $namespace);
        Folder::createIfNotExists(Globals::VAR_DIR . 'documentation');
        $output = '';
        $links = [];
        foreach ($classes as $className) {
            $this->console->echoes(
                'Génération de la documentation de ',
                Color::FG_CYAN,
                "$className\n",
            );
            $md = $this->generateMd($className);
            if ($verbose) {
                $this->console->echoes(Mode::DIM, $md, "\n");
            }
            $filename = str_replace('\\', '_', $className);
            FileSystem::write(Globals::VAR_DIR . ($link = "documentation/$filename") . '.md', $md);
            $links[$className] = $link;
        }
        $str = '';
        $module = '';
        $oldModule = null;
        $last = 0;
        $modules = [];
        foreach ($links as $label => $link) {
            $rc = new \ReflectionClass($label);
            $thor = '';
            $baseModule = '';
            $rest = Strings::split($label, '\\', $thor);
            $rest = Strings::split($rest, '\\', $baseModule);
            $module = "$thor\\$baseModule";
            if ($label === $module) {
                $module = "$thor";
            }
            if ($oldModule !== $module) {
                $modules[$module] = "{$module}-module";
                $str .= "### $module module\n";
            }
            $type = match (true) {
                $rc->isTrait()     => 'trait',
                $rc->isEnum()      => 'enum',
                $rc->isFinal()     => 'final class',
                $rc->isInterface() => 'interface',
                $rc->isAbstract()  => 'abstract class',
                default            => 'class'
            };
            $link = basename($link);
            $str .= "* [$label]($link) `$type`\n";
            $oldModule = $module;
        }
        $summary = "# Thor's classes documentation\n\n## Summary\n\n" . implode(
                "\n",
                array_map(
                    fn(string $hlbl, string $href) => "* [$hlbl](#" . str_replace('\\', '', $href) . ') module',
                    array_keys($modules),
                    array_values($modules)
                )
            ) . "\n\n## Modules\n\n";
        FileSystem::write(Globals::VAR_DIR . ($link = "documentation.md"), $summary . $str);
    }

    /**
     * @param class-string $className
     *
     * @return string
     *
     * @throws \ReflectionException
     */
    private function generateMd(string $className): string
    {
        $rc = new \ReflectionClass($className);
        $type = match (true) {
            $rc->isTrait()     => 'trait',
            $rc->isEnum()      => 'enum',
            $rc->isFinal()     => 'final class',
            $rc->isInterface() => 'interface',
            $rc->isAbstract()  => 'abstract class',
            default            => 'class'
        };
        $name = $rc->getShortName();
        $namespace = $rc->getNamespaceName();

        $interfaces = $rc->getInterfaces();
        $parent = $rc->getParentClass();

        $md = "# $name `$type`\n\n";
        if ($parent) {
            $md .= "> **Extends** : [{$parent->getShortName()}]({$parent->getName()})  \n";
        }
        if (!empty($interfaces)) {
            $md .= "> **Implements** : " . implode(
                    ', ',
                    array_map(
                        fn(\ReflectionClass $interface) => '[' . $interface->getShortName() . '](' . str_replace(
                                '\\',
                                '_',
                                $interface->getName()
                            ) . ')',
                        $interfaces
                    )
                ) . "  \n";
        }
        $md .= "> **Namespace** : `$namespace\\$name`\n\n";

        $md .= $this->parseComment($rc->getDocComment(), true);

        if ($type === 'enum') {
            $md .= "## Cases\n\n";
            $re = new \ReflectionEnum($className);
            foreach ($re->getCases() as $case) {
                $comment = $this->parseComment($case->getDocComment());
                $md .= "### {$case->getName()}\n$comment";
            }
        } else {
            // TODO : consts

            $staticMethods = [];
            $publicMethods = [];
            $protectedMethods = [];
            foreach ($rc->getMethods() as $method) {
                if ($method->getDeclaringClass()->getName() !== $className) {
                    continue;
                }
                if ($method->isStatic() && $method->isPublic()) {
                    $staticMethods[] = $method;
                    continue;
                }
                if ($method->isPublic()) {
                    $publicMethods[] = $method;
                    continue;
                }
                if ($method->isProtected()) {
                    $protectedMethods[] = $method;
                }
            }

            $md .= $this->mdBlockMethods($staticMethods, 'Static methods', true);
            $md .= $this->mdBlockMethods($publicMethods, 'Public methods', true);
            $md .= $this->mdBlockMethods($protectedMethods, 'Protected methods', true);
            $md .= $this->mdBlockMethods($staticMethods, 'Static methods');
            $md .= $this->mdBlockMethods($publicMethods, 'Public methods');
            $md .= $this->mdBlockMethods($protectedMethods, 'Protected methods');
            $md .= $this->mdBlockProperties($rc->getProperties(\ReflectionProperty::IS_PUBLIC), 'Public properties');
        }


        return $md;
    }

    /**
     * @param array  $properties
     * @param string $title
     *
     * @return string
     */
    private
    function mdBlockProperties(
        array $properties,
        string $title
    ): string {
        if (empty($properties)) {
            return '';
        }
        $md = "## $title\n\n";
        /** @var \ReflectionProperty $property */
        foreach ($properties as $property) {
            $md .= "### `{$property->getName()}:{$property->getType()}`\n";
        }
        return "$md\n";
    }

    /**
     * @param array  $methods
     * @param string $title
     * @param bool   $short
     *
     * @return string
     * @throws \ReflectionException
     */
    private
    function mdBlockMethods(
        array $methods,
        string $title,
        bool $short = false
    ): string {
        if (empty($methods)) {
            return '';
        }
        $md = "## $title\n\n";
        /** @var \ReflectionMethod $method */
        foreach ($methods as $method) {
            $returnType = "{$method->getReturnType()}";
            $comment = $this->parseComment($method->getDocComment(), !$short);
            $parameters = implode(
                "\n",
                array_map(
                    function (\ReflectionParameter $parameter) {
                        $defaultValue = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : '';
                        if (is_string($defaultValue) || $defaultValue instanceof \Stringable) {
                            $defaultValue = "\"$defaultValue\"";
                        } elseif (is_bool($defaultValue)) {
                            $defaultValue = $defaultValue ? 'true' : 'false';
                        } elseif (is_scalar($defaultValue)) {
                            $defaultValue = "$defaultValue";
                        } elseif (is_null($defaultValue)) {
                            $defaultValue = "null";
                        } elseif (is_array($defaultValue)) {
                            $defaultValue = '[' . (empty($defaultValue) ? '' : '...') . ']';
                        } else {
                            $defaultValue = '(...)';
                        }

                        return '* `' . $parameter->getType() . ' $' . $parameter->getName() .
                            ($parameter->isOptional() ? ' = ' . $defaultValue : '') . '`';
                    },
                    $method->getParameters()
                )
            );
            if ($short) {
                $comment = trim($comment, "\n");
                $comment = $comment === '' ? $comment : " : $comment";
                $md .= "* [{$method->getName()}](#{$method->getName()})()$comment\n";
                continue;
            }
            $md .= "### `{$method->getName()}()`\n$comment";
            if ($parameters !== '') {
                $md .= "#### Parameters\n\n$parameters\n\n";
            }
            if ($returnType !== 'void' && $returnType !== '') {
                $md .= "#### Return type : `$returnType`\n\n";
            }
            $md .= "<hr>\n\n";
        }
        return "$md\n";
    }

    private function parseComment(string $comment, bool $long = false): string
    {
        $str = '';
        $separator = '';
        foreach (explode("\n", $comment) as $line) {
            $line = trim($line);
            if (str_starts_with($line, '/**')) {
                continue;
            }
            if (str_starts_with($line, '*/')) {
                continue;
            }
            $line = trim(trim($line, '*'));
            if (str_starts_with($line, "@")) {
                break;
            }
            if ($line === '') {
                $separator = $long ? "\n" : "  ";
                continue;
            }
            $str .= "$separator$line\n";
            $separator = '';
        }

        if (trim($str) === '') {
            return '';
        }

        if ($long) {
            return trim($str, "\n") . "\n\n";
        }
        return substr(trim($str, "\n"), 0, min(strpos($str, '.'), 75)) . "...\n";
    }

    /**
     * @param string $path
     * @param string $namespace
     *
     * @return array
     */
    private function getClasses(string $path, string $namespace): array
    {
        static $base = null;
        if ($base === null) {
            $base = $path;
        }
        $classes = [];
        $files = glob("$path/*");
        $dirs = [];
        foreach ($files as $file) {
            if (in_array(basename($file), ['.', '..'])) {
                continue;
            }
            $file = realpath($file);
            if (!FileSystem::isDir($file)) {
                if (!str_ends_with($file, '.php')) {
                    continue;
                }
                $ext = FileSystem::getExtension($file);
                $file = mb_substr($file, 0, -mb_strlen($ext) - 1);
                $localPath = mb_substr($file, mb_strlen(realpath($base)));
                $classes[] = $namespace . str_replace(
                        '/',
                        '\\',
                        trim($localPath, '/')
                    );
                continue;
            }
            $dirs[] = $file;
        }
        foreach ($dirs as $dir) {
            $classes = [...$classes, ...$this->getClasses($dir, $namespace)];
        }

        return $classes;
    }
}
