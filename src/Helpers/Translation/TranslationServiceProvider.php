<?php

namespace Engelsystem\Helpers\Translation;

use Engelsystem\Config\Config;
use Engelsystem\Container\ServiceProvider;
use Gettext\Translations;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Session\Session;

class TranslationServiceProvider extends ServiceProvider
{
    /** @var GettextTranslator */
    protected $translators = [];

    public function register(): void
    {
        /** @var Config $config */
        $config = $this->app->get('config');
        /** @var Session $session */
        $session = $this->app->get('session');

        $locales = $config->get('locales');
        $locale = $config->get('default_locale');
        $fallbackLocale = $config->get('fallback_locale', 'en_US');

        $sessionLocale = $session->get('locale', $locale);
        if (isset($locales[$sessionLocale])) {
            $locale = $sessionLocale;
        }

        $session->set('locale', $locale);

        $translator = $this->app->make(
            Translator::class,
            [
                'locale'                => $locale,
                'locales'               => $locales,
                'fallbackLocale'        => $fallbackLocale,
                'getTranslatorCallback' => [$this, 'getTranslator'],
                'localeChangeCallback'  => [$this, 'setLocale'],
            ]
        );
        $this->app->singleton(Translator::class, function () use ($translator) {
            return $translator;
        });
        $this->app->alias(Translator::class, 'translator');
    }

    /**
     * @param string $locale
     * @codeCoverageIgnore
     */
    public function setLocale(string $locale): void
    {
        $locale .= '.UTF-8';
        // Set the users locale
        putenv('LC_ALL=' . $locale);
        setlocale(LC_ALL, $locale);

        // Reset numeric formatting to allow output of floats
        putenv('LC_NUMERIC=C');
        setlocale(LC_NUMERIC, 'C');
    }

    /**
     * @param string $locale
     * @return GettextTranslator
     */
    public function getTranslator(string $locale): GettextTranslator
    {
        if (!isset($this->translators[$locale])) {
            $file = $this->getFile($locale);

            /** @var GettextTranslator $translator */
            $translator = $this->app->make(GettextTranslator::class);

            /** @var Translations $translations */
            $translations = $this->app->make(Translations::class);
            if (Str::endsWith($file, '.mo')) {
                $translations->addFromMoFile($file);
            } else {
                $translations->addFromPoFile($file);
            }

            $translator->loadTranslations($translations);

            $this->translators[$locale] = $translator;
        }

        return $this->translators[$locale];
    }

    /**
     * @param string $locale
     * @return string
     */
    protected function getFile(string $locale): string
    {
        $filepath = $file = $this->app->get('path.lang') . '/' . $locale . '/default';
        $file = $filepath . '.mo';

        if (!file_exists($file)) {
            $file = $filepath . '.po';
        }

        return $file;
    }
}
