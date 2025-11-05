### Symfony

- `APP_ENV` - окружение приложения (dev/test/prod);
- `APP_SECRET` - секрет для CSRF;
- `DEFAULT_URI` - базовый URI для генерации ссылок в командах и фонах.

### База данных (Doctrine)

- `DB_HOST` - хост БД;
- `DB_PORT` - порт БД;
- `DB_DATABASE` - имя БД;
- `DB_USER` - имя пользователя БД;
- `DB_PASSWORD` - пароль пользователя БД;
- `DB_SERVER_VERSION` - версия сервера БД.

### Symfony Lock

- `LOCK_DSN` - DSN хранилища блокировок.

### HTMLWEB API - внешний сервис для определения оператора телефона

- `HTMLWEB_API_URL` - URL API - https://htmlweb.ru/json/mnp/phone
  (для разработки поднят мок сервер по адресу http://phone_operator_mock:8000/operatorMock.php);

- `HTMLWEB_API_KEY` - ключ API (если внешний сервис потребуется; для mock - можно пустым).
