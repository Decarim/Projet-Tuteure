<?php
namespace App\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use App\Model\AperitifModel;
use App\Helper\HelperDate;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
class AperitifController implements ControllerProviderInterface
{

    private $AperitifModel;
    private $helperDate;


    public function index(Application $app)
    {
        return $this->showAperitif($app);       // appel de la méthode show
    }

    public function home(Application $app)
    {
        return $app["twig"]->render('aperitif/v_admin.html.twig');
    }

    public function addaperitif(Application $app)
    {

        $this->AperitifModel = new AperitifModel($app);
        $aperitif = $this->AperitifModel->getAllAperitif();
        return $app["twig"]->render('aperitif/v_form_create_aperitif.html.twig', ['aperitif' => $aperitif]);
    }

    public function deleteAperitif(Application $app, $id)
    {
        $this->AperitifModel = new AperitifModel($app);

        $aperitifModel = $this->AperitifModel->getAperitif($id);
        return $app["twig"]->render('aperitif/v_form_delete_aperitif.html.twig', ['donnees' => $aperitifModel]);
    }

    public function editAperitif(Application $app, $id)
    {
        $this->AperitifModel = new AperitifModel($app);
        $donnees = $this->AperitifModel->getAperitif($id);
        //var_dump($donnees);

        return $app["twig"]->render('aperitif/v_form_update_aperitif.html.twig', ['donnees' => $donnees]);
    }

    public function validFormAddAperitif(Application $app)
    {
        //var_dump($app['request']->attributes);
        if (isset($_POST['_csrf_token'])) {
            $token = $_POST['_csrf_token'];
            $csrf_token = new CsrfToken('token_add_aperitif', $token);
            $csrf_token_ok = $app['csrf.token_manager']->isTokenValid($csrf_token);
            if (!$csrf_token_ok) {
                $erreurs["csrf"] = "Erreur : token : " . $token;
                return $app["twig"]->render("v_error_csrf.html.twig", ['erreurs' => $erreurs]);
            }
        } else
            return $app->redirect($app["url_generator"]->generate("index.errorCsrf"));

        if (1 == 1) {
            $donnees = [
                'libelle_aperitif' => htmlspecialchars($_POST['libelle_aperitif']),                    // echapper les entrées

            ];


            if ((!preg_match("/^[A-Za-z ]{2,}/", $donnees['libelle']))) $erreurs['libelle'] = 'libelle composé de 2 lettres minimum';
            if (!empty($erreurs)) {
                $this->AperitifModel = new AperitifModel($app);
                $aperitif = $this->AperitifModel->getAllaperitif();
                return $app["twig"]->render('aperitif/v_form_create_aperitif.html.twig', ['donnees' => $donnees, 'erreurs' => $erreurs, 'aperitif' => $aperitif]);
            } else {
                $this->AperitifModel = new AperitifModel($app);
                $this->AperitifModel->insertAperitif($donnees);
                return $app->redirect($app["url_generator"]->generate("aperitif.index"));
            }
        } else {
            return "probleme";
        }
    }

    public function validFormDeleteAperitif(Application $app, Request $req)
    {
        //var_dump($app['request']->attributes);
        if (isset($_POST['_csrf_token'])) {
            $token = $_POST['_csrf_token'];
            $csrf_token = new CsrfToken('token_delete_aperitif', $token);
            $csrf_token_ok = $app['csrf.token_manager']->isTokenValid($csrf_token);
            if (!$csrf_token_ok) {
                $erreurs["csrf"] = "Erreur : token : " . $token;
                return $app["twig"]->render("v_error_csrf.html.twig", ['erreurs' => $erreurs]);
            }
        } else
            return $app->redirect($app["url_generator"]->generate("index.errorCsrf"));

        $donnees = [
            'idAperitif' => $app->escape($req->get('id')),
        ];

        $this->AperitifModel = new AperitifModel($app);
        $this->AperitifModel->deleteaperitif($donnees);
        return $app->redirect($app["url_generator"]->generate("aperitif.index"));
    }

    public function validFormEditaperitif(Application $app, Request $req)
    {
        $this->helperDate = new HelperDate();
        if (isset($_POST['_csrf_token'])) {
            $token = $_POST['_csrf_token'];
            $csrf_token = new CsrfToken('token_edit_aperitif', $token);
            $csrf_token_ok = $app['csrf.token_manager']->isTokenValid($csrf_token);
            if (!$csrf_token_ok) {
                $erreurs["csrf"] = "Erreur : token : " . $token;
                return $app["twig"]->render("v_error_csrf.html.twig", ['erreurs' => $erreurs]);
            }
        } else
            return $app->redirect($app["url_generator"]->generate("index.errorCsrf"));

        $donnees = [
            'idAperitif' => htmlspecialchars($_POST['id']),
            'libelle_aperitif' => htmlspecialchars($_POST['libelle']),                    // echapper les entrées

        ];

        if ((!preg_match("/^[A-Za-z ]{2,}/", $donnees['libelle']))) $erreurs['libelle'] = 'libelle composé de 2 lettres minimum';
        if (!empty($erreurs)) {
            $this->AperitifModel = new AperitifModel($app);
            $aperitif = $this->AperitifModel->getAllaperitif();
            return $app["twig"]->render('aperitif/v_form_update_aperitif.html.twig', ['donnees' => $donnees, 'erreurs' => $erreurs, 'aperitif' => $aperitif]);
        } else {
            $this->AperitifModel = new AperitifModel($app);
            $this->AperitifModel->updateAperitif($donnees);
            return $app->redirect($app["url_generator"]->generate("aperitif.index"));
        }
    }

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\AperitifController::index')->bind('aperitif.index');
        $controllers->get('/show', 'App\Controller\AperitifController::showAperitif')->bind('aperitif.show');

        $controllers->get('/home', 'App\Controller\AperitifController::home')->bind('aperitif.home');

        $controllers->get('/add', 'App\Controller\AperitifController::addAperitif')->bind('aperitif.add');
        $controllers->post('/add', 'App\Controller\AperitifController::validFormAddAperitif')->bind('aperitif.validFormAddAperitif');

        $controllers->get('/delete{id}', 'App\Controller\AperitifController::deleteAperitif')->bind('aperitif.delete');
        $controllers->delete('/delete', 'App\Controller\AperitifController::validFormDeleteAperitif')->bind('aperitif.validFormDeleteAperitif');

        $controllers->get('/edit{id}', 'App\Controller\AperitifController::editAperitif')->bind('aperitif.edit');
        $controllers->put('/edit', 'App\Controller\AperitifController::validFormEditAperitif')->bind('aperitif.validFormEditAperitif');

        return $controllers;
    }

}