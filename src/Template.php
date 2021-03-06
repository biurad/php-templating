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

namespace Biurad\UI;

use Biurad\UI\Exceptions\RenderException;
use Biurad\UI\Interfaces\LoaderInterface;
use Biurad\UI\Interfaces\RenderInterface;
use Biurad\UI\Interfaces\StorageInterface;
use Biurad\UI\Interfaces\TemplateInterface;

final class Template implements TemplateInterface
{
    /** @var LoaderInterface */
    private $loader;

    /** @var RenderInterface[] */
    private $renders;

    /** @var array<string,mixed> */
    private $globals = [];

    /**
     * @param StorageInterface  $storage
     * @param bool              $profile
     */
    public function __construct(StorageInterface $storage, bool $profile = false)
    {
        $this->loader = new Loader($storage, $profile ? new Profile() : null);
    }

    /**
     * {@inheritdoc}
     */
    public function addGlobal(string $name, $value): void
    {
        $this->globals[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function addNamespace(string $namespace, $hints): void
    {
        $this->loader->addNamespace($namespace, $hints);
    }

    /**
     * {@inheritdoc}
     */
    public function addRender(RenderInterface ...$renders): void
    {
        foreach ($renders as $render) {
            $this->renders[] = $render->withLoader($this->loader);
        }
    }

    /**
     * Get all associated view engines.
     *
     * @return RenderInterface[]
     */
    public function getRenders(): array
    {
        return $this->renders;
    }

    /**
     * {@inheritdoc}
     */
    public function render(string $template, array $parameters = []): string
    {
        $this->addGlobal('template', $this);

        foreach ($this->renders as $engine) {
            if ($engine->getLoader()->exists($template)) {
                return $engine->render($template, \array_replace($parameters, $this->globals));
            }
        }

        throw new RenderException(
            \sprintf('No render engine is able to work with the template "%s".', $template)
        );
    }

    /**
     * Find the template file that exist, then render it contents.
     *
     * @param string              $templates
     * @param array<string,mixed> $parameters
     *
     * @return null|string
     */
    public function renderTemplates(array $templates, array $parameters): ?string
    {
        $this->addGlobal('template', $this);

        foreach ($this->renders as $engine) {
            foreach ($templates as $template) {
                if ($engine->getLoader()->exists($template)) {
                    return $engine->render($template, \array_replace($parameters, $this->globals));
                }
            }
        }

        return null;
    }
}
