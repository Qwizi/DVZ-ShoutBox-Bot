<?php

declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use \Qwizi\DVZSB\Bot;

abstract class Command
{
    const EMPTY_ARGS_POINTS = 2;
    protected $shoutData = [];
    protected $commandData = [];
    protected $args = [];

    public function __construct(array $shoutData, array $commandData)
    {
        $this->shoutData = $shoutData;
        $this->commandData = $commandData;
    }

    private function findArgument(string $name)
    {
        return array_search($name, array_column($this->args, 'name'));
    }

    protected function parseArguments(string $text)
    {
        // Tworzymy ze stringa tablice w poszukiowaniu argumentow zaczynajacych sie na --
        $explodeText = explode('--', $text);
        // Usuwamy pierwszy element z tablicy, poniewaz jest to zawsze komenda wraz z prefixem, czego jest nam nie potrzebna
        unset($explodeText[0]);
        // Reindexujemy tablice
        $explodeText = array_values($explodeText);

        // Tablicja przechowujaca zwalidowane argumenty
        $args = [];

        // Sprawdzamy oczywiscie czy komenda ma dodane jakes argumenty
        if (!empty($this->args)) {
            // Iterujemy po argumentach
            for ($i = 0; $i < count($this->args); $i++) {
                // pomocniczna tablica do pojedynczego argumentu
                $arg = [];
                $arg['name'] = '';
                $arg['value'] = '';
                $arg['validated'] = false;
                // zmienna do sprawdzania stanu walidacji walidatorów
                $validatorsValidate = false;
                // zmienna do sprawdzania stanu walidacji argumentow
                $argumentValidate = false;
                
                // Sprawdzamy czy tablica z rodzielonymi argumentami nie jest pusta
                if (!empty($explodeText)) {
                    // Sprawdzamy wyrazeniem czy w tej nablicy znajduja sie argumenty z poprawnym syntaxem argument=value
                    if (preg_match('/^([^=]+)=(.*)$/', $explodeText[$i], $match)) {
                        // Ustawiamy stan argumentow na prawde
                        $argumentValidate = true;
                        // Pierwsze znalezienie jest odpowiedzialne za nazwe argumentu
                        $argumentName = $match[1];
                        // Drugie znalezienie jest odpowiedzialne za wartosc argumentu
                        $argumentValue = $match[2];

                        // Sprawdzamy czy dodana nazwa/alias jest rowna znalezionemu argumentowi w rozdzielonej tablic
                        if ($this->args[$i]['name'] == $argumentName || in_array($argumentName, $this->args[$i]['aliases'])) {
                            switch ($this->args[$i]['type']) {
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
                            // Ustawiamy nazwe i wartosc argumentu
                            $arg['name'] = $this->args[$i]['name'];
                            $arg['value'] = $argumentValue;

                            // Teraz sprawdzamy czy argument ma dodane jekies walidatory
                            $validators = $this->args[$i]['validators'];
                            if (!empty($validators)) {
                                // Ustawiamy punkty walidatorow
                                $validatorPoints = 0;
                                // Iterujemy po walidatorach
                                foreach ($validators as $validator) {
                                    // Pzekazujemy do walidatora dane z shouta
                                    $validator->setShoutData($this->shoutData);
                                    // Walidujemy wartosc argumentu
                                    $validator->validate($argumentValue);
                                    // Jezeli walidacja przebiegla pomyslnie przyznajemy 1 punkt do punktacji walidatorow
                                    $validator->getValidateState() 
                                        ? $validatorPoints++ 
                                        : $validatorPoints--;
                                }
                                // Jezeli punktacja walidatorow jest rowna ilosi walidatoriw to ustawuiamy zmienna przechowaujaca stan walidatrow na true
                                $validatorsValidate = $validatorPoints == count($validators) ? true : false;
                            } 
                            else 
                            {
                                // Oczywiscie jezeli nie ma dodanych zadnych walidatorow to stan walidatorow bedzie zawsze prawda
                                $validatorsValidate = true;
                            }
                        } 
                        else 
                        {
                            // Jezeli nie znaleziono argumentow w rozdzielonej tabicy ustawiamy stan argumentow na false
                            $argumentValidate = false;
                        }
                    }
                } 
                else 
                {
                    // Jezeli nie znaleziono argumentow ALE zostala ustawiona opcja required przy dodawaniu argumentow trzeba ustawic stan argumentow na false
                    if ($this->args[$i]['is_required']) {
                        $argumentValidate = false;
                    } else {
                        $argumentValidate = true;
                    }
                }

                // Jezeli stan argumentow i stan walidatorow jest prawda uznajemy ze argumennt jest poprawnie zwalidowany
                if ($argumentValidate) {
                    if ($validatorsValidate) {
                        $arg['validated'] = true;
                    } 
                    else 
                    {
                        foreach($validators as $validator) {
                            if (!$validator->getValidateState()) {
                                $arg['validated'] = false;
                                $validator->shoutErrorMsg();
                            }
                        }
                    }
                } 
                else 
                {
                    $arg['validated'] = false;
                    Bot::shout($this->getHint(), $this->shoutData['uid'], $this->shoutData['shout_id']);
                }

                $args[] = [
                    'name' => $arg['name'],
                    'value' => $arg['value'],
                    'validated' => $arg['validated'],
                ];
            }
        }
        return $args;
    }

    protected function addArgument(string $name, string $type, array $options = [])
    {
        $aviableTypes = ['int', 'float', 'bool', 'string'];
        if (!in_array($type, $aviableTypes)) {
            $type = 'string';
        }
        $this->args[] = [
            'name' => $name,
            'type' => $type,
            'is_required' => $options['is_required'],
            'validators' => $options['validators'],
            'aliases' => $options['aliases'],
        ];
    }

    public function getHint()
    {
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
                        if ($lastAlias == $value) {
                            $comma = '';
                        }

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
