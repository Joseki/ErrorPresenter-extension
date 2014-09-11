<?php


namespace Joseki\Application;

use Nette\Application\InvalidPresenterException;
use Nette\Application\IPresenterFactory;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Http\IRequest;

class ErrorPresenterFactory
{

    /** @var \Nette\Application\IRouter */
    private $router;

    /** @var \Nette\Http\IRequest */
    private $httpRequest;

    /** @var \Nette\Application\IPresenterFactory */
    private $presenterFactory;

    private $defaultErrorPresenter = null;



    function __construct(IPresenterFactory $presenterFactory, IRouter $router, IRequest $httpRequest)
    {
        $this->router = $router;
        $this->httpRequest = $httpRequest;
        $this->presenterFactory = $presenterFactory;
    }



    /**
     * @return null|string
     */
    public function getErrorPresenter()
    {
        $request = $this->router->match($this->httpRequest);

        if (!$request instanceof Request) {
            return $this->defaultErrorPresenter;
        }

        $errorPresenter = $this->defaultErrorPresenter;
        $name = $request->getPresenterName();
        $modules = explode(":", $name);
        unset($modules[count($modules) - 1]);
        while (count($modules) != 0) {
            $catched = false;
            try {
                $errorPresenter = implode(":", $modules) . ':Error';
                $this->presenterFactory->getPresenterClass($errorPresenter);
                break;
            } catch (InvalidPresenterException $e) {
                $catched = true;
            }
            unset($modules[count($modules) - 1]);
        }
        if (isset($catched) && $catched) {
            return $this->defaultErrorPresenter;
        }

        return $errorPresenter;
    }



    /**
     * @param null $defaultErrorPresenter
     */
    public function setDefaultErrorPresenter($defaultErrorPresenter)
    {
        $this->defaultErrorPresenter = $defaultErrorPresenter;
    }
}
