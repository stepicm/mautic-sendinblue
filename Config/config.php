<?php

return [
    'name'        => 'Sendinblue integration',
    'description' => 'Allows to send E-mails with Sendinblue',
    'version'     => '1.0.3',
    'author'      => 'stepicm',
    'services'    => [
        'other' => [
            'mautic.transport.sendinblue_api' => [
                'class' => \MauticPlugin\MauticSendinblueBundle\Swiftmailer\Transport\SendinblueApiTransport::class,
                'arguments' => [
                    '%mautic.mailer_api_key%',
                    'translator',
                    'mautic.transport.sendinblue_api.callback',
                    'doctrine.orm.entity_manager',
                    'mautic.helper.bundle',
                ],
                'tags' => [
                    'mautic.email_transport',
                ],
                'tagArguments' => [
                    [
                        'transport_alias' => 'mautic.email.config.mailer_transport.sendinblue',
                        'field_api_key' => true,
                    ],
                ],
            ],
            'mautic.transport.sendinblue_api.callback' => [
                'class' => \MauticPlugin\MauticSendinblueBundle\Swiftmailer\Callback\SendinblueApiCallback::class,
                'arguments' => [
                    'mautic.email.model.transport_callback',
                    'monolog.logger.mautic',
                    'mautic.transport.sendinblue_api.parser',
                    'mautic.helper.bundle',
                ],
            ],
            'mautic.transport.sendinblue_api.publisher.data' => [
                'class' => \MauticPlugin\MauticSendinblueBundle\Publisher\Data\DataUpdater::class,
            ],
            'mautic.transport.sendinblue_api.publisher.runner' => [
                'class' => \MauticPlugin\MauticSendinblueBundle\Publisher\UpdateRunner::class,
            ],
            'mautic.transport.sendinblue_api.parser' => [
                'class' => \MauticPlugin\MauticSendinblueBundle\Parser\SendinblueResponseParser::class,
                'arguments' => [
                    'router',
                    'doctrine.orm.entity_manager',
                    'mautic.transport.sendinblue_api.publisher.runner',
                    'mautic.transport.sendinblue_api.publisher.data',
                    'mautic.helper.bundle',
                ],
            ],
        ],
    ],
    'parameters' => [
        'log_enabled' => false,
        'log_path'    => null,
    ],
];
