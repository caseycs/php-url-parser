<?php
namespace UrlParser;

class UrlParser
{
    const PORT_DEFAULT = 80;
    const PATTERN_FULL_URL = '~^(?<scheme>.*)://(?:(?<user>[^:]+):(?<pass>[^@]+)@)?(?<host>[^/:]*)(?::(?<port>\d+))?'
        .'(?:/)?$~';// [path?query#fragment

    private $url, $scheme, $port, $host, $user, $pass, $path, $query, $fragment;

    public function __construct($url, $baseUrl = null)
    {
//        if (null === $baseUrl) {
            $this->parseFullUrl($url);
//        } else {
//            $this->parseBaseAndRelativeUrl($baseUrl, $url);
//        }
    }

    private function parseFullUrl($url)
    {
        $this->url = $url;

        $matches = [];
        if (!preg_match(self::PATTERN_FULL_URL, $url, $matches)) {
            throw new UrlInvalidException;
        }

        $this->scheme = $matches['scheme'];
        $this->host = $matches['host'];
        $this->port = isset($matches['port']) ? (int)$matches['port'] : self::PORT_DEFAULT;

        $this->user = isset($matches['user']) ? $matches['user'] : null;
        $this->pass = isset($matches['pass']) ? $matches['pass'] : null;
    }

    private function parseBaseAndRelativeUrl($url)
    {
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getUser()
    {
        if (null === $this->user) {
            throw new UrlPartNotDefinedException;
        }
        return $this->user;
    }

    public function getPass()
    {
        if (null === $this->pass) {
            throw new UrlPartNotDefinedException;
        }
        return $this->pass;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function __toString()
    {
        return $this->url;
    }
}
