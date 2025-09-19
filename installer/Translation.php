<?php

namespace Installer;

class Translation
{

    private string $language;

    /**
     * @var <string,mixed>[] $trans
     */
    private array $trans = [];

    public function __construct(){
        $transFile = glob(__DIR__ . '/resources/language/*.php');
        foreach ($transFile as $file) {
            $lang = pathinfo($file, PATHINFO_FILENAME);
            $this->trans[$lang] = include $file;
        }
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function trans(string $key,mixed $default): mixed
    {
        return $this->trans[$this->language][$key] ?? $default;
    }
}