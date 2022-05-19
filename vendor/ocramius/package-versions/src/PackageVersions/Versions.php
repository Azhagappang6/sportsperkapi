<?php

declare(strict_types=1);

namespace PackageVersions;

/**
 * This class is generated by ocramius/package-versions, specifically by
 * @see \PackageVersions\Installer
 *
 * This file is overwritten at every run of `composer install` or `composer update`.
 */
final class Versions
{
    public const ROOT_PACKAGE_NAME = '__root__';
    /**
     * Array of all available composer packages.
     * Dont read this array from your calling code, but use the \PackageVersions\Versions::getVersion() method instead.
     *
     * @var array<string, string>
     * @internal
     */
    public const VERSIONS          = array (
  'doctrine/annotations' => 'v1.7.0@fa4c4e861e809d6a1103bd620cce63ed91aedfeb',
  'doctrine/cache' => 'v1.8.0@d768d58baee9a4862ca783840eca1b9add7a7f57',
  'doctrine/collections' => 'v1.6.2@c5e0bc17b1620e97c968ac409acbff28b8b850be',
  'doctrine/common' => 'v2.11.0@b8ca1dcf6b0dc8a2af7a09baac8d0c48345df4ff',
  'doctrine/dbal' => 'v2.9.2@22800bd651c1d8d2a9719e2a3dc46d5108ebfcc9',
  'doctrine/doctrine-bundle' => '1.11.2@28101e20776d8fa20a00b54947fbae2db0d09103',
  'doctrine/doctrine-cache-bundle' => '1.3.5@5514c90d9fb595e1095e6d66ebb98ce9ef049927',
  'doctrine/doctrine-migrations-bundle' => 'v2.0.0@4c9579e0e43df1fb3f0ca29b9c20871c824fac71',
  'doctrine/event-manager' => 'v1.0.0@a520bc093a0170feeb6b14e9d83f3a14452e64b3',
  'doctrine/inflector' => 'v1.3.0@5527a48b7313d15261292c149e55e26eae771b0a',
  'doctrine/instantiator' => '1.2.0@a2c590166b2133a4633738648b6b064edae0814a',
  'doctrine/lexer' => '1.1.0@e17f069ede36f7534b95adec71910ed1b49c74ea',
  'doctrine/migrations' => '2.1.1@a89fa87a192e90179163c1e863a145c13337f442',
  'doctrine/orm' => 'v2.6.4@b52ef5a1002f99ab506a5a2d6dba5a2c236c5f43',
  'doctrine/persistence' => '1.1.1@3da7c9d125591ca83944f477e65ed3d7b4617c48',
  'doctrine/reflection' => 'v1.0.0@02538d3f95e88eb397a5f86274deb2c6175c2ab6',
  'egulias/email-validator' => '2.1.11@92dd169c32f6f55ba570c309d83f5209cefb5e23',
  'friendsofsymfony/oauth-server-bundle' => '1.6.2@fcaa25cc49474bdb0db7894f880976fe76ffed23',
  'friendsofsymfony/oauth2-php' => '1.2.3@a41fef63f81ef2ef632350a6c7dc66d15baa9240',
  'friendsofsymfony/rest-bundle' => '2.5.0@a5fc73b84bdb2f0fdf58a717b322ceb6997f7bf3',
  'friendsofsymfony/user-bundle' => 'v2.1.2@1049935edd24ec305cc6cfde1875372fa9600446',
  'hoa/compiler' => '3.17.08.08@aa09caf0bf28adae6654ca6ee415ee2f522672de',
  'hoa/consistency' => '1.17.05.02@fd7d0adc82410507f332516faf655b6ed22e4c2f',
  'hoa/event' => '1.17.01.13@6c0060dced212ffa3af0e34bb46624f990b29c54',
  'hoa/exception' => '1.17.01.16@091727d46420a3d7468ef0595651488bfc3a458f',
  'hoa/file' => '1.17.07.11@35cb979b779bc54918d2f9a4e02ed6c7a1fa67ca',
  'hoa/iterator' => '2.17.01.10@d1120ba09cb4ccd049c86d10058ab94af245f0cc',
  'hoa/math' => '1.17.05.16@7150785d30f5d565704912116a462e9f5bc83a0c',
  'hoa/protocol' => '1.17.01.14@5c2cf972151c45f373230da170ea015deecf19e2',
  'hoa/regex' => '1.17.01.13@7e263a61b6fb45c1d03d8e5ef77668518abd5bec',
  'hoa/stream' => '1.17.02.21@3293cfffca2de10525df51436adf88a559151d82',
  'hoa/ustring' => '4.17.01.16@e6326e2739178799b1fe3fdd92029f9517fa17a0',
  'hoa/visitor' => '2.17.01.16@c18fe1cbac98ae449e0d56e87469103ba08f224a',
  'hoa/zformat' => '1.17.01.10@522c381a2a075d4b9dbb42eb4592dd09520e4ac2',
  'jdorn/sql-formatter' => 'v1.2.17@64990d96e0959dff8e059dfcdc1af130728d92bc',
  'jms/metadata' => '2.1.0@8d8958103485c2cbdd9a9684c3869312ebdaf73a',
  'jms/serializer' => '3.3.0@4c1e4296734385af7718ca71ec0febb4815b4a87',
  'jms/serializer-bundle' => '3.4.1@d5af7fe83fead9b791dd6b46a936d5e6e42deed4',
  'nelmio/cors-bundle' => '2.0.1@9683e6d30d000ef998919261329d825de7c53499',
  'nikic/php-parser' => 'v4.2.4@97e59c7a16464196a8b9c77c47df68e4a39a45c4',
  'ocramius/package-versions' => '1.5.1@1d32342b8c1eb27353c8887c366147b4c2da673c',
  'ocramius/proxy-manager' => '2.2.3@4d154742e31c35137d5374c998e8f86b54db2e2f',
  'psr/cache' => '1.0.1@d11b50ad223250cf17b86e38383413f5a6764bf8',
  'psr/container' => '1.0.0@b7ce3b176482dbbc1245ebf52b181af44c2cf55f',
  'psr/log' => '1.1.0@6c001f1daafa3a3ac1d8ff69ee4db8e799a654dd',
  'sensio/framework-extra-bundle' => 'v5.4.1@585f4b3a1c54f24d1a8431c729fc8f5acca20c8a',
  'symfony/cache' => 'v4.3.4@1d8f7fee990c586f275cde1a9fc883d6b1e2d43e',
  'symfony/cache-contracts' => 'v1.1.5@ec5524b669744b5f1dc9c66d3c2b091eb7e7f0db',
  'symfony/config' => 'v4.3.4@07d49c0f823e0bc367c6d84e35b61419188a5ece',
  'symfony/console' => 'v4.3.4@de63799239b3881b8a08f8481b22348f77ed7b36',
  'symfony/debug' => 'v4.3.4@afcdea44a2e399c1e4b52246ec8d54c715393ced',
  'symfony/dependency-injection' => 'v4.3.4@d3ad14b66ac773ba6123622eb9b5b010165fe3d9',
  'symfony/doctrine-bridge' => 'v4.3.4@d2967b2b43788bd3a7cddeb8bd4567e142b3821c',
  'symfony/dotenv' => 'v4.3.4@1785b18148a016b8f4e6a612291188d568e1f9cd',
  'symfony/event-dispatcher' => 'v4.3.4@429d0a1451d4c9c4abe1959b2986b88794b9b7d2',
  'symfony/event-dispatcher-contracts' => 'v1.1.5@c61766f4440ca687de1084a5c00b08e167a2575c',
  'symfony/filesystem' => 'v4.3.4@9abbb7ef96a51f4d7e69627bc6f63307994e4263',
  'symfony/finder' => 'v4.3.4@86c1c929f0a4b24812e1eb109262fc3372c8e9f2',
  'symfony/flex' => 'v1.4.6@133e649fdf08aeb8741be1ba955ccbe5cd17c696',
  'symfony/form' => 'v4.3.4@eba11fd575e791d72030cb59215a9948791f1e74',
  'symfony/framework-bundle' => 'v4.3.4@0fd8e354cef6b3da666e585d7ae75aeea2423833',
  'symfony/http-foundation' => 'v4.3.4@d804bea118ff340a12e22a79f9c7e7eb56b35adc',
  'symfony/http-kernel' => 'v4.3.4@5e0fc71be03d52cd00c423061cfd300bd6f92a52',
  'symfony/inflector' => 'v4.3.4@b25a8dc15fada858432efa083c1ecd2cef5991a7',
  'symfony/intl' => 'v4.3.4@8db5505703c5bdb23d524fd994dad2f781966538',
  'symfony/mailer' => 'v4.3.9@a2e19255ce8de3a9c4e5228fde33ca9390af787c',
  'symfony/maker-bundle' => 'v1.13.0@c4388410e2fb6321e77c5dd6e3cb2dba821f9fe6',
  'symfony/mime' => 'v4.3.4@987a05df1c6ac259b34008b932551353f4f408df',
  'symfony/options-resolver' => 'v4.3.4@81c2e120522a42f623233968244baebd6b36cb6a',
  'symfony/orm-pack' => 'v1.0.6@36c2a928482dc5f05c5c1c1b947242ae03ff1335',
  'symfony/polyfill-intl-icu' => 'v1.12.0@66810b9d6eb4af54d543867909d65ab9af654d7e',
  'symfony/polyfill-intl-idn' => 'v1.12.0@6af626ae6fa37d396dc90a399c0ff08e5cfc45b2',
  'symfony/polyfill-mbstring' => 'v1.12.0@b42a2f66e8f1b15ccf25652c3424265923eb4f17',
  'symfony/polyfill-php72' => 'v1.12.0@04ce3335667451138df4307d6a9b61565560199e',
  'symfony/polyfill-php73' => 'v1.12.0@2ceb49eaccb9352bff54d22570276bb75ba4a188',
  'symfony/process' => 'v4.3.4@e89969c00d762349f078db1128506f7f3dcc0d4a',
  'symfony/property-access' => 'v4.3.4@bb0c302375ffeef60c31e72a4539611b7f787565',
  'symfony/routing' => 'v4.3.4@ff1049f6232dc5b6023b1ff1c6de56f82bcd264f',
  'symfony/security-bundle' => 'v4.3.9@8d157b5a96c2d7561e29eca3574344727482d3fb',
  'symfony/security-core' => 'v4.3.4@a8c67a8bc6bd8012c5d6b70cb030ca3422476caa',
  'symfony/security-csrf' => 'v4.3.9@0760ec651ea8ff81e22097780337e43f3a795769',
  'symfony/security-guard' => 'v4.3.9@62cc82a384f2c1c75c58189fcf713032f6fef1e9',
  'symfony/security-http' => 'v4.3.9@75e96df3a1b9b38c67e2fa208894f72dae5e1147',
  'symfony/service-contracts' => 'v1.1.6@ea7263d6b6d5f798b56a45a5b8d686725f2719a3',
  'symfony/stopwatch' => 'v4.3.4@1e4ff456bd625be5032fac9be4294e60442e9b71',
  'symfony/templating' => 'v4.3.4@15407776e1fe250ed3fa1c1a679482c60c2affe3',
  'symfony/translation' => 'v4.3.9@73f86a49454d9477864ccbb6c06993e24a052a48',
  'symfony/translation-contracts' => 'v1.1.7@364518c132c95642e530d9b2d217acbc2ccac3e6',
  'symfony/twig-bridge' => 'v4.3.8@67fdb93de3361bcf1ab02bd8275af8c790bae900',
  'symfony/twig-bundle' => 'v4.3.9@869ebf144acafd19fb9c8c386808c26624f28572',
  'symfony/validator' => 'v4.3.9@539484217f9966aa93e01915c5035c74b6ea1b9b',
  'symfony/var-exporter' => 'v4.3.4@d5b4e2d334c1d80e42876c7d489896cfd37562f2',
  'symfony/web-server-bundle' => 'v4.3.4@dc26b980900ddf3e9feade14e5b21c029e8ca92f',
  'symfony/yaml' => 'v4.3.4@5a0b7c32dc3ec56fd4abae8a4a71b0cf05013686',
  'twig/twig' => 'v2.12.2@d761fd1f1c6b867ae09a7d8119a6d95d06dc44ed',
  'willdurand/jsonp-callback-validator' => 'v1.1.0@1a7d388bb521959e612ef50c5c7b1691b097e909',
  'willdurand/negotiation' => 'v2.3.1@03436ededa67c6e83b9b12defac15384cb399dc9',
  'zendframework/zend-code' => '3.3.2@936fa7ad4d53897ea3e3eb41b5b760828246a20b',
  'zendframework/zend-eventmanager' => '3.2.1@a5e2583a211f73604691586b8406ff7296a946dd',
  'paragonie/random_compat' => '2.*@1df15d4b03e96173a0ad313cb572bd07c44ebb9b',
  'symfony/polyfill-ctype' => '*@1df15d4b03e96173a0ad313cb572bd07c44ebb9b',
  'symfony/polyfill-iconv' => '*@1df15d4b03e96173a0ad313cb572bd07c44ebb9b',
  'symfony/polyfill-php71' => '*@1df15d4b03e96173a0ad313cb572bd07c44ebb9b',
  'symfony/polyfill-php70' => '*@1df15d4b03e96173a0ad313cb572bd07c44ebb9b',
  'symfony/polyfill-php56' => '*@1df15d4b03e96173a0ad313cb572bd07c44ebb9b',
  '__root__' => 'dev-master@1df15d4b03e96173a0ad313cb572bd07c44ebb9b',
);

    private function __construct()
    {
    }

    /**
     * @throws \OutOfBoundsException If a version cannot be located.
     *
     * @psalm-param key-of<self::VERSIONS> $packageName
     */
    public static function getVersion(string $packageName) : string
    {
        if (isset(self::VERSIONS[$packageName])) {
            return self::VERSIONS[$packageName];
        }

        throw new \OutOfBoundsException(
            'Required package "' . $packageName . '" is not installed: check your ./vendor/composer/installed.json and/or ./composer.lock files'
        );
    }
}
