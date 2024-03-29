# BrokerAPI

[//]: # "[![Latest Version on Packagist][ico-version]][link-packagist]"

[![Software License][ico-license]](./LICENSE.md)

[//]: # "[![Build Status][ico-travis]][link-travis]"

[//]: # "[![Coverage Status][ico-scrutinizer]][link-scrutinizer]"

[//]: # "[![Quality Score][ico-code-quality]][link-code-quality]"

[//]: # "[![Total Downloads][ico-downloads]][link-downloads]"


BrokerAPI is a wrapper for Message-driven API's built on top of most used industry plugins such as [PHP AMQP lib](https://packagist.org/packages/php-amqplib/php-amqplib) for RabbitMQ.
It is built for usage altogether with [AsyncAPI specs and generators](https://github.com/asyncapi/generator)

## Structure

The structure for this plugin is as follows

```
filters/
partials/
template/
utilities/
```

## Requirements
You need to have the following versions of npm+node installed:

```
v12.16+ < Node.js < 15
v6.13.7+ < npm < 7
```

## Install

You need to have the asyncapi/generator installed and a valid AsyncAPI specification file.

``` bash
$ npm install -g @asyncapi/generator
```

## Usage
Refer to the /examples folder for further examples

``` bash
./utilities/generate.sh -o {output} -s {sourceYamlFile}
```

## Change log

Please see [CHANGELOG](./CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](./CONTRIBUTING.md) and [CODE_OF_CONDUCT](./CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email ezublena@gmail.com instead of using the issue tracker.

## Credits

- [Emiliano Zublena][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](./LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/GA/BrokerAPI.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/GA/BrokerAPI/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/GA/BrokerAPI.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/GA/BrokerAPI.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/GA/BrokerAPI.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/GA/BrokerAPI
[link-travis]: https://travis-ci.org/GA/BrokerAPI
[link-scrutinizer]: https://scrutinizer-ci.com/g/GA/BrokerAPI/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/GA/BrokerAPI
[link-downloads]: https://packagist.org/packages/GA/BrokerAPI
[link-author]: https://github.com/emilianozublena
[link-contributors]: https://github.com/asyncapi/php-template/graphs/contributors
