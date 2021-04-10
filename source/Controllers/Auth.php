<?php


namespace Source\Controllers;


use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\GoogleUser;
use RafaNSilva\Support\Email;
use Source\Core\Controller;
use Source\Models\User;

/**
 * Class Auth
 * @package Source\Controllers
 */
class Auth extends Controller
{
    /**
     * Auth constructor.
     * @param $router
     */
    public function __construct($router)
    {
        parent::__construct($router);
    }

    /**
     * @param array $data
     */
    public function login(array $data): void
    {
        $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
        $passwd = filter_var($data["passwd"], FILTER_DEFAULT);

        if (!$email || !$passwd) {
            echo $this->ajaxResponse("message", [
                "type" => "alert",
                "message" => "Informe seu e-mail e senha para logar"
            ]);
            return;
        }

        $user = (new User())->find("email = :e", "e={$email}")->fetch();
        if (!$user || !password_verify($passwd, $user->passwd)) {
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "E-mail ou senha informados não conferem"
            ]);
            return;
        }

        /** SOCIAL VALIDATE */
        $this->socialValidate($user);

        $_SESSION["user"] = $user->id;
        echo $this->ajaxResponse("redirect", [
            "url" => $this->router->route("app.home")
        ]);
    }

    /**
     * @param array $data
     */
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

        /** SOCIAL VALIDATE */
        $this->socialValidate($user);

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

    /**
     * @param array $data
     * @throws \Exception
     */
    public function forget(array $data): void
    {
        $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);

        if (!$email) {
            echo $this->ajaxResponse("message", [
                "type" => "alert",
                "message" => "Informe o SEU E-MAIL para recuperar a senha"
            ]);
            return;
        }

        $user = (new User())->find("email = :email", "email={$email}")->fetch();
        if (!$user) {
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
            "Recupere sua senha | " . site("name"),
            $this->view->render("emails/recover", [
                "user" => $user,
                "link" => $this->router->route("web.reset", [
                    "email" => $user->email,
                    "forget" => $user->forget
                ])
            ]),
            $user->email,
            "{$user->first_name} {$user->last_name}",
        )->send();

        flash("success", "Enviamos um link de confirmação para seu e-mail");

        echo $this->ajaxResponse("redirect", [
            "url" => $this->router->route("web.forget")
        ]);
    }

    /**
     * @param array $data
     */
    public function reset(array $data): void
    {
        if (empty($_SESSION["forget"]) || !$user = (new User())->findById($_SESSION["forget"])) {
            flash("error", "Não foi possivel recuperar, tente novamente");
            echo $this->ajaxResponse("redirect", [
                "url" => $this->router->route("web.forget")
            ]);
            return;
        }

        if (empty($data["password"]) || empty($data["password_re"])) {
            echo $this->ajaxResponse("message", [
                "type" => "alert",
                "message" => "Informe e repita sua nova senha"
            ]);
            return;
        }

        if ($data["password"] != $data["password_re"]) {
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => "Você informou duas senhas diferentes"
            ]);
            return;
        }

        $user->passwd = $data["password"];
        $user->forget = null;

        if (!$user->save()) {
            echo $this->ajaxResponse("message", [
                "type" => "error",
                "message" => $user->fail()->getMessage()
            ]);
            return;
        }

        unset($_SESSION["forget"]);

        flash("success", "Sua senha foi atualizada com sucesso");
        echo $this->ajaxResponse("redirect", [
            "url" => $this->router->route("web.login")
        ]);
    }

    public function facebook(): void
    {
        $facebook = new Facebook(FACEBOOK_LOGIN);
        $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRIPPED);
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRIPPED);

        if (!$error && !$code) {
            $auth_url = $facebook->getAuthorizationUrl(["scope" => "email"]);
            header("Location: {$auth_url}");
            return;
        }

        if ($error) {
            flash("error", "Não foi possível logar com o Facebook");
            $this->router->route("web.login");
        }

        if ($code && empty($_SESSION["facebook_auth"])) {
            try {
                $token = $facebook->getAccessToken("authorization_code", ["code" => $code]);
                $_SESSION["facebook_auth"] = serialize($facebook->getResourceOwner($token));
            } catch (\Exception $exception) {
                flash("error", "Não foi possível logar com o Facebook");
                $this->router->route("web.login");
            }
        }

        /** @var $facebookUser FacebookUser */
        $facebookUser = unserialize($_SESSION["facebook_auth"]);
        $userById = (new User())->find("facebook_id = :id", "id={$facebookUser->getId()}")->fetch();

        //LOGIN BY ID
        if ($userById) {
            unset($_SESSION["facebook_auth"]);

            $_SESSION["user"] = $userById->id;
            $this->router->redirect("app.home");
        }

        //LOGIN BY E-MAIL
        $userByEmail = (new User())->find("email = :e", "e={$facebookUser->getEmail()}")->fetch();
        if ($userByEmail) {
            flash("info", "Olá {$facebookUser->getFirstName()}, faça login para conectar seu Facebook");
            $this->router->redirect("web.login");
        }

        //REGISTER IF NOT
        $link = $this->router->route("web.login");
        flash(
            "info",
            "Olá {$facebookUser->getFirstName()}, <b>se já tem uma conta clique <a title='Fazer Login' href='{$link}'>FAZER LOGIN</a></b>, ou complete seu cadastro"
        );
        $this->router->redirect("web.register");
    }

    public function google(): void
    {
        $google = new Google(GOOGLE_LOGIN);
        $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRIPPED);
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRIPPED);

        if (!$error && !$code) {
            $auth_url = $google->getAuthorizationUrl();
            header("Location: {$auth_url}");
            return;
        }

        if ($error) {
            flash("error", "Não foi possível logar com o Google");
            $this->router->route("web.login");
        }

        if ($code && empty($_SESSION["google_auth"])) {
            try {
                $token = $google->getAccessToken("authorization_code", ["code" => $code]);
                $_SESSION["google_auth"] = serialize($google->getResourceOwner($token));
            } catch (\Exception $exception) {
                flash("error", "Não foi possível logar com o Google");
                $this->router->route("web.login");
            }
        }

        /** @var $googleUser FacebookUser */
        $googleUser = unserialize($_SESSION["google_auth"]);
        $userById = (new User())->find("google_id = :id", "id={$googleUser->getId()}")->fetch();

        //LOGIN BY ID
        if ($userById) {
            unset($_SESSION["google_auth"]);

            $_SESSION["user"] = $userById->id;
            $this->router->redirect("app.home");
        }

        //LOGIN BY E-MAIL
        $userByEmail = (new User())->find("email = :e", "e={$googleUser->getEmail()}")->fetch();
        if ($userByEmail) {
            flash("info", "Olá {$googleUser->getFirstName()}, faça login para conectar seu Facebook");
            $this->router->redirect("web.login");
        }

        //REGISTER IF NOT
        $link = $this->router->route("web.login");
        flash(
            "info",
            "Olá {$googleUser->getFirstName()}, <b>se já tem uma conta clique <a title='Fazer Login' href='{$link}'>FAZER LOGIN</a></b>, ou complete seu cadastro"
        );
        $this->router->redirect("web.register");
    }

    public function socialValidate(User $user): void
    {
        /**
         * FACEBOOK
         */
        if (!empty($_SESSION["facebook_auth"])) {
            /** @var $facebookUser FacebookUser */
            $facebookUser = unserialize($_SESSION["facebook_auth"]);

            $user->facebook_id = $facebookUser->getId();
            $user->photo = $facebookUser->getPictureUrl();
            $user->save();

            unset($_SESSION["facebook_auth"]);
        }

        /**
         * GOOGLE
         */

        if (!empty($_SESSION["google_auth"])) {
            /** @var $googleUser GoogleUser */
            $googleUser = unserialize($_SESSION["google_auth"]);

            $user->google_id = $googleUser->getId();
            $user->photo = $googleUser->getAvatar();
            $user->save();

            unset($_SESSION["google_auth"]);
        }
    }
}