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

/**
 * Carries information about a rendered view.
 */
final class Source
{
    /** @var string */
    private $template;

    /** @var bool */
    private $cached;

    /**
     * @param string $template The template name
     * @param bool   $cached   If the template is cached
     */
    public function __construct(string $template, bool $cached = false)
    {
        $this->cached   = $cached;
        $this->template = $template;
    }

    /**
     * Returns the object string representation.
     *
     * @return string The template name
     */
    public function __toString()
    {
        return $this->template;
    }

    /**
     * Returns true if contents are cached to avoid recompile.
     *
     * @return bool
     */
    public function isCached(): bool
    {
        return $this->cached;
    }

    /**
     * Returns if the current content is a file
     */
    public function isFile(): bool
    {
        return \file_exists($this->template);
    }

    /**
     * Returns the content of the template.
     *
     * @return string The template content
     */
    public function getContent(): string
    {
        return $this->template;
    }
}