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

#### `mail` - Отправка сообщения единственному получателю

```php
/**
 * @param SendEmailDto|array{
 *     messageId: int,
 *     email: string,
 *     snippets?: array,
 *     attachments?: array
 * } $data
 * @return bool
 * @throws Exception
 */
public function mail(SendEmailDto|array $data): bool
// Через DTO объект
$enkod->mail(new SendEmailDto(
    messageId: 123,
    email: 'user@example.com',
    snippets: ['name' => 'John'],
    attachments : [
        [
            'fileName' => 'test.pdf',
            'mimeType' => 'application/pdf',
            'content' => 'JVBERi0xLjUNCiW1tbW1DQoxIDAgb2JqDQo8PC9UeXBlL0NhdGFsb2cvUGFnZXMgMiAwIFIvTGFu...'
        ]
));

// Через массив (legacy поддержка)
$enkod->mail([
    'messageId' => 123,
    'email' => 'user@example.com',
    'snippets' => ['name' => 'John'],
    'attachments' => [
        [
            'fileName' => 'test.pdf',
            'mimeType' => 'application/pdf',
            'content' => 'JVBERi0xLjUNCiW1tbW1DQoxIDAgb2JqDQo8PC9UeXBlL0NhdGFsb2cvUGFnZXMgMiAwIFIvTGFu...'
        ]
]);
```

#### `smtp` - Отправка email-сообщения по API для работы с сервисом как с SMTP

```php
/**
 * @param string $sendingDomain Домен отправки
 * @param SmtpEmailDto|array{
 *     to: string|array,
 *     subject: string,
 *     body?: string,
 *     html?: string,
 *     from?: string,
 *     fromName?: string,
 *     cc?: string|array,
 *     bcc?: string|array,
 *     replyTo?: string,
 *     attachments?: array
 * } $data
 * @return bool
 * @throws Exception
 */
public function smtp(string $sendingDomain, SmtpEmailDto|array $data): bool

// Через DTO объект
use Timurrodya\Enkod\Dto\SmtpEmailDto;
use Timurrodya\Enkod\Dto\AttachmentDto;

$enkod->smtp(
    'example.com',
    new SmtpEmailDto(
        to: 'recipient@example.com',
        subject: 'Test Email',
        body: 'Plain text version',
        html: '<html><body>HTML version</body></html>',
        from: 'sender@example.com',
        fromName: 'Sender Name',
        cc: ['cc@example.com'],
        bcc: ['bcc@example.com'],
        replyTo: 'reply@example.com',
        attachments: [
            new AttachmentDto(
                fileName: 'document.pdf',
                mimeType: 'application/pdf',
                content: 'JVBERi0xLjUNCiW1tbW1DQoxIDAgb2JqDQo8PC9UeXBlL0NhdGFsb2cvUGFnZXMgMiAwIFIvTGFu...'
            )
        ]
    )
);

// Через массив (legacy поддержка)
$enkod->smtp('example.com', [
    'to' => 'recipient@example.com',
    'subject' => 'Test Email',
    'body' => 'Plain text version',
    'html' => '<html><body>HTML version</body></html>',
    'from' => 'sender@example.com',
    'fromName' => 'Sender Name',
    'cc' => ['cc@example.com'],
    'bcc' => ['bcc@example.com'],
    'replyTo' => 'reply@example.com',
    'attachments' => [
        [
            'fileName' => 'document.pdf',
            'mimeType' => 'application/pdf',
            'content' => 'JVBERi0xLjUNCiW1tbW1DQoxIDAgb2JqDQo8PC9UeXBlL0NhdGFsb2cvUGFnZXMgMiAwIFIvTGFu...'
        ]
    ]
]);

// Отправка нескольким получателям
$enkod->smtp('example.com', [
    'to' => ['recipient1@example.com', 'recipient2@example.com'],
    'subject' => 'Test Email',
    'html' => '<html><body>HTML version</body></html>',
]);
```

- [Отправка сообщения нескольким получателям](https://openapi.enkod.io/#tag/Emails/paths/~1v1~1mails~1/post) @method bool mails(int $messageId, object $recipients)
- [Создание шаблона сообщения](https://openapi.enkod.io/#tag/Emails/paths/~1v1~1message~1create~1/post) @method array messageCreate(string $subject, string $fromEmail, string
  $fromName, string $html, string $plainText, bool $isTransaction = false, bool $isActive = false, string $replyToEmail = null, string $replyToName = null, array $tags = [], object
  $utm, object $urlParams)
- [Создание мгновенного, запланированного или черновика сообщения](https://openapi.enkod.io/#tag/Emails/paths/~1v1~1message~1onetime~1/post) @method array messageOnetime(object
  $message, bool $isDraft = false, object $to = null, Carbon $deliveryDate = null)
- [Отправка email-сообщения по API для работы с сервисом как с SMTP](https://openapi.enkod.io/#/emails/paths/~1smtp~1{sendingdomain}~1/post) @method bool smtp(string $sendingDomain, SmtpEmailDto|array $data)
 