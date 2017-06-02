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
