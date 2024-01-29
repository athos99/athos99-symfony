<?php

namespace App\Menu;


use App\Application;
use App\Security\Action;
use App\Security\Role;
use Symfony\Component\Security\Core\Security;

class Menu
{
    /**
     * @var MenuTwig
     */
    public $menuTwig;

    public function __construct(MenuTwig $menuTwig, Security $security, Application $application)
    {
        $this->menuTwig = $menuTwig;
        $user = $security->getUser();

        if ($security->isGranted(Role::ALL)) {

            $menuTwig->addItem('redaction_left', new MenuItem('ckeditor', 'ckeditor', 'ckeditor' ));
        }
//        if ($security->isGranted(Action::CONSULTER_ENTITES_FACTURATION) || $security->isGranted(Action::CRUD_ENTITE_FACTU)) {
//            $subMenus = [];
//            if ($security->isGranted(Action::CONSULTER_ENTITES_FACTURATION)) {
//                $subMenus[] = new MenuItem('entitesFacturation', 'Rapports mensuels des entités de facturation',
//                    'admin_entite_facturation', [], [], $subMenus);
//            }
//            if ($security->isGranted(Action::CRUD_ENTITE_FACTU)) {
//                $subMenus[] = new MenuItem('crudEntiteFactu', 'Gérer', 'admin_crud_entite_factu_index');
//            }
//            $menuTwig->addItem('redaction_left',
//                new MenuItem('entitesFacturation', 'Entités de facturation', null, [], [],
//                    $subMenus));
//        }

        if ($security->isGranted(Action::CONSULTER_ADMIN)) {
            $subAdminMenus = [];
//            $subAdminMenus[] = new MenuItem('rubrique', 'Rubriques', 'admin_crud_rubrique_index');
//            $subAdminMenus[] = new MenuItem('modeles', 'Modèles', 'admin_crud_modele_index');

            $menuTwig->addItem('redaction_left', new MenuItem('admin', 'Admin', null, [], [], $subAdminMenus));
        }

        $subSettingsMenus = [];

        if ($security->isGranted(Action::PARAMETRE_READ)) {
            $subSettingsMenus[] = new MenuItem('parameter', 'Paramètres', 'admin_parameter');
        }

        if ($security->isGranted(Action::DEBUG)) {
//            $subSettingsMenus[] = new MenuItem('debug', 'Debug', 'admin_debug');
        }
        if ($security->isGranted(Action::SETTINGS)) {
  //          $subSettingsMenus[] = new MenuItem('externe', 'Simulation réseau externe', 'admin_simul_externe');
        }
        $menuTwig->addItem('redaction_right',
            new MenuItem('idConnexion',
                'Bonjour '.($user ? '<strong>'.$security->getUser()->getUserIdentifier().'</strong>' : ''),
                null));
        $menuTwig->addItem('redaction_right', new MenuItem('idDeconnexion', 'Se déconnecter', 'saml_logout'));
        if ($security->isGranted(Action::NAVBAR_ENVIRONNEMENT)) {
            $menuTwig->addItem(
                'redaction_right',
                new MenuItem(
                    'environnement',
                    '<span class="badge rounded-pill '.$application->getServerType().
                    '" data-bs-toggle="tooltip" title="Vous êtes dans l\'environnement de '.
                    $application->getServerType()."\nVersion : ".$application->getVersion().'">'.$application->getServerType().'</span>',
                    null
                )
            );
        }

        if ($subSettingsMenus) {
            $menuTwig->addItem(
                'redaction_right',
                new MenuItem('settings', '<i class="bi bi-gear"></i>', null, [], [], $subSettingsMenus)
            );
        }
    }
}
