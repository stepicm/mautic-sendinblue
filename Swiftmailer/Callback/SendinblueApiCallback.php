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
        $sendinblueHash = $this->em->getRepository(SendinblueHash::class)->findOneBy(['sendinblueId' => $parameters['message-id']]);
        $emailStats = $this->em->getRepository(EmailStats::class)->findOneBy(['trackingHash' => $sendinblueHash->getLeadHashId()]);
        $lead = $this->em->getRepository(CustomSimpleContact::class)->findOneBy(['id' => $emailStats->getLeadId()]);
        $campaign = $this->em->getRepository(CustomSimpleCampaign::class)->findOneBy(['id' => $emailStats->getSourceId()]);

        $dwhStat = new DwhStats();
        $dwhStat->setUsername($lead->getUsername());
        $dwhStat->setCampaignId($emailStats->getSourceId());
        $dwhStat->setCampaignCategoryId($campaign->getCategoryId());
        $dwhStat->setChannelId($emailStats->getEmailId());
        $dwhStat->setChannel('email');
        $dwhStat->setEventType($parameters['event']);

        $date = new \DateTime();
        $date->setTimestamp($parameters['ts_event']);

        $dwhStat->setEventTs($date);

        $this->em->persist($dwhStat);
        $this->em->flush();
    }
}
