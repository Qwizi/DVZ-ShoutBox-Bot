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
    }

    private function findArgument(string $name) {
        return array_search($name, array_column($this->args, 'name'));
    }

    protected function parseArguments(string $text) {
        $explodeText = explode('--', $text);
        unset($explodeText[0]);
        $explodeText = array_values($explodeText);
        $args = [];

        if (!empty($this->args)) {
            for ($i = 0; $i < count($this->args); $i++) {
                if (!empty($explodeText)) {
                    if (preg_match('/^([^=]+)=(.*)$/', $explodeText[$i], $match)) {
                        $arg = [];
                        $arg['name'] = '';
                        $arg['value'] = '';
                        $arg['validated'] = false;

                        $argumentName = $match[1];
                        $argumentValue = $match[2];
    
                        if ($this->args[$i]['name'] == $argumentName || in_array($argumentName, $this->args[$i]['aliases'])) {
                            switch($this->args[$i]['type']) {
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
                            $arg['name'] = $this->args[$i]['name'];
                            $arg['value'] = $argumentValue;

                            $validators = $this->args[$i]['validators'];
                            if (!empty($validators)) {
                                $validatePoints = 0;
                                foreach($validators as $validator) {
                                    $validator->setShoutData($this->shoutData);
                                    $validated = $validator->validate($argumentValue) ? $validatePoints++ : $validatePoints--;
                                }

                                $arg['validated'] = $validatePoints == count($validators) ? true : false;

                            } else {
                                $arg['validated'] = true;
                            }
                        }

                        $args[] = [
                            'name' => $arg['name'],
                            'value' => $arg['value'],
                            'validated' => $arg['validated']
                        ];
                    }
                }
            }
        }
        return $args;
    }

    protected function addArgument(string $name, string $type, array $options=[]) {
        $aviableTypes = ['int', 'float', 'bool', 'string'];
        if (!in_array($type, $aviableTypes)) {
            $type = 'string';
        }
        $this->args[] = [
            'name' => $name,
            'type' => $type,
            'is_required' => $options['is_required'],
            'validators' => $options['validators'],
            'aliases' => $options['aliases']
        ];
    }

    protected function getHint() {
        $message = \sprintf(
            "Usage %s%s ", 
            $this->commandData['prefix'], 
            $this->commandData['command']
        );

        if (!empty($this->args)) {
            foreach ($this->args as $arg) {
                if (!empty($arg['aliases'])) {
                    $aliases = '';
                    $lastAlias = end($arg['aliases']);
                    foreach ($arg['aliases'] as $key => $value) {
                        $comma = ', ';
                        if ($lastAlias == $value) $comma = '';
                        $aliases .= sprintf("--%s%s", $value, $comma);
                    }
                    $message .= sprintf("--%s [%s]=<%s> ", $arg['name'], $aliases, $arg['type']);
                } else {
                    $message .= sprintf("--%s=<%s> ", $arg['name'], $arg['type']);
                }
            }
        }
        return $message;
    }

    abstract public function handle();
}
