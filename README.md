# Enkod

## Требования

- Версии PHP: ^8.1
- Версии Guzzle": ^7.2

## Установка

Вы можете установить пакет через composer:

```shell script
composer require timurrodya/enkod-laravel
```

Публикация конфигурационного файла. Выполните `artisan` команду

```shell script
php artisan vendor:publish --provider='Timurrodya\Enkod\EnkodServiceProvider' --tag=config
```

Настройка проекта осществляется через `.env` вашего проекта. Вам необходимо указать три параметра

- `ENKOD_API_KEY` - apiKey для раоты с API Enkod
- `ENKOD_BASE_URL` - адрес Api Enkod по умолчанию https://api.enkod.ru/
- `ENKOD_VERSION` - версия api, по умолчанию v1

## Методы

- [Отправка сообщения единственному получателю](https://openapi.enkod.io/#tag/Emails/paths/~1v1~1mail~1/post) @method bool mail(int $messageId, string $email, array $snippets = [],
  array $attachments = [])
- [Отправка сообщения нескольким получателям](https://openapi.enkod.io/#tag/Emails/paths/~1v1~1mails~1/post) @method bool mails(int $messageId, object $recipients)
- [Создание шаблона сообщения](https://openapi.enkod.io/#tag/Emails/paths/~1v1~1message~1create~1/post) @method array messageCreate(string $subject, string $fromEmail, string
  $fromName, string $html, string $plainText, bool $isTransaction = false, bool $isActive = false, string $replyToEmail = null, string $replyToName = null, array $tags = [], object
  $utm, object $urlParams)
- [Создание мгновенного, запланированного или черновика сообщения](https://openapi.enkod.io/#tag/Emails/paths/~1v1~1message~1onetime~1/post) @method array messageOnetime(object
  $message, bool $isDraft = false, object $to = null, Carbon $deliveryDate = null)
 