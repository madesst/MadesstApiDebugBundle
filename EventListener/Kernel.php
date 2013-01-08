<?php
/**
 * Created by JetBrains PhpStorm.
 * User: madesst
 * Date: 08.01.13
 * Time: 17:18
 * To change this template use File | Settings | File Templates.
 */

namespace Madesst\ApiDebugBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Templating\EngineInterface;

class Kernel
{
	protected $default_format = 'json';
	protected $valid_accept_header = false;

	/**
	 * Templating engine
	 *
	 * @var EngineInterface
	 */
	protected $templating;

	public function __construct(EngineInterface $templating)
	{
		$this->templating = $templating;
	}

	public function onKernelResponse(FilterResponseEvent $event)
	{
		if ($this->valid_accept_header) {
			return;
		}

		$response = $event->getResponse();
		$request = $event->getRequest();

		/**
		 * Заменяем заголовки так чтобы приложение считало что только что оно обработало обычный text/html
		 * Это необходимое условие чтобы контент отобразился с html заголовком и чтобы отобразилась вебпанель
		 */
		$request->setRequestFormat('html');
		$request->headers->set('Accept', $request->getMimeType('html'));
		$response->headers->set('Content-Type', $request->getMimeType('html'));

		$result = $this->templating->render('MadesstApiDebugBundle::wrapper.html.twig', array(
			'content' => $response->getContent(),
		));

		$response->setContent($result);
	}

	public function onKernelController(FilterControllerEvent $event)
	{
		if ($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST) {
			return;
		}

		/**
		 * Если пришел запрос с GET параметром _ignore_debug - игнорируем дальнейшую логику
		 * Это может потребоваться в некоторых случаях, поэтому я добавил такой функционал
		 */
		if ($event->getRequest()->get('_ignore_debug')) {
			$this->valid_accept_header = true;
			return;
		}

		/**
		 * Если запрос имеет html формат - значит это скорее всего не API функционал и нам не нужно инъектить логику
		 */
		if ($event->getRequest()->getRequestFormat() == 'html') {
			$this->valid_accept_header = true;
			return;
		}

		/**
		 * Если запрос к API имеет Accept заголовок отличный от json и xml - значит метод к API открывают через браузер
		 */
		foreach ($event->getRequest()->getAcceptableContentTypes() as $content_type) {
			if ($event->getRequest()->getMimeType('json') == $content_type || $event->getRequest()->getMimeType('xml') == $content_type) {
				$this->valid_accept_header = true;
			}
		}

		/**
		 * Заменяем неправильный Accept заголовок на тот что указан в конфиге в качестве дефолтного
		 * Для того чтобы дальнейшая логика приложения отработала так будто запрос имел правильный Accept заголовок
		 */
		if (!$this->valid_accept_header) {
			$event->getRequest()->headers->set('Accept', $event->getRequest()->getMimeType($this->default_format));
		}
	}
}