<?php

namespace MauticPlugin\MauticSendinblueBundle\Swiftmailer\Callback;

use Mautic\EmailBundle\Model\TransportCallback;
use MauticPlugin\MauticSendinblueBundle\Swiftmailer\Exception\ResponseItemException;
use Symfony\Component\HttpFoundation\Request;
use Monolog\Logger;
use MauticPlugin\MauticSendinblueBundle\Parser\SendinblueResponseParser;

/**
 * Class SendinblueApiCallback.
 */
class SendinblueApiCallback
{

    /**
     * @var TransportCallback
     */
    private $transportCallback;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var SendinblueResponseParser
     */
    protected $parser;

    /**
     * SendinblueApiCallback constructor.
     *
     * @param TransportCallback $transportCallback
     * @param Logger $logger
     */
    public function __construct(TransportCallback $transportCallback, Logger $logger, SendinblueResponseParser $parser)
    {
        $this->transportCallback = $transportCallback;
        $this->logger = $logger;
        $this->parser = $parser;
    }

    /**
     * Processes Sendinblue API callback request.
     *
     * @param Request $request
     */
    public function processCallbackRequest(Request $request)
    {
        $parameters = $request->request->all();

        // send data via Parser/SendinblueResponseParser
        // if returns true, use previos principle
        // else end it in this phase
        if ($this->parser->request($request)->parse()) {
            if (isset($parameters['event']) && CallbackEnum::shouldBeEventProcessed($parameters['event'])) {
                try {
                    $item = new ResponseItem($parameters);
                    $this->transportCallback->addFailureByAddress($item->getEmail(), $item->getReason(), $item->getDncReason());
                }
                catch (ResponseItemException $e) {
                    $this->logger->log('error', $e->getMessage());
                }
            }
        }
    }
}
