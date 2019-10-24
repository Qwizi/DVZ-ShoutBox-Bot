<?php

namespace Qwizi\DVZSB\Interfaces;

interface CommandManagerInterface
{
    public static function createInstance(DB_Base $db, datacache $cache);
    public static function getInstance();
    public static function i();
    private function setCommands(array $commands): void;
    public function getCommands(): array;
    private function getCommandsFromCache(): array;
    public function createCommand(array $commandData): voidd;
}