<?php
namespace ANDS\Registry\API\Middleware;


use ANDS\Registry\API\Request;
use ANDS\Util\Config;
use \Exception as Exception;

class IPRestrictionMiddleware
{
    private $message;

    public function pass()
    {
        $whitelist = Config::get('app.api_whitelist_ip');
        if (!$whitelist) {
            throw new Exception("Whitelist IP not configured properly. This operation is unsafe.");
        }
        $ip = Request::ip();
        if (!in_array($ip, $whitelist)) {
            throw new Exception("IP: $ip is not whitelist for this behavior");
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}