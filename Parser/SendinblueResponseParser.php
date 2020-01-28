<?php

namespace MauticPlugin\MauticSendinblueBundle\Parser;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use MauticPlugin\MauticSendinblueBundle\Entity\SendinblueHash;
use Doctrine\ORM\EntityManager;
use MauticPlugin\MauticSendinblueBundle\Publisher\UpdateRunner;
use MauticPlugin\MauticSendinblueBundle\Publisher\Data\DataUpdater;

class SendinblueResponseParser
{
    const SB_CLICK = 'click';
    const SB_DELIVERED = 'delivered';
    const SB_OPENED = 'opened';
    const SB_REQUEST = 'request';
    const SB_UNIQUE_OPENED = 'unique_opened';
    const SB_UNSUBSCRIBED = 'unsubscribed';
    const SB_SOFT_BOUNCE = 'soft_bounce';
    const SB_HARD_BOUNCE = 'hard_bounce';
    const SB_ERROR = 'error';
    const SB_SPAM = 'spam';
    const SB_BLOCKED = 'blocked';
    const SB_INVALID_EMAIL = 'invalid_email';

    const EMAIL_TRACKER = 'mautic_email_tracker';
    const EMAIL_WEBVIEW = 'mautic_email_webview';
    const EMAIL_UNSUBSCRIBE = 'mautic_email_unsubscribe';
    const EMAIL_RESUBSCRIBE = 'mautic_email_resubscribe';

    const ACTION_UNSUBSCRIBE = 'unsubscribe';
    const ACTION_RESUBSCRIBE = 'resubscribe';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var DataUpdater
     */
    protected $updater;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var UpdateRunner
     */
    protected $runner;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var array
     */
    protected $payload;

    /**
     * @param Router $container
     * @param EntityManager $doctrine
     * @param UpdateRunner $runner
     * @param DataUpdater $updater
     */
    public function __construct(Router $router, EntityManager $doctrine, UpdateRunner $runner, DataUpdater $updater)
    {
        $this->router = $router;
        $this->em = $doctrine;
        $this->runner = $runner;
        $this->updater = $updater;
    }

    /**
     * @param Request $request
     * @return SendinblueResponseParser
     */
    public function request(Request $request)
    {
        $this->request = $request;
        $this->params = $request->request->all();

        return $this;
    }

    /**
     * @return bool
     */
    public function parse()
    {
        // continues to DoNotContact (DNC)
        $status = true;
        $run = true;

        $this->payload = [
            'event' => $this->params['event'],
            'sb_id' => $this->params['message-id'],
            'email' => $this->params['email'],
        ];

        switch ($this->params['event']) {
            case self::SB_OPENED:
            case self::SB_UNIQUE_OPENED:
                // send request to mautic
                // send request to BQ
                $status = false;
                $this->payload['url'] = $this->url(self::EMAIL_TRACKER, $this->params['message-id']);
            break;
            case self::SB_SPAM:
            case self::SB_SOFT_BOUNCE:
                $status = false;
                $this->payload['url'] = false;
                $this->payload['action'] = $this->params['event'];
            break;
            case self::SB_HARD_BOUNCE:
            case self::SB_BLOCKED:
            case self::SB_INVALID_EMAIL:
                $status = true;
                $this->payload['url'] = false;
                $this->payload['action'] = $this->params['event'];
            break;
            case self::SB_REQUEST:
                $status = false;
                $run = false;
                // nothing -> confirmation of request
                // check if sb_id is in DB
            break;
            case self::SB_DELIVERED:
                $status = false;
                $run = false;
                // send request to BQ
            break;
            case self::SB_CLICK:
            case self::SB_UNSUBSCRIBED:
                $status = false;
                // send request to mautic
                // send request to BQ
                // click link is in the request object under link array
                if (isset($this->params['link'])) {
                    $val = $this->checkLink($this->params['link']);

                    if ($val['url']) {
                        $this->payload['url'] = $this->params['link'];
                    } else {
                        $status = true;

                        $this->payload['url'] = $val['url'];
                        $this->payload['action'] = $val['action'];
                    }
                } else {
                    $this->payload['url'] = $this->url(self::EMAIL_WEBVIEW, $this->params['message-id']);
                }
            break;
            case self::SB_ERROR:
            default:
                $status = false;
                $run = false;
                // log error unknown response
            break;
        }

        if ($run) {
            $this->run();
        }

        return $status;
    }

    protected function url($route, $sbHash)
    {
        $entity = $this->getIdHashFromSbHash($sbHash);

        if (null !== $entity) {
            return $this->router->generate($route, ['idHash' => $entity->getLeadHashId()], UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            return null;
        }
    }

    protected function run()
    {
        $this->updater->setData($this->payload);

        return $this->runner->send($this->updater);
    }

    private function getIdHashFromSbHash($sbHash)
    {
        return $this->em->getRepository(SendinblueHash::class)->findOneBy(['sendinblueId' => $sbHash]);
    }

    private function checkLink($link)
    {
        $rVal = [
            'url' => true,
            'action' => false, 
        ];

        if (false !== strpos($link, self::ACTION_UNSUBSCRIBE)) {
            $rVal = [
                'url' => false,
                'action' => self::ACTION_UNSUBSCRIBE,
            ];
        }

        if (false !== strpos($link, self::ACTION_RESUBSCRIBE)) {
            $rVal = [
                'url' => false,
                'action' => self::ACTION_RESUBSCRIBE,
            ];
        }

        return $rVal;
    }
}
