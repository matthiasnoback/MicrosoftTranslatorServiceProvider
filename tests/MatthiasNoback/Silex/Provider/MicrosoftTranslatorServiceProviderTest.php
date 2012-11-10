<?php

namespace MatthiasNoback\Tests\Silex\Provider;

use Silex\Application;
use MatthiasNoback\Silex\Provider\MicrosoftTranslatorServiceProvider;

class MicrosoftTranslatorServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRequiresClientId()
    {
        $app = new Application();
        $app->register(new MicrosoftTranslatorServiceProvider);

        $this->setExpectedException('\InvalidArgumentException');
        $app['microsoft_translator'];
    }

    public function testRequiresClientSecret()
    {
        $app = new Application();
        $app->register(new MicrosoftTranslatorServiceProvider, array('microsoft_oauth.client_id' => 'id'));

        $this->setExpectedException('\InvalidArgumentException');
        $app['microsoft_translator'];
    }

    public function testDefinesTranslatorAndBrowserWithCachedFileGetContentsClientUsingArrayCache()
    {
        $app = new Application();
        $app->register(new MicrosoftTranslatorServiceProvider(), array(
            'microsoft_oauth.client_id' => 'id',
            'microsoft_oauth.client_secret' => 'id',
        ));

        $this->assertInstanceOf('Doctrine\\Common\\Cache\\ArrayCache', $app['microsoft_translator.browser.client.cache']);

        $this->assertInstanceOf('Buzz\\Client\\FileGetContents', $app['microsoft_translator.browser.real_client']);

        $this->assertInstanceOf('MatthiasNoback\\Buzz\\Client\\CachedClient', $app['microsoft_translator.browser.client']);

        $this->assertSame(0, $app['microsoft_translator.browser.client.cache.lifetime']);

        $this->assertSame(array('Authorization'), $app['microsoft_translator.browser.client.ignore_headers']);

        $this->assertInstanceOf('Buzz\\Browser', $app['microsoft_translator.browser']);

        $this->assertInstanceOf('MatthiasNoback\\MicrosoftTranslator\\MicrosoftTranslator', $app['microsoft_translator']);
    }

    public function testDefinesAccessTokenProviderWithAccessTokenCacheUsingArrayCache()
    {
        $app = new Application();
        $app->register(new MicrosoftTranslatorServiceProvider(), array(
            'microsoft_oauth.client_id' => 'id',
            'microsoft_oauth.client_secret' => 'id',
        ));

        $this->assertInstanceOf('Buzz\\Client\\FileGetContents', $app['microsoft_oauth.access_token_provider.browser.client']);

        $this->assertInstanceOf('Doctrine\\Common\\Cache\\ArrayCache', $app['microsoft_oauth.access_token_cache.cache']);

        $this->assertInstanceOf('MatthiasNoback\\MicrosoftOAuth\\AccessTokenCache', $app['microsoft_oauth.access_token_cache']);

        $this->assertInstanceOf('Buzz\\Browser', $app['microsoft_oauth.access_token_provider.browser']);

        $this->assertInstanceOf('MatthiasNoback\\MicrosoftOAuth\\AccessTokenProvider', $app['microsoft_oauth.access_token_provider']);
    }
}
