<?php namespace Mberizzo\Locationemailrules\Classes;

class RuleFieldsBuilder
{

    protected $form;

    public function __construct($form)
    {
        $this->form = $form;
    }

    public function getConfigAsArray($defaultValues = null, $states)
    {
        $values = [];

        if ($defaultValues) {
            $values = json_decode($defaultValues, true)[$this->form->id] ?? [];
        }

        return [
            "email_rules[{$this->form->id}]" => [
                'type' => 'nestedform',
                'usePanelStyles' => false,
                'tab' => $this->form->name,
                'form' => [
                    'enableDefaults' => true,
                    'fields' => [
                        // 'form_id' => [ // this is an example of partial with input hidden in repeater
                        //     'type' => 'partial',
                        //     'default' => $this->form->id,
                        //     'path' => '$/mberizzo/locationemailrules/models/rule/fields/_form_id_field.htm'
                        // ],
                        'emails' => [
                            'type' => 'taglist',
                            'mode' => 'array',
                            'label' => 'Enviar a:',
                            'comment' => 'Listado de correos separados por coma',
                            'default' => array_get($values, 'emails'),
                        ],
                        'exceptions' => [
                            'label' => 'Excepciones',
                            'type' => 'repeater',
                            'prompt' => 'Agregar Excepcion',
                            'default' => array_get($values, 'exceptions', []),
                            'form' => [
                                'fields' => [
                                    'state' => [
                                        'span' => 'left',
                                        'label' => 'State',
                                        'type' => 'dropdown',
                                        'options' => $states,
                                    ],
                                    'emails' => [
                                        'span' => 'right',
                                        'label' => 'Emails',
                                        'type' => 'taglist',
                                        'mode' => 'array',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
