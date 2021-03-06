<?php

namespace MauticPlugin\MauticSendinblueBundle\Swiftmailer\Callback;

use Mautic\EmailBundle\Model\TransportCallback;
use MauticPlugin\MauticSendinblueBundle\Swiftmailer\Exception\ResponseItemException;
use Symfony\Component\HttpFoundation\Request;
use Monolog\Logger;
use MauticPlugin\MauticSendinblueBundle\Parser\SendinblueResponseParser;
use MauticPlugin\MauticSendinblueBundle\Entity\SendinblueHash;
use MauticPlugin\MauticSendinblueBundle\Entity\EmailStats;
use MauticPlugin\MauticSendinblueBundle\Entity\DwhStats;
use MauticPlugin\MauticSendinblueBundle\Entity\CustomSimpleContact;
use MauticPlugin\MauticSendinblueBundle\Entity\CustomSimpleCampaign;
use MauticPlugin\MauticSendinblueBundle\Entity\CustomSimpleCampaignEvents;
use Mautic\CoreBundle\Helper\BundleHelper;
use Doctrine\ORM\EntityManager;

/**
 * Class SendinblueApiCallback.
 */
class SendinblueApiCallback
{
    const LOG_TYPE_RESPONSE = 'response';
    const LOG_TYPE_ERROR = 'error';

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
     * @var BundleHelper
     */
    protected $helper;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var array
     */
    protected $configParams;

    /**
     * @var string
     */
    protected $requestTimeString;

    /**
     * SendinblueApiCallback constructor.
     *
     * @param TransportCallback $transportCallback
     * @param Logger $logger
     */
    public function __construct(TransportCallback $transportCallback, Logger $logger, SendinblueResponseParser $parser, BundleHelper $helper, EntityManager $doctrine)
    {
        $this->transportCallback = $transportCallback;
        $this->logger = $logger;
        $this->parser = $parser;
        $this->helper = $helper;
        $this->em = $doctrine;
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

                    // system logging
                    $this->additionalLog($e->getMessage() . ' - ' . json_encode($parameters), self::LOG_TYPE_ERROR);
                }
            }
        }

        // save stat to db
        $this->saveDwhStat($parameters);
    }

    private function additionalLog($what, $type)
    {
        if (false === isset($this->configParams)) {
            $this->configParams = $this->helper->getBundleConfig('MauticSendinblueBundle', 'parameters', true);
        }

        if (false === isset($this->requestTimeString)) {
            $this->requestTimeString = '[' . date('Y/m/d H:i:s') . '] - ';
        }

        // system logging
        if ($this->configParams['log_enabled']) {
            $logFileName = sprintf('%s/%s-sendinblue-%ss.log', $this->configParams['log_path'], date('Y-m-d'), $type);
            file_put_contents($logFileName, $this->requestTimeString . $what . PHP_EOL, FILE_APPEND);
        }
    }

    private function saveDwhStat($parameters)
    {
        // test webhook exception
        if (isset($parameters['email']) && $parameters['email'] === 'example@example.com') {
            return;
        }

        if (isset($parameters['message-id'])) {
            $sendinblueHash = $this->em->getRepository(SendinblueHash::class)->findOneBy(['sendinblueId' => $parameters['message-id']]);

            if (is_null($sendinblueHash)) {
                return;
            }

            $emailStats = $this->em->getRepository(EmailStats::class)->findOneBy(['trackingHash' => $sendinblueHash->getLeadHashId()]);
            $lead = $this->em->getRepository(CustomSimpleContact::class)->findOneBy(['id' => $emailStats->getLeadId()]);
            $campaignEvent = $this->em->getRepository(CustomSimpleCampaignEvents::class)->findOneBy(['id' => $emailStats->getSourceId()]);

            // safety net
            if (is_null($campaignEvent)) {
                return;
            }

            $campaign = $this->em->getRepository(CustomSimpleCampaign::class)->findOneBy(['id' => $campaignEvent->getCampaignId()]);

            $dwhStat = new DwhStats();

            if (isset($lead)) {
                $dwhStat->setUsername($lead->getUsername());
                $dwhStat->setPlayerId($lead->getPlayerId());
            }
            if (isset($emailStats) && isset($campaignEvent)) {
                $dwhStat->setCampaignId($campaignEvent->getCampaignId());
                $dwhStat->setChannelId($emailStats->getEmailId());
            }
            if (isset($campaign)) {
                $dwhStat->setCampaignCategoryId($campaign->getCategoryId());
            }
            if (isset($parameters['event'])) {
                $dwhStat->setEventType($parameters['event']);
            }
            $dwhStat->setChannel('email');

            if (isset($parameters['ts_event'])) {
                $date = new \DateTime();
                $date->setTimestamp($parameters['ts_event']);
                $dwhStat->setEventTs($date);
            }

            $this->em->persist($dwhStat);
            $this->em->flush();
        }
    }
}
