<?php


namespace Source\Core;


use CoffeeCode\Optimizer\Optimizer;
use CoffeeCode\Router\Router;
use League\Plates\Engine;

/**
 * Class Controller
 * @package Source\Core
 */
abstract class Controller
{
    /** @var Engine */
    protected $view;

    /** @var Router */
    protected $router;

    /** @var Optimizer */
    protected $seo;

    /**
     * Controller constructor.
     * @param $router
     */
    public function __construct($router)
    {
        $this->router = $router;
        $this->view = Engine::create(
            dirname(__DIR__, 2) . "/views",
            "php"
        );
        $this->view->addData(["router" => $this->router]);

        $this->seo = new Optimizer();
        $this->seo->openGraph(site("name"), site("locale"), "article")
            ->publisher(SOCIAL["facebook_page"], SOCIAL["facebook_author"])
            ->facebook(SOCIAL["facebook_appId"])
            ->twitterCard(SOCIAL["twitter_creator"], SOCIAL["twitter_site"], site("domain"));
    }

    /**
     * @param string $param
     * @param array $values
     * @return string
     */
    public function ajaxResponse(string $param, array $values): string
    {
        return json_encode([$param => $values]);
    }
}