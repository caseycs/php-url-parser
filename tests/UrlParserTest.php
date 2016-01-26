<?php
use UrlParser\UrlParser;

class UrlParserTest extends PHPUnit_Framework_TestCase
{
    public function providerFullUrl()
    {
        return [
            'basic' => [
                'http://ya.ru',
                'http',
                'ya.ru',
                80,
                null,
                null,
                '/',
                '',
                ''
            ],
            'basic with slash' => [
                'http://ya.ru/',
                'http',
                'ya.ru',
                80,
                null,
                null,
                '/',
                '',
                ''
            ],
            'custom port' => [
                'http://ya.ru:55',
                'http',
                'ya.ru',
                55,
                null,
                null,
                '/',
                '',
                ''
            ],
            'custom scheme' => [
                'https://ya.ru:55',
                'https',
                'ya.ru',
                55,
                null,
                null,
                '/',
                '',
                ''
            ],
            'auth' => [
                'http://a:b@ya.ru',
                'http',
                'ya.ru',
                80,
                'a',
                'b',
                '/',
                '',
                ''
            ],
            'auth+port+slash+scheme' => [
                'https://a:b@ya.ru:55/',
                'https',
                'ya.ru',
                55,
                'a',
                'b',
                '/',
                '',
                ''
            ],
            'subdomain' => [
                'http://fff.ya.ru',
                'http',
                'fff.ya.ru',
                80,
                null,
                null,
                '/',
                '',
                ''
            ],
        ];
    }

    /**
     * @dataProvider providerFullUrl
     */
    public function testFullUrl(
        $url,
        $scheme,
        $host,
        $port,
        $user,
        $pass,
        $path,
        $query,
        $fragment
    ) {
        $parser = new UrlParser($url);

        $this->assertSame($url, (string)$parser);
        $this->assertEquals($url, $parser);

        $this->assertSame($scheme, $parser->getScheme());
        $this->assertSame($host, $parser->getHost());

        if (null !== $port) {
            $this->assertSame($port, $parser->getPort());
        }

        if (null !== $user) {
            $this->assertSame($user, $parser->getUser());
        }

        if (null !== $pass) {
            $this->assertSame($pass, $parser->getPass());
        }
    }
}
