<?php

namespace App;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Parameter as ParameterEntity;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class BaseParameter
{
    /**
     * @var array
     */
    protected $defaultValues = [];

    /**
     * @var array
     */
    protected $currentValues = [];

    /**
     * @var \ReflectionProperty[]
     */
    private $paramProperties;

    /**
     * @var string[]
     */
    private $annotations = null;

    /**
     * @var FilesystemAdapter
     */
    protected $cache;
    public const PARAMETER_KEY_CACHE = 'parameter';


    public function __construct(EntityManagerInterface $entityManager)
    {

        try {
            $this->cache = new FilesystemAdapter();

            //Initialise une classe 'ReflectionClass' permettant de rapporter certaines informations d'une classe. En l'occurence, la classe 'Parameter'.
            $paramClass = new \ReflectionClass($this);
            //Récupère toutes les propriétés de la classe sous la forme d'un tableau d'objets 'ReflectionProperty'.
            $this->paramProperties = $paramClass->getProperties(\ReflectionProperty::IS_PUBLIC);

            $paramNames = [];
            //Récupère la valeur 'name' ainsi que les valeurs par défaut de chaque objets présents dans $paramProperties.
            foreach ($this->paramProperties as $paramProperty) {
                $paramName = $paramProperty->getName();
                //Ajoute les 'name' dans le tableau final.
                $paramNames[] = $paramName;
                //Ajoute au tableau "$paramName" comme clé et le texte par défaut des propriétés comme valeur
                $this->defaultValues[$paramName] = $this->{$paramName};
            }


//            $dbParameters= $this->cache->get(
//                self::PARAMETER_KEY_CACHE,
//                function (ItemInterface $item) use ($entityManager){
//                    try {
//                        //Séléction de la table 'parameter' de la base de données, grâce au QueryBuilder Doctrine
//                        $mainQuery = $entityManager->createQueryBuilder();
//                        $mainQuery->select('parameter')
//                            ->from(ParameterEntity::class, 'parameter');
//                        /** @var ParameterEntity[] $dbParameters */
//                        $item->expiresAfter(120);
//                        return $mainQuery->getQuery()->getResult();
//                    } catch (\Throwable $e) {
//                    }
//                    $this->clearCache();
//                    throw new \Exception('Parameter cache');
//                },
//                0.1
//            );

            //Séléction de la table 'parameter' de la base de données, grâce au QueryBuilder Doctrine
            $mainQuery = $entityManager->createQueryBuilder();
            $mainQuery->select('parameter')
                ->from(ParameterEntity::class, 'parameter');
            /** @var ParameterEntity[] $dbParameters */
            $dbParameters = $mainQuery->getQuery()->getResult();

            /* Boucle sur le tableau contenant tout les enregistrements (stockés sous forme d'objets)
            de la table 'parameter' */
            foreach ($dbParameters as $dbParameter) {
                //Récupère la valeur 'name' des enregistrements
                $dbParameterName = $dbParameter->getName();
                //Vérifie si le champ 'name' de l'enregistrement correspond à celui d'un des paramètres
                if (in_array($dbParameterName, $paramNames)) {
                    //Si oui, récupère la valeur 'value' de l'enregistrement
                    $dbParameterValue = $dbParameter->getValue();
                    //Vérifie le champ 'value' de l'enregistrement correspond à une des valeurs par défaut
                    if ($dbParameterValue == $this->defaultValues[$dbParameterName]) {
                        //Si oui, supprime l'enregistrement en quesiton de la base de données
                        $duplicateQuery = $entityManager->createQueryBuilder();
                        $duplicateQuery->select('p')
                            ->from(ParameterEntity::class, 'p')
                            ->where('p.name = :name')
                            ->setParameter('name', $dbParameterName);
                        /** @var ParameterEntity $duplicatedValue */
                        $duplicatedValue = $duplicateQuery->getQuery()->getResult();
                        $entityManager->remove($duplicatedValue[0]);
                        $entityManager->flush();
                        $this->clearCache();
                    } else {
                        //Sinon, modifie la valeur du paramètre en question par celle de l'enregistrement de la base
                        //Et ajoute l'enregistrement dans le tableau '$dbParameterValue'
                        $this->{$dbParameterName} = $dbParameterValue;
                        $this->currentValues[$dbParameterName] = $dbParameterValue;
                    }
                }
            }
        } catch (\Throwable $t) {
            throw new \Exception('Erreur fatal paramètre DB');
        }
    }


    /**
     * @return array
     */
    public function getDefaultValues(): array
    {
        return $this->defaultValues;
    }

    /**
     * @return array
     */
    public function getCurrentValues(): array
    {
        return $this->currentValues;
    }


    /**
     * @throws InvalidArgumentException
     */
    public function clearCache()
    {
        $this->cache->delete(self::PARAMETER_KEY_CACHE);
    }


    /**
     * @return Param[]
     */
    public function getAnnotations()
    {
        if ($this->annotations === null) {
            $this->annotations = [];
            $annotationsEmpty = new Param();
            $reader = new AnnotationReader();
            foreach ($this->paramProperties as $paramProperty) {
                $annotations = $reader->getPropertyAnnotations($paramProperty);
                $this->annotations[$paramProperty->getName()] = $annotations ? $annotations[0] : $annotationsEmpty;
            }
        }

        return $this->annotations;
    }
}
