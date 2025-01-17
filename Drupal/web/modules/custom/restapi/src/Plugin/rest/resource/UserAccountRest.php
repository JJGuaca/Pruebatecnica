<?php

namespace Drupal\restapi\Plugin\rest\resource;
use Drupal\rest\Plugin\ResourceBase;
use Psr\Log\LoggerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\user\Entity\User;

/**
* Provides a resource to get view modes by entity and bundle.
* @RestResource(
*   id = "user_account_rest",
*   label = @Translation("User Account API"),
*   uri_paths = {
*     "canonical" = "/api/user-account",
*     "create" = "/api/edit-account",
*   }
* )
*/

class UserAccountRest extends ResourceBase {
    /**
    * A current user instance which is logged in the session.
    * @var \Drupal\Core\Session\AccountProxyInterface
    */
    protected $loggedUser;

    /**
    * Constructs a Drupal\rest\Plugin\ResourceBase object.
    *
    * @param array $config
    *   A configuration array which contains the information about the plugin instance.
    * @param string $module_id
    *   The module_id for the plugin instance.
    * @param mixed $module_definition
    *   The plugin implementation definition.
    * @param array $serializer_formats
    *   The available serialization formats.
    * @param \Psr\Log\LoggerInterface $logger
    *   A logger instance.
    * @param \Drupal\Core\Session\AccountProxyInterface $current_user
    *   A currently logged user instance.
    */
    public function __construct(array $config, $module_id, $module_definition, array $serializer_formats, LoggerInterface $logger, AccountProxyInterface $current_user) {
        parent::__construct($config, $module_id, $module_definition, $serializer_formats, $logger);
        $this->loggedUser = $current_user;
    }

    /**
    * {@inheritdoc}
    */
    public static function create(ContainerInterface $container, array $config, $module_id, $module_definition) {
        return new static(
            $config,
            $module_id,
            $module_definition,
            $container->getParameter('serializer.formats'),
            $container->get('logger.factory')->get('user_account_api'),
            $container->get('current_user')
        );
    }

    /*
    * Fetch User Account Details API
    */
    public function get() {
        global $base_url;
        try {
            $logged_user = User::load(\Drupal::currentUser()->id());
            $user_id = $logged_user->get('uid')->value;
            $role = $logged_user->get('roles')->getValue();
            $user_role = $role[0]['target_id'];

            $user_data['user_id'] = $user_id;
            $user_data['role'] = ucfirst($user_role);
            $user_data['first_name'] = $logged_user->get('field_first_name')->value;
            $user_data['last_name'] = $logged_user->get('field_last_name')->value;
            $user_data['email'] = $logged_user->get('mail')->value;
            $user_data['city'] = $logged_user->get('field_city')->value;

            $final_api_reponse = array(
                "status" => "Success",
                "message" => "User Account",
                "result" => $user_data
            );
            return new JsonResponse($final_api_reponse);
        }
        catch(\Exception $e) {
            $this->logger->error($this->t('Fallo en la autenticación .', ['%error' => $e->getMessage()]));
            $this->messenger->addMessage($this->t('Fallo en la autenticación .', ['%error' => $e->getMessage()]));
        }
    }

    /*
    * Edit User Details API
    */
    public function post(Request $data) {
        global $base_url;
        try {
            $logged_user = User::load(\Drupal::currentUser()->id());
            $user_id = $logged_user->get('uid')->value;
            $content = $data->getContent();
            $params = json_decode($content, TRUE);

            if($params['first_name'] != "") {
                $logged_user->set('field_first_name', $params['first_name']);
            }
            if($params['last_name'] != "") {
                $logged_user->set('field_last_name', $params['last_name']);
            }
            if($params['city'] != "") {
                $logged_user->set('field_city', $params['city']);
            }
            $logged_user->save();

            $final_api_reponse = array(
                "status" => "Success",
                "message" => "User Details Updated",
                "result" => "Your details have been updated."
            );
            return new JsonResponse($final_api_reponse);
        }
        catch (\Exception $e) {
            $this->logger->error($this->t('Fallo en la solicitud POST .', ['%error' => $e->getMessage()]));
            $this->messenger->addMessage($this->t('Fallo en la solicitud POST .', ['%error' => $e->getMessage()]));
        }
    }
}