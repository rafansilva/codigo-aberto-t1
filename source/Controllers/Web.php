<?php


namespace Source\Controllers;


use Source\Core\Controller;
use Source\Models\User;

/**
 * Class Web
 * @package Source\Controllers
 */
class Web extends Controller
{
    /**
     * Web constructor.
     * @param $router
     */
    public function __construct($router)
    {
        parent::__construct($router);

        if (!empty($_SESSION["user"])) {
            $this->router->redirect("app.home");
        }
    }

    /**
     *
     */
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

    /**
     * @param array $data
     */
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

    /**
     *
     */
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

    /**
     * @param array $data
     */
    public function reset(array $data): void
    {
        if (empty($_SESSION["forget"])) {
            flash("info", "Informe seu E-MAIL para recuperar a senha");
            $this->router->redirect("web.forget");
        }

        $errorForget = "Não foi possivel recuperar, tente novamente";
        $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
        $forget = filter_var($data["forget"], FILTER_DEFAULT);

        if (!$email || !$forget) {
            flash("error", $errorForget);
            $this->router->redirect("web.forget");
        }

        $user = (new User())->find("email = :e AND forget = :f", "e={$email}&f={$forget}")->fetch();
        if (!$user) {
            flash("error", $errorForget);
            $this->router->redirect("web.forget");
        }

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

    /**
     * @param array $data
     */
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