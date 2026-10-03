<?php

declare(strict_types=1);

namespace Application\Form;

use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator;
use Laminas\Filter;

class ContactForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = 'contact-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('action', '/contact/process');
        $this->setAttribute('method', 'post');

        // Campo: nombre
        $this->add([
            'name'       => 'nombre',
            'type'       => Element\Text::class,
            'options'    => [
                'label' => 'Nombre completo',
            ],
            'attributes' => [
                'id'          => 'nombre',
                'class'       => 'form-control',
                'placeholder' => 'Tu nombre',
                'maxlength'   => 100,
            ],
        ]);

        // Campo: email
        $this->add([
            'name'       => 'email',
            'type'       => Element\Text::class,
            'options'    => [
                'label' => 'Correo electrónico',
            ],
            'attributes' => [
                'id'          => 'email',
                'class'       => 'form-control',
                'placeholder' => 'tu@correo.com',
                'maxlength'   => 255,
            ],
        ]);

        // Campo: mensaje
        $this->add([
            'name'       => 'mensaje',
            'type'       => Element\Textarea::class,
            'options'    => [
                'label' => 'Mensaje',
            ],
            'attributes' => [
                'id'          => 'mensaje',
                'class'       => 'form-control',
                'placeholder' => 'Escribe tu mensaje...',
                'rows'        => 5,
                'maxlength'   => 1000,
            ],
        ]);

        // Botón: enviar
        $this->add([
            'name'       => 'submit',
            'type'       => Element\Submit::class,
            'attributes' => [
                'value' => 'Enviar mensaje',
                'class' => 'btn btn-primary',
            ],
        ]);
    }

    /**
     * Especificación del InputFilter: filtros y validadores por campo.
     * Laminas\Form lo aplica automáticamente al llamar a isValid().
     */
    public function getInputFilterSpecification(): array
    {
        return [
            'nombre' => [
                'required' => true,
                'filters'  => [
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\StripTags::class],
                ],
                'validators' => [
                    [
                        'name'    => Validator\NotEmpty::class,
                        'options' => [
                            'messages' => [
                                Validator\NotEmpty::IS_EMPTY => 'El nombre es obligatorio.',
                            ],
                        ],
                    ],
                    [
                        'name'    => Validator\StringLength::class,
                        'options' => [
                            'min'      => 2,
                            'max'      => 100,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'El nombre es demasiado corto (mínimo 2 caracteres).',
                                Validator\StringLength::TOO_LONG  => 'El nombre es demasiado largo (máximo 100 caracteres).',
                            ],
                        ],
                    ],
                ],
            ],

            'email' => [
                'required' => true,
                'filters'  => [
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    [
                        'name'    => Validator\NotEmpty::class,
                        'options' => [
                            'messages' => [
                                Validator\NotEmpty::IS_EMPTY => 'El correo electrónico es obligatorio.',
                            ],
                        ],
                    ],
                    [
                        'name'    => Validator\Regex::class,
                        'options' => [
                            'pattern'  => '/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
                            'messages' => [
                                Validator\Regex::NOT_MATCH => 'El correo electrónico no tiene un formato válido.',
                            ],
                        ],
                    ],
                    [
                        'name'    => Validator\StringLength::class,
                        'options' => [
                            'max'      => 255,
                            'messages' => [
                                Validator\StringLength::TOO_LONG => 'El correo es demasiado largo (máximo 255 caracteres).',
                            ],
                        ],
                    ],
                ],
            ],

            'mensaje' => [
                'required' => true,
                'filters'  => [
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\StripTags::class],
                ],
                'validators' => [
                    [
                        'name'    => Validator\NotEmpty::class,
                        'options' => [
                            'messages' => [
                                Validator\NotEmpty::IS_EMPTY => 'El mensaje es obligatorio.',
                            ],
                        ],
                    ],
                    [
                        'name'    => Validator\StringLength::class,
                        'options' => [
                            'min'      => 10,
                            'max'      => 1000,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'El mensaje es demasiado corto (mínimo 10 caracteres).',
                                Validator\StringLength::TOO_LONG  => 'El mensaje es demasiado largo (máximo 1000 caracteres).',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}