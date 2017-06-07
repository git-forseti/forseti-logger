# forseti-logger
padrão de log da forseti

# Usando sentry

Coloque a dependência no seu projeto

```
composer require sentry/sentry
```

Configure o DNS

```php
<?php
putenv('FORSETI_SENTRY_DNS=<DNS_SENTRY>');
putenv('FORSETI_SENTRY_LOGGER_LEVEL='.\Monolog\Logger::ERROR); //nível do erro a ser reportado
```

# Sentry com Symfony Console

Por padrão o symfony console vira o gerenciador de exception, por isso é necessário desabilitar. Abaixo exemplo:

```php
$app = new \Symfony\Component\Console\Application('Portal', '1.0.0');
$app->setCatchExceptions($config["console_exception"]);
```
