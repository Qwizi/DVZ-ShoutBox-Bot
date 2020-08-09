<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use MyLanguage;

use \Qwizi\DVZSB\Bot;

abstract class Command
{
    protected $shoutData = [];
    protected $commandData = [];
    protected $args = [];

    public function __construct(array $shoutData, array $commandData)
    {
        $this->shoutData = $shoutData;
        $this->commandData = $commandData;
        //$this->pattern = $this->createPattern("/^({command}|{command}[\s]((.*)))$/");
        //$this->args = $this->createArgs();
    }

    private function findArgument(string $name) {
        return array_search($name, array_column($this->args, 'name'));
    }

    protected function parseArguments(string $text) {
        $explodeText = explode('--', $text);
        unset($explodeText[0]);

        $args = [];

        foreach ($explodeText as $argumentFromText) {
            if (preg_match('/^([^=]+)=(.*)$/', $argumentFromText, $match)) {
                $argumentName = $match[1];
                $argumentValue = $match[2];
                $argumentIndex = $this->findArgument($argumentName);
                if ($argumentIndex !== false) {
                    switch($this->args[$argumentIndex]['type']) {
                        case 'int':
                            $argumentValue = intval($argumentValue);
                        break;
                        case 'float':
                            $argumentValue = floatval($argumentValue);
                        break;
                        case 'bool':
                            $argumentValue = boolval($argumentValue);
                        break;
                        case 'string':
                            $argumentValue = strval($argumentValue);
                        break;
                        default:
                            $argumentValue = strval($argumentValue);
                        break;
                    }

                    $args[] = [
                        'name' => $argumentName,
                        'value' => $argumentValue
                    ];
                }
            }
        }
        return $args;
    }

    protected function addArgument(string $name, string $type) {
        $aviableTypes = ['int', 'float', 'bool', 'string'];
        if (!in_array($type, $aviableTypes)) {
            $type = 'string';
        }
        $this->args[] = [
            'name' => $name,
            'type' => $type,
        ];
    }

    protected function getHint() {
        $message = \sprintf(
            "%s%s ", 
            $this->commandData['prefix'], 
            $this->commandData['command']
        );

        if (!empty($this->args)) {
            foreach ($this->args as $arg) {
                $message .= sprintf("--%s=<%s> ", $arg['name'], $arg['type']);
            }
        }
        return $message;
    }

    abstract public function handle();
}
