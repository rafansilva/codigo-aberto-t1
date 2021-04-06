<?php


namespace Source\Controllers;


use RafaNSilva\Support\Email;
use Source\Core\Controller;
use Source\Models\User;

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

        $user = new User();
        $user->bootstrap(
            $data["first_name"],
            $data["last_name"],
            $data["email"],
            $data["passwd"]
        );

        if (!$user->save()) {
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => $user->fail()->getMessage()
            ]);
            return;
        }

        $_SESSION["user"] = $user->id;
        echo $this->ajaxResponse("redirect", [
            "url" => $this->router->route("app.home")
        ]);
    }

    public function forget(array $data): void
    {
        $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);

        if (!$email){
            echo $this->ajaxResponse("message", [
                "type" => "alert",
                "message" => "Informe o SEU E-MAIL para recuperar a senha"
            ]);
            return;
        }

        $user = (new User())->find("email = :email", "email={$email}")->fetch();
        if (!$user){
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "O E-MAIL informado não é cadastrado"
            ]);
            return;
        }

        $user->forget = md5(uniqid(rand(), true));
        $user->save();

        $_SESSION["forget"] = $user->id;

        $email = new Email();
        $email->bootstrap(
            "Recupere sua senha | ". site("name"),
            "",
            $user->email,
            "{$user->first_name} {$user->last_name}",
        );

        echo json_encode($data);
    }
}