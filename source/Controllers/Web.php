<?php


namespace Source\Controllers;


use Source\Core\Controller;

class Web extends Controller
{
    public function __construct($router)
    {
        parent::__construct($router);

        if (!empty($_SESSION["user"])) {
            $this->router->redirect("app.home");
        }
    }

    public function login(): void
    {
        $head = $this->seo->optimize(
            "Faça Login Para Continuar | " . site("name"),
            site("desc"),
            $this->router->route("web.login"),
            routerImage("LOGIN")
        )->render();

        echo $this->view->render("theme/login", [
            "head" => $head
        ]);
    }

    public function register(array $data): void
    {
        $head = $this->seo->optimize(
            "Crie Sua Conta | " . site("name"),
            site("desc"),
            $this->router->route("web.register"),
            routerImage("REGISTER")
        )->render();

        $testUser = new \stdClass();
        $testUser->first_name = null;
        $testUser->last_name = null;
        $testUser->email = null;

        echo $this->view->render("theme/register", [
            "head" => $head,
            "user" => $testUser
        ]);
    }

    public function forget(): void
    {
        $head = $this->seo->optimize(
            "Recupere Sua Senha | " . site("name"),
            site("desc"),
            $this->router->route("web.forget"),
            routerImage("FORGET")
        )->render();

        echo $this->view->render("theme/forget", [
            "head" => $head
        ]);
    }

    public function reset(array $data): void
    {
        $head = $this->seo->optimize(
            "Crie Uma Nova Senha | " . site("name"),
            site("desc"),
            $this->router->route("web.reset"),
            routerImage("RESET")
        )->render();

        echo $this->view->render("theme/reset", [
            "head" => $head
        ]);
    }

    public function error(array $data): void
    {
        $error = filter_var($data["errcode"], FILTER_VALIDATE_INT);

        $head = $this->seo->optimize(
            "Ooopss {$error} | " . site("name"),
            site("desc"),
            $this->router->route("web.error", ["errcode", error]),
            routerImage($error),
            false
        )->render();

        echo $this->view->render("theme/error", [
            "head" => $head,
            "error" => $error
        ]);
    }
}