# MadesstApiDebugBundle

## About

MadesstApiDebugBundle позволяет вам использовать веб дебаг панель для запросов к вашему REST API. Когда вы разрабатываете большое и сложное API
вам скорее всего потребуется информация из веб дебага (логи, количество запросов к базе и т.д.), но стандартными средствами ее получить нельзя.
Бандл оборачивает такие запросы и выводит их в специальном лэйауте, добавляя кроме веб дебаг панели еще и визуализацию ответа API (средствами JS).

## Screenshot

<img src="https://raw.github.com/madesst/MadesstApiDebugBundle/master/Resources/doc/img.png" />

## Configuration

Чтобы включить бандл, необходимо указать в конфиге:

    //app/config/config_dev.yml
    madesst_api_debug:
        enabled: true

## License

Released under the MIT License, see LICENSE.
