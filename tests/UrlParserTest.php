<?php
use UrlParser\UrlParser;

class UrlParserTest extends PHPUnit_Framework_TestCase
{
    public function providerFullUrl()
    {
        return [
            'basic' => [
                'http://ya.ru',
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
            'basic with slash' => [
                'http://ya.ru/',
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
                'http://ya.ru:55/',
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
                'https://ya.ru:55/',
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
                'http://a:b@ya.ru/',
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
                'http://fff.ya.ru/',
                'http',
                'fff.ya.ru',
                80,
                null,
                null,
                '/',
                '',
                ''
            ],
            'path' => [
                'http://ya.ru/fff',
                'http://ya.ru/fff',
                'http',
                'ya.ru',
                80,
                null,
                null,
                '/fff',
                '',
                ''
            ],
            'path+query' => [
                'http://ya.ru/fff?aa=bb',
                'http://ya.ru/fff?aa=bb',
                'http',
                'ya.ru',
                80,
                null,
                null,
                '/fff',
                'aa=bb',
                ''
            ],
            'query without path' => [
                'http://ya.ru?aa=bb',
                'http://ya.ru/?aa=bb',
                'http',
                'ya.ru',
                80,
                null,
                null,
                '/',
                'aa=bb',
                ''
            ],
            'fragment' => [
                'http://ya.ru/#fff',
                'http://ya.ru/#fff',
                'http',
                'ya.ru',
                80,
                null,
                null,
                '/',
                '',
                'fff'
            ],
            'fragment without path' => [
                'http://ya.ru#fff',
                'http://ya.ru/#fff',
                'http',
                'ya.ru',
                80,
                null,
                null,
                '/',
                '',
                'fff'
            ],
            'path+query+fragment' => [
                'http://ya.ru/aaa/bbb?c=a#fff',
                'http://ya.ru/aaa/bbb?c=a#fff',
                'http',
                'ya.ru',
                80,
                null,
                null,
                '/aaa/bbb',
                'c=a',
                'fff'
            ],
            'everything possible together' => [
                'ftp://u:p@aaa.ya.ru:444/aaa/bbb?c=a#fff',
                'ftp://u:p@aaa.ya.ru:444/aaa/bbb?c=a#fff',
                'ftp',
                'aaa.ya.ru',
                444,
                'u',
                'p',
                '/aaa/bbb',
                'c=a',
                'fff'
            ],
            'normalize path simple 1' => [
                'http://ya.ru/./fff',
                'http://ya.ru/fff',
                'http',
                'ya.ru',
                80,
                null,
                null,
                '/fff',
                '',
                ''
            ],
            'normalize path simple 2' => [
                'http://ya.ru/././fff',
                'http://ya.ru/fff',
                'http',
                'ya.ru',
                80,
                null,
                null,
                '/fff',
                '',
                ''
            ],
            'normalize path tricky 1' => [
                'http://ya.ru/fff/../',
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
            'normalize path tricky 2' => [
                'http://ya.ru/aa/fff/../vvv',
                'http://ya.ru/aa/vvv',
                'http',
                'ya.ru',
                80,
                null,
                null,
                '/aa/vvv',
                '',
                ''
            ],
            'normalize path tricky 3' => [
                'http://ya.ru/aa/fff/../../vvv',
                'http://ya.ru/vvv',
                'http',
                'ya.ru',
                80,
                null,
                null,
                '/vvv',
                '',
                ''
            ],
            'normalize path tricky 4' => [
                'http://ya.ru/aa/fff/..',
                'http://ya.ru/aa',
                'http',
                'ya.ru',
                80,
                null,
                null,
                '/aa',
                '',
                ''
            ],
            'normalize path tricky 5' => [
                'http://ya.ru/../fff',
                'http://ya.ru/fff',
                'http',
                'ya.ru',
                80,
                null,
                null,
                '/fff',
                '',
                ''
            ],
            'normalize path tricky 6' => [
                'http://ya.ru/..',
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
        ];
    }

    /**
     * @dataProvider providerFullUrl
     */
    public function testFullUrl(
        $url,
        $toString,
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

        $this->assertSame($toString, (string)$parser, '__toString mismatch');
        $this->assertEquals($toString, $parser, '__toString mismatch');

        $this->assertSame($scheme, $parser->getScheme(), 'scheme mismatch');
        $this->assertSame($host, $parser->getHost(), 'host mismatch');
        $this->assertSame($port, $parser->getPort(), 'port mismatch');

        if (null !== $user) {
            $this->assertSame($user, $parser->getUser(), 'user mismatch');
        }

        if (null !== $pass) {
            $this->assertSame($pass, $parser->getPass(), 'pass mismatch');
        }

        $this->assertSame($path, $parser->getPath(), 'path mismatch');
        $this->assertSame($query, $parser->getQuery(), 'query mismatch');
        $this->assertSame($fragment, $parser->getFragment(), 'fragment mismatch');
    }
}
