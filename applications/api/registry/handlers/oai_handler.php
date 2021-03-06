<?php

namespace ANDS\API\Registry\Handler;

use ANDS\Cache\Cache;
use ANDS\Registry\Providers\OAIRecordRepository;
use ANDS\OAI\Exception\BadArgumentException;
use ANDS\OAI\ServiceProvider;
use ANDS\Util\Config;

class OaiHandler extends Handler
{
    /** @var int cache ttl in minutes */
    protected static $cacheDuration = 60;

    public function handle()
    {
        date_default_timezone_set(Config::get('app.timezone'));
        $this->getParentAPI()->providesOwnResponse();
        $options = array_merge($_GET, $_POST);

        $provider = new ServiceProvider(new OAIRecordRepository());
        $provider->setOptions($options);

        try {
            $response = $provider->get();
        } catch (\Exception $e) {
            $exception = new BadArgumentException(get_exception_msg($e));
            $response = $provider->getExceptionResponse($exception);
            return (string)$response->getResponse()->getBody();
        }

        monolog([
            'event' => $response->errored() ? 'error' : $options['verb'],
            'errors' => implode(' ', $response->getErrors()),
            'errored' => $response->errored(),
            'request' => $options
        ], "oai_api", "info", true);

        return (string)$response->getResponse()->getBody();
    }

    /**
     * Handle the OAI Request
     * Using ANDS\OAI\ServiceProvider
     *
     * @param $options
     * @return string
     * @throws \ANDS\OAI\Exception\OAIException
     */
    public function handleOAIRequest($options)
    {
        $provider = new ServiceProvider(
            new OAIRecordRepository()
        );
        $provider->setOptions($options);
        try {
            $response = $provider->get()->getResponse();
        } catch (\Exception $e) {
            $exception = new BadArgumentException(get_exception_msg($e));
            $response = $provider->getExceptionResponse($exception);
            return (string)$response->getResponse()->getBody();
        }
        return (string)$response->getBody();
    }
}