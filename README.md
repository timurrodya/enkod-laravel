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

#### `messageCreate` - Создание шаблона сообщения для отправки по API

Метод создает шаблон email-сообщения в системе Enkod. Созданный шаблон можно использовать для отправки через методы `mail()` или `mails()`. 

**Важно:** Этот метод создает шаблон сообщения, но не отправляет его. Для отправки используйте методы `mail()` или `mails()` с полученным `messageId`.

```php
/**
 * @param MessageCreateDto|array{
 *     subject: string,
 *     fromEmail: string,
 *     fromName: string,
 *     html: string,
 *     plainText: string,
 *     isTransaction?: bool,
 *     isActive?: bool,
 *     replyToEmail?: string,
 *     replyToName?: string,
 *     tags?: array,
 *     utm?: object,
 *     urlParams?: object
 * } $data
 * @return array|string Возвращает массив с данными созданного сообщения (включая messageId) или строку с ошибкой
 * @throws Exception
 */
public function messageCreate(MessageCreateDto|array $data): array|string
```

**Параметры:**

- `subject` (string, обязательный) - Тема письма. Не может быть пустой.
- `fromEmail` (string, обязательный) - Email адрес отправителя. Должен быть валидным email.
- `fromName` (string, обязательный) - Имя отправителя. Не может быть пустым.
- `html` (string, обязательный*) - HTML версия письма. Обязателен, если не указан `plainText`.
- `plainText` (string, обязательный*) - Текстовая версия письма. Обязателен, если не указан `html`.
- `isTransaction` (bool, опциональный, по умолчанию `false`) - Флаг транзакционного письма.
- `isActive` (bool, опциональный, по умолчанию `false`) - Флаг активности шаблона.
- `replyToEmail` (string, опциональный) - Email для ответа. Должен быть валидным email, если указан.
- `replyToName` (string, опциональный) - Имя для ответа.
- `tags` (array, опциональный, по умолчанию `[]`) - Массив тегов для категоризации сообщения.
- `utm` (object, опциональный, по умолчанию `{}`) - Объект с UTM-метками для отслеживания.
- `urlParams` (object, опциональный, по умолчанию `{}`) - Объект с дополнительными параметрами URL.

**Валидация:**

- `subject` не может быть пустой строкой
- `fromEmail` должен быть валидным email адресом
- `fromName` не может быть пустой строкой
- Хотя бы один из `html` или `plainText` должен быть указан (не пустой)
- `replyToEmail` должен быть валидным email, если указан
- `tags` должен быть массивом
- `utm` должен быть объектом
- `urlParams` должен быть объектом

**Примеры использования:**

```php
// Через DTO объект
use Timurrodya\Enkod\Dto\MessageCreateDto;

$result = $enkod->messageCreate(new MessageCreateDto(
    subject: 'Добро пожаловать!',
    fromEmail: 'noreply@example.com',
    fromName: 'Команда Example',
    html: '<html><body><h1>Добро пожаловать!</h1><p>Спасибо за регистрацию.</p></body></html>',
    plainText: 'Добро пожаловать! Спасибо за регистрацию.',
    isTransaction: false,
    isActive: true,
    replyToEmail: 'support@example.com',
    replyToName: 'Служба поддержки',
    tags: ['welcome', 'registration'],
    utm: (object)['source' => 'website', 'medium' => 'email'],
    urlParams: (object)['ref' => 'api']
));

// messageId можно получить из результата
$messageId = $result['id'] ?? $result['messageId'] ?? null;

// Теперь можно отправить сообщение используя созданный шаблон
if ($messageId) {
    $enkod->mail([
        'messageId' => $messageId,
        'email' => 'user@example.com',
        'snippets' => ['name' => 'Иван']
    ]);
}
```

```php
// Через массив (legacy поддержка)
$result = $enkod->messageCreate([
    'subject' => 'Добро пожаловать!',
    'fromEmail' => 'noreply@example.com',
    'fromName' => 'Команда Example',
    'html' => '<html><body><h1>Добро пожаловать!</h1><p>Спасибо за регистрацию.</p></body></html>',
    'plainText' => 'Добро пожаловать! Спасибо за регистрацию.',
    'isTransaction' => false,
    'isActive' => true,
    'replyToEmail' => 'support@example.com',
    'replyToName' => 'Служба поддержки',
    'tags' => ['welcome', 'registration'],
    'utm' => [
        'source' => 'website',
        'medium' => 'email'
    ],
    'urlParams' => [
        'ref' => 'api'
    ]
]);
```

```php
// Минимальный пример (только обязательные поля)
$result = $enkod->messageCreate([
    'subject' => 'Тестовое письмо',
    'fromEmail' => 'sender@example.com',
    'fromName' => 'Отправитель',
    'html' => '<p>Привет!</p>',
    'plainText' => 'Привет!'
]);
```

**Возвращаемое значение:**

Метод возвращает массив с данными созданного сообщения. Обычно содержит:
- `id` или `messageId` - идентификатор созданного шаблона (используйте его для отправки)
- Другие данные о созданном сообщении согласно API Enkod

**Использование созданного шаблона:**

После создания шаблона вы получите `messageId`, который можно использовать для отправки:

```php
// Создаем шаблон
$result = $enkod->messageCreate([...]);
$messageId = $result['id'] ?? $result['messageId'];

// Отправляем одному получателю
$enkod->mail([
    'messageId' => $messageId,
    'email' => 'user@example.com',
    'snippets' => ['name' => 'Иван']
]);

// Или отправляем нескольким получателям
$enkod->mails($messageId, (object)[
    'email' => 'user1@example.com',
    'snippets' => ['name' => 'Иван']
]);
```

- [Отправка сообщения нескольким получателям](https://openapi.enkod.io/#tag/Emails/paths/~1v1~1mails~1/post) @method bool mails(int $messageId, object $recipients)
- [Создание шаблона сообщения](https://openapi.enkod.io/#tag/Emails/paths/~1v1~1message~1create~1/post) @method array|string messageCreate(MessageCreateDto|array $data)
- [Создание мгновенного, запланированного или черновика сообщения](https://openapi.enkod.io/#tag/Emails/paths/~1v1~1message~1onetime~1/post) @method array messageOnetime(object
  $message, bool $isDraft = false, object $to = null, Carbon $deliveryDate = null)
- [Отправка email-сообщения по API для работы с сервисом как с SMTP](https://openapi.enkod.io/#/emails/paths/~1smtp~1{sendingdomain}~1/post) @method bool smtp(string $sendingDomain, SmtpEmailDto|array $data)
 