<?php

namespace App;

use App\Param;

/**
 * Les paramètres sont de type string, car ils seront stockés dans la DB
 * comme le PHP est un langage non typé cela ne pose pas de problèmes :-)
 * pour les boolean, utiliser '0' ou '1' que 'true' ou 'false'
 */
class Parameter extends BaseParameter
{
    /**
     * @Param(description="Met le site d'édition des avis en mode maintenance, seul l'administrateur peut y accéder", valeur="1 ou 0")
     */
   public $modeMaintenance = "0";


    /**
     * @Param(description="Information affichée", valeur="string : 'Une maintenance est actuellement en cours, rendant provisoirement indisponibles la création et la publication d’avis FAO. Tout est mis en œuvre pour rétablir ce service dans les meilleurs délais.' ou '' si on ne veut rien afficher")
     */
    public $pageInfo = "";


    /**
     * @Param(description="Affiche sur la page admin des informations sur les attributs Gina SAML2", valeur ="1 ou 0")
     */
    public string $adminHomepageDisplayGinaAttribut = '0';


}
