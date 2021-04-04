<?php


namespace Source\Controllers;


use Source\Core\Controller;
use Source\Models\User;

class App extends Controller
{
    /** @var User */
    protected $user;

    public function __construct($router)
    {
        parent::__construct($router);

        //RESTRIÇÃO DE ACESSO
        if (empty($_SESSION["user"]) || !$this->user = (new User())->findById($_SESSION["user"])) {
            unset($_SESSION["user"]);

            flash("error", "Acesso negado. Favor logue-se");
            $this->router->redirect("web.login");
        }

    }

    public function home(): void
    {
//        $head = $this->seo->optimize(
//            "Dashboard Home | " . site("name"),
//            site("desc"),
//            $this->router->route("web.home"),
//            routerImage("HOME")
//        )->render();
//
//        echo $this->view->render("theme/dashboard", [
//            "head" => $head,
//            "user" => ""
//        ]);

        var_dump($this->user);
    }

    public function logoff(): void
    {
        unset($_SESSION["user"]);

        flash("info", "Você saiu com sucesso, volte logo {$this->user->first_name}");
        $this->router->redirect("web.login");
    }
}