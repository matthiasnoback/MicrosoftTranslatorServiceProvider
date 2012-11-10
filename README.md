# MicrosoftTranslatorServiceProvider

By Matthias Noback

## Installation

Using Composer, add to ``composer.json``:

    {
        "require": {
            "matthiasnoback/microsoft-translator-service-provider": "dev-master"
        }
    }

Then using the Composer binary:

    php composer.phar install

Register the service provider:

    <?php

    use MatthiasNoback\Silex\Provider\MicrosoftTranslatorServiceProvider;

    $app->register(new MicrosoftTranslatorServiceProvider(), array(
        'microsoft_oauth.client_id' => 'YOUR-CLIENT-ID',
        'microsoft_oauth.client_secret' => 'YOUR-CLIENT-SECRET',
    );

## Usage

This service provider wraps the corresponding [Microsoft Translator V2 API PHP library](https://github.com/matthiasnoback/microsoft-translator)
and adds the translator as the service ``microsoft_translator`` to your application.

You need to register your application at the [Azure DataMarket](https://datamarket.azure.com/developer/applications) and
thereby retrieve a "client id" and a "client secret". Copy these values to the right keys in the options array (see above):

## Making calls

### Translate a string

    // in your controller

    $translatedString = $app['microsoft_translator']->translate('This is a test', 'nl', 'en');

    // $translatedString will be 'Dit is een test', which is Dutch for...

### Detect the language of a string

    $text = 'This is a test';

    $detectedLanguage = $app['microsoft_translator']->detect($text);

    // $detectedLanguage will be 'en'

### Get a spoken version of a string

    $text = 'My name is Matthias';

    $spoken = $app['microsoft_translator']->speak($text, 'en', 'audio/mp3', 'MaxQuality');

    // $spoken will be the raw MP3 data, which you can save for instance as a file

For more examples, see the [README of the PHP library](https://github.com/matthiasnoback/microsoft-translator/blob/master/README.md)
