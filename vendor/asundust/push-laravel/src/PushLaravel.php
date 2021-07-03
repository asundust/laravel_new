<?php

namespace Asundust\PushLaravel;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class PushLaravel
{
    private $pushUrl;
    private $pushSecret;

    /**
     * PushLaravel constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->pushUrl = $config['push_url'] ?? '';
        $this->pushSecret = $config['push_secret'] ?? '';
    }

    /**
     * send.
     *
     * @return array|bool
     *
     * @throws PushLaravelException
     * @throws GuzzleException
     */
    public function send(string $title, ?string $content = null, ?string $url = null, ?string $urlTitle = null)
    {
        if (!$this->pushUrl || !$this->pushSecret) {
            throw new PushLaravelException('PushLaravel Config Error');
        }

        $formParams = [
            'title' => $title,
        ];
        if ($content) {
            $formParams['content'] = $content;
        }
        if ($url) {
            $formParams['url'] = $url;
        }
        if ($urlTitle) {
            $formParams['url_title'] = $urlTitle;
        }

        return json_decode(
            (new Client([
                'timeout' => 10,
                'verify' => false,
                'http_errors' => false,
            ]))
                ->post(rtrim($this->pushUrl, '/').'/push/'.$this->pushSecret, [
                    'form_params' => $formParams,
                ])
                ->getBody()
                ->getContents(),
            true);
    }
}
