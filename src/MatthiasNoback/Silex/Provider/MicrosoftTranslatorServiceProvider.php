<?php

namespace MatthiasNoback\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use MatthiasNoback\MicrosoftTranslator\MicrosoftTranslator;
use Buzz\Browser;
use Buzz\Client\Curl;
use MatthiasNoback\MicrosoftOAuth\AccessTokenProvider;
use MatthiasNoback\Buzz\Client\CachedClient;
use Doctrine\Common\Cache\ArrayCache;
use MatthiasNoback\MicrosoftOAuth\AccessTokenCache;

class MicrosoftTranslatorServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $this->registerMicrosoftOAuth($app);
        $this->registerMicrosoftTranslator($app);
    }

    private function registerMicrosoftOAuth(Application $app)
    {
        $app['microsoft_oauth.access_token_provider.browser.client'] = $app->share(function() {
            return new Curl();
        });

        $app['microsoft_oauth.access_token_cache.cache'] = $app->share(function(Application $app) {
            return new ArrayCache();
        });

        $app['microsoft_oauth.access_token_cache'] = $app->share(function(Application $app) {
            return new AccessTokenCache($app['microsoft_oauth.access_token_cache.cache']);
        });

        $app['microsoft_oauth.access_token_provider.browser'] = $app->share(function(Application $app) {
            return new Browser($app['microsoft_oauth.access_token_provider.browser.client']);
        });

        $app['microsoft_oauth.access_token_provider'] = $app->share(function(Application $app) {
            if (!isset($app['microsoft_oauth.client_id'])) {
                throw new \InvalidArgumentException('Please provide your Microsoft OAuth client ID as option "microsoft_oauth.client_id"');
            }

            if (!isset($app['microsoft_oauth.client_secret'])) {
                throw new \InvalidArgumentException('Please provide your Microsoft OAuth client secret as option "microsoft_oauth.client_secret"');
            }

            $accessTokenProvider = new AccessTokenProvider(
                $app['microsoft_oauth.access_token_provider.browser'],
                $app['microsoft_oauth.client_id'],
                $app['microsoft_oauth.client_secret']
            );

            $accessTokenProvider->setCache($app['microsoft_oauth.access_token_cache']);

            return $accessTokenProvider;
        });
    }

    private function registerMicrosoftTranslator(Application $app)
    {
        $app['microsoft_translator.browser.real_client'] = $app->share(function() {
            return new Curl();
        });

        $app['microsoft_translator.browser.client.cache'] = $app->share(function(Application $app) {
            return new ArrayCache();
        });

        $app['microsoft_translator.browser.client.cache.lifetime'] = 0;
        $app['microsoft_translator.browser.client.ignore_headers'] = array('Authorization');

        $app['microsoft_translator.browser.client'] = $app->share(function(Application $app) {
            return new CachedClient(
                $app['microsoft_translator.browser.real_client'],
                $app['microsoft_translator.browser.client.cache'],
                $app['microsoft_translator.browser.client.cache.lifetime'],
                $app['microsoft_translator.browser.client.ignore_headers']
            );
        });

        $app['microsoft_translator.browser'] = $app->share(function(Application $app) {
            return new Browser($app['microsoft_translator.browser.client']);
        });

        $app['microsoft_translator'] = $app->share(function(Application $app) {
            return new MicrosoftTranslator(
                $app['microsoft_translator.browser'],
                $app['microsoft_oauth.access_token_provider']
            );
        });
    }

    public function boot(Application $app)
    {
    }
}
