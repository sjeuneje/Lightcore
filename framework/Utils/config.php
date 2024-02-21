<?php

function config(string $path)
{
    [$configFile, $configKey] = explode('.', $path);

    return (include BASE_PATH . "/config/$configFile.php")[$configKey];
}