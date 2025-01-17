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
*   id = "user_registration_rest",
*   label = @Translation("User Registration API"),
*   uri_paths = {
*     "create" = "/api/user-registration",
*   }
* )
*/
class UserRegistrationRest extends ResourceBase {
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
            $container->get('logger.factory')->get('user_registration_api'),
            $container->get('current_user')
        );
    }

    /*
    * User Registration API
    */
    public function post(Request $data) {
        global $base_url;
        try {
            $content = $data->getContent();
            $params = json_decode($content, TRUE);

            $message_string = "";
            $message_string .= empty($params['tipo_de_documento']) ? "Tipo de documento. " : "";
            $message_string .= empty($params['n_identificacion']) ? "NIdentificación. " : "";
            $message_string .= empty($params['first_name']) ? "Nombres. " : "";
            $message_string .= empty($params['last_name']) ? "Apellidos. " : "";
            $message_string .= empty($params['telefono']) ? "Teléfono. " : "";
            $message_string .= empty($params['direccion']) ? "Dirección. " : "";
            $message_string .= empty($params['email']) ? "Email ID. " : "";
            $message_string .= empty($params['password']) ? "Password. " : "";
            if($message_string) {
                $final_api_reponse = array(
                    "status" => "Error",
                    "message" => "Mandatory Fields Missing",
                    "result" => "Required fields: ".$message_string
                );
                return new JsonResponse($final_api_reponse);
            }
            else {
                $user_name = strtolower($params['first_name'].'.'.$params['last_name']);
                $user_full_name = ucfirst($params['first_name']).' '.ucfirst($params['last_name']);
                $user_email = $params['email'];

                // Checking for duplicate user entries
                $email_check = \Drupal::entityQuery('user')->accessCheck(TRUE)->condition('mail', $user_email)->execute();
                $username_check = \Drupal::entityQuery('user')->accessCheck(TRUE)->condition('name', $user_name)->execute();
                if (!empty($email_check) || !empty($username_check)) {
                    $final_api_reponse = array(
                        "status" => "Error",
                        "message" => "Registration Failed",
                        "result" => "User details already exists. Please try with different Name or Email-ID."
                    );
                    return new JsonResponse($final_api_reponse);
                }
                else {
                    // Crear nuevo usuario
                    $new_user = User::create([
                        'name' => $user_name,
                        'pass' => $params['password'],
                        'mail' => $user_email,
                        'roles' => array('personas', 'authenticated'),
                        'field_first_name' => ucfirst($params['first_name']),
                        'field_last_name' => ucfirst($params['last_name']),
                        'field_direccion' => $params['direccion'],
                        'field_telefono' => $params['telefono'],
                        'field_n_identificacion' => $params['n_identificacion'],
                        'field_tipo_de_documento' => $params['tipo_de_documento'],
                        'status' => 1,
                    ])->save();

                    $final_api_reponse = array(
                        "status" => "Success",
                        "message" => "Registration Successful",
                        "result" => "Thank You. Account for ".$user_full_name." has been created."
                    );
                    return new JsonResponse($final_api_reponse);
                }
            }
            
            
        }
        catch (\Exception $e) {
            $this->logger->error($this->t('Fallo en el registro del usuario.', ['%error' => $e->getMessage()]));
        }
    }
}