<?php


namespace Source\Controllers;


use Source\Core\Controller;
use Source\Models\Users;

class Auth extends Controller
{
    public function __construct($router)
    {
        parent::__construct($router);
    }

    public function register(array $data): void
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        if (in_array("", $data)) {
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "Preencha todos os campos para continuar!"
            ]);
            return;
        }

        if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)){
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "Por favor informe um e-mail válido para continuar!"
            ]);
            return;
        }

        $checkUserEmail = (new Users())->find("email = :e", "e={$data['email']}")->count();
        if ($checkUserEmail){
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "Já existe um usuário cadastrado com esse e-mail!"
            ]);
            return;
        }


        $user = new Users();
        $user->first_name = $data["first_name"];
        $user->last_name = $data["last_name"];
        $user->email = $data["email"];
        $user->passwd = password_hash($data["passwd"], PASSWORD_DEFAULT);

        $user->save();
        $_SESSION["user"] = $user->id;

        echo $this->ajaxResponse("redirect", [
            "url" => $this->router->route("app.home")
        ]);
        return;
    }
}