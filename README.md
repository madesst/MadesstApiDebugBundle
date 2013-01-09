# MadesstApiDebugBundle

## О бандле

MadesstApiDebugBundle позволяет вам использовать веб дебаг панель для запросов к вашему REST API. Когда вы разрабатываете большое и сложное API
вам скорее всего потребуется информация из веб дебага (логи, количество запросов к базе и т.д.), но стандартными средствами ее получить нельзя.
Бандл оборачивает такие запросы и выводит их в специальном лэйауте, добавляя кроме веб дебаг панели еще и визуализацию ответа API (средствами JS).
Для визуализации используется сторонний JS код: https://github.com/padolsey/prettyPrint.js

Бандл еще очень сырой, поэтому возможны отклонения в его поведении, прошу информировать меня о таких случаях

## Скриншот

<img src="https://raw.github.com/madesst/MadesstApiDebugBundle/master/Resources/doc/img.png" />

## Установка

Добавьте бандл в ваш `composer.json`:

    {
        "require": {
            "madesst/api-debug-bundle": "dev-master"
        }
    }

И зарегистрируйте бандл в `app/AppKernel.php`:

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Madesst\ApiDebugBundle\MadesstApiDebugBundle(),
        );
    }

Чтобы включить бандл, необходимо указать в конфиге:

    // app/config/config_dev.yml
    madesst_api_debug:
        enabled: true

## Подробности использования

Подразумевается, что в вашем routing.yml для урлов API установлен _format в json или xml (любое значение, включая ~, кроме html),
бандл будет перехватывать запросы к таким урлам и оборачивать их вывод.
Если _format не указан вообще или равен html, то бандл будет игнорировать запрос.

Простой пример:

    /// src/Madesst/ApiTestBundle/Resources/config/routing.yml
    madesst_api_test_json:
        pattern:  /json
        defaults: { _controller: MadesstApiTestBundle:Default:json, _format: ~ }

    madesst_api_test_html:
        pattern:  /html
        defaults: { _controller: MadesstApiTestBundle:Default:html }



    // src/Madesst/ApiTestBundle/Controller/DefaultController.php
    class DefaultController extends Controller
    {
        public function jsonAction()
        {
            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->setContent(json_encode(array('name' => '123')));
            return $response;
        }

        public function htmlAction()
        {
            return $this->render('MadesstApiTestBundle:Default:index.html.twig', array('name' => 456));
        }
    }

Т.к. в первом урле используется установка _format в null (что приведет к установке Response Content-Type согласно Request Accept), то
обращение к первому урлу вызовет работу бандла и обернет json вывод в лэйаут, обращение ко второму пройдет незаметно для бандла.

Если требуется обойти поведение бандла и провести обычный форкфлоу, вы можете использовать GET параметер _ignore_debug=true

При возникновении трудностей, вы можете взглянуть на использование бандла в рамках этого проекта (простой пример REST API): https://github.com/madesst/symfony2rest

## License

Released under the MIT License, see LICENSE.
