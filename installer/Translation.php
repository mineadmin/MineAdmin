<?php

namespace Installer;

class Translation
{

    private string $language;

    public function __construct(){
        $transFile = glob(__DIR__ . '/resouces/language/*.php');
        foreach ($transFile as $file) {
            $lang = pathinfo($file, PATHINFO_FILENAME);
            $this->{$lang} = require $file;
        }
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function trans(string $key,mixed $default): mixed
    {
        return $this->{$this->language}[$key] ?? $default;
    }
}