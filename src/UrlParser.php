<?php
namespace UrlParser;

class UrlParser
{
    const PORT_DEFAULT = 80;
    const PATTERN_FULL_URL = '~^'
        . '(?<scheme>.*)://'
        . '(?:(?<user>[^:]+):(?<pass>[^@]+)@)?(?<host>.*?)'
        . '(?::(?<port>\d+))?'
        . '(?<path>/[^\?#]*)?'
        . '(?<query>\?[^#]*)?'
        . '(?<fragment>#.*)?'
        . '$~';

    private $scheme, $port, $host, $user, $pass, $path, $query, $fragment;

    public function __construct($url, $baseUrl = null)
    {
        $this->parseFullUrl($url);
        $this->path = $this->normalizePath($this->path);
    }

    private function parseFullUrl($url)
    {
        $matches = [];
        if (!preg_match(self::PATTERN_FULL_URL, $url, $matches)) {
            throw new UrlInvalidException;
        }

        $this->scheme = $matches['scheme'];
        $this->host = $matches['host'];
        $this->port = isset($matches['port']) && $matches['port'] !== '' ? (int)$matches['port'] : self::PORT_DEFAULT;

        $this->user = isset($matches['user']) && $matches['user'] !== ''  ? $matches['user'] : null;
        $this->pass = isset($matches['pass']) && $matches['pass'] !== ''  ? $matches['pass'] : null;

        $this->path = isset($matches['path']) && $matches['path'] !== ''  ? $matches['path'] : '/';

        $this->query = isset($matches['query']) && $matches['query'] !== ''  ? substr($matches['query'], 1) : '';
        $this->fragment = isset($matches['fragment']) && $matches['fragment'] !== ''  ? substr($matches['fragment'], 1) : '';
    }

    private function normalizePath($path)
    {
        //simple
        $count = 0;
        do {
            $path = str_replace('/./', '/', $path, $count);
        } while ($count > 0);

        //tricky
        $pathPartsResult = [];
        foreach (explode('/', substr($path, 1)) as $pathPart) { // without first slash
            if ($pathPart === '..') {
                array_pop($pathPartsResult);
                continue;
            }
            $pathPartsResult[] = $pathPart;
        }
        $path = '/' . join('/', $pathPartsResult);

        return $path;
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
        $result = $this->scheme . '://';
        if ($this->user || $this->pass) {
            $result .= $this->user . ':' . $this->pass . '@';
        }
        $result .= $this->host;
        if ($this->port !== self::PORT_DEFAULT) {
            $result .= ':' . $this->port;
        }
        $result .= $this->path;
        if ($this->query) {
            $result .= '?' . $this->query;
        }
        if ($this->fragment) {
            $result .= '#' . $this->fragment;
        }
        return $result;
    }
}
