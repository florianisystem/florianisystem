<?php

declare(strict_types=1);

/**
 * Obfuscator plugin for Craft CMS 3.x, 4.x
 *
 * Adds a Twig filter to obfuscate emails using Hivelogic Enkoder.
 *
 * @link      https://miranj.in/
 * @copyright Copyright (c) 2018 Miranj
 */

 namespace Engelsystem\Renderer\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\Markup;
use Twig\Environment;
use Engelsystem\Helpers\StandalonePHPEnkoder;


class Obfuscation extends AbstractExtension
{
    public function __construct(protected StandalonePHPEnkoder $enkoder)
    {
    }

    public function getFilters(): array
    {
        $options = ['needs_environment' => true];

        return [
            new TwigFilter('enkode', [$this, 'enkodeFilter'], $options),
            new TwigFilter('enkode_emails', [$this, 'enkodeEmailsFilter'], $options),
            new TwigFilter('enkode_mailtos', [$this, 'enkodeMailtosFilter'], $options),
            new TwigFilter('enkode_plaintext_emails', [$this, 'enkodePlaintextEmailsFilter'], $options),
        ];
    }

    public function enkodeFilter(Environment $env, string $str, string $message = 'JavaScript is required to reveal this message.'): Markup
    {
        $str = $this->enkoder->enkode($str, $message);
        return $this->getTwigMarkup($env, $str);
    }

    public function enkodeEmailsFilter(Environment $env, string $str): Markup
    {
        $str = $this->enkoder->enkodeAllEmails($str);
        return $this->getTwigMarkup($env, $str);
    }

    public function enkodeMailtosFilter(Environment $env, string $str): Markup
    {
        $str = $this->enkoder->enkodeMailtos($str);
        return $this->getTwigMarkup($env, $str);
    }

    public function enkodePlaintextEmailsFilter(Environment $env, string $str): Markup
    {
        $str = $this->enkoder->enkodePlaintextEmails($str);
        return $this->getTwigMarkup($env, $str);
    }

    protected function getTwigMarkup(Environment $env, string $str): Markup
    {
        $charset = $env->getCharset();
        return new Markup($str, $charset);
    }
}
