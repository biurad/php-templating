<?php

declare(strict_types=1);

/*
 * This file is part of Biurad opensource projects.
 *
 * PHP version 7.2 and above required
 *
 * @author    Divine Niiquaye Ibok <divineibok@gmail.com>
 * @copyright 2019 Biurad Group (https://biurad.com/)
 * @license   https://opensource.org/licenses/BSD-3-Clause License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Biurad\UI\Renders;

use Twig;
use Twig\Cache\CacheInterface;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\NodeVisitor\NodeVisitorInterface;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

final class TwigRender extends AbstractRender
{
    protected const EXTENSIONS = ['twig'];

    /** @var Twig\Environment */
    protected $environment;

    /**
     * TwigEngine constructor.
     *
     * @param Twig\Environment $engine
     * @param string[]         $extensions
     */
    public function __construct(Twig\Environment $environment, array $extensions = self::EXTENSIONS)
    {
        $this->environment = $environment;
        $this->extensions  = $extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function render(string $template, array $parameters): string
    {
        $this->addFunction(
            new Twig\TwigFunction(
                'view',
                fn (string $template, array $parameters = []) => $this->render($template, $parameters),
                ['is_safe' => ['all']]
            )
        );

        if (isset($parameters['template'])) {
            /** @var \Biurad\UI\Template $global */
            $global = $parameters['template'];

            $this->addFunction(
                new Twig\TwigFunction(
                    'template',
                    fn (string $template, array $parameters = []) => $global->render($template, $parameters),
                    ['is_safe' => ['all']]
                )
            );
        }
        $source = $this->getLoader()->find($template);

        if ($source->isFile()) {
            $source   = \file_get_contents((string) $source);
        }
        $this->environment->setLoader(new ChainLoader(
            [new ArrayLoader([$template => (string) $source]), $this->environment->getLoader()]
        ));

        return $this->environment->render($template, $parameters);
    }

    /**
     * @param string $charset
     */
    public function setCharset(string $charset): void
    {
        $this->environment->setCharset($charset);
    }

    /**
     * Sets the current cache implementation.
     *
     * @param CacheInterface|false|string $cache A Twig\Cache\CacheInterface implementation,
     *                                           an absolute path to the compiled templates,
     *                                           or false to disable cache
     */
    public function setCache($cache): void
    {
        $this->environment->setCache($cache);
    }

    /**
     * @param RuntimeLoaderInterface $loader
     */
    public function addRuntimeLoader(RuntimeLoaderInterface $loader): void
    {
        $this->environment->addRuntimeLoader($loader);
    }

    /**
     * @param ExtensionInterface $extension
     */
    public function addExtension(ExtensionInterface $extension): void
    {
        $this->environment->addExtension($extension);
    }

    /**
     * @param ExtensionInterface[] $extensions An array of extensions
     */
    public function setExtensions(array $extensions): void
    {
        $this->environment->setExtensions($extensions);
    }

    /**
     * @param NodeVisitorInterface $visitor
     */
    public function addNodeVisitor(NodeVisitorInterface $visitor): void
    {
        $this->environment->addNodeVisitor($visitor);
    }

    /**
     * @param Twig\TwigFilter $filter
     */
    public function addFilter(Twig\TwigFilter $filter): void
    {
        $this->environment->addFilter($filter);
    }

    /**
     * @param callable $callable
     */
    public function registerUndefinedFilterCallback(callable $callable): void
    {
        $this->environment->registerUndefinedFilterCallback($callable);
    }

    /**
     * @param Twig\TwigFunction $function
     */
    public function addFunction(Twig\TwigFunction $function): void
    {
        $this->environment->addFunction($function);
    }
}
