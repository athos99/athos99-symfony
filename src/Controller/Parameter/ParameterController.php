<?php

namespace App\Controller\Parameter;

use App\Controller\BaseController;
use App\Entity\Parameter as ParameterEntity;
use App\Parameter as ParameterClass;
use App\Form\ParameterType;
use App\Security\Action;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/settings/parameter")]
class ParameterController extends BaseController
{
    #[Route('/', name: 'admin_parameter', methods: ['GET'])]
    public function index(EntityManagerInterface $faoEntityManager, ParameterClass $parameterClass)
    {
        //Refuse l'accès si l'utilisateur n'a pas le droit de lire les paramètres
        $this->denyAccessUnlessGranted(Action::PARAMETRE_READ);
        $parameters = $faoEntityManager
            ->getRepository(ParameterEntity::class)
            ->findAll();
        $defaultValues = $parameterClass->getDefaultValues();
        $currentValues = $parameterClass->getCurrentValues();
        return $this->render('parameter/index.html.twig', [
            'parameters' => $parameters,
            'defaultValues' => $defaultValues,
            'currentValues' => $currentValues,
            'annotations' => $parameterClass->getAnnotations(),
            'modif' => $this->isGranted(Action::PARAMETRE_WRITE),
        ]);
    }

    #[Route('/{name}/edit', name: 'admin_parameter_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        $name,
        EntityManagerInterface $faoEntityManager,
        ParameterClass $parameterClass
    ) {
        //Refuse l'accès si l'utilisateur n'a pas de rôle "ADMIN"
        $this->denyAccessUnlessGranted(Action::PARAMETRE_WRITE);
        $annotation = $parameterClass->getAnnotations()[$name];
        if ($annotation->readOnly) {
            return $this->redirectToRoute('admin_parameter', [], Response::HTTP_SEE_OTHER);
        }

        //Récupère les deux tableaux de la classe Parameter
        $defaultValues = $parameterClass->getDefaultValues();
        $currentValues = $parameterClass->getCurrentValues();
        //Récupère la valeur par défaut qui correspond au paramètre passé dans l'URL
        $defaultValue = $defaultValues[$name];

        $currentValueState = array_key_exists($name, $currentValues);


        //Séléction de l'enregistrement de la table qui à comme valeur 'name' la valeur passée dans l'URL
        $query = $faoEntityManager->createQueryBuilder();
        $query->select('parameter')
            ->from(ParameterEntity::class, 'parameter')
            ->where('parameter.name = :name')
            ->setParameter('name', $name);
        /** @var ParameterEntity[] $dbParameters */
        $dbParameters = $query->getQuery()->getResult();
        //Si la table contient quelque chose on assigne l'enregistrement à la variable $dbParameter
        if ($dbParameters) {
            $dbParameter = $dbParameters[0];
            //Sinon, l'entité est créée avec le nom présent dans l'URL, ainsi que la valeur par défaut correspondante
        } else {
            $dbParameter = new ParameterEntity();
            $dbParameter->setName($name);
            $dbParameter->setValue($defaultValue);
            $faoEntityManager->persist($dbParameter);
        }

        //Création du formulaire avec l'objet '$dbParameter'
        $form = $this->createForm(ParameterType::class, $dbParameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->get('action') == 'update') {
                if ($dbParameter->getValue() == $defaultValue) {
                    $faoEntityManager->remove($dbParameter);
                }
                $faoEntityManager->flush();
                $parameterClass->clearCache();

                $this->logger->info(
                    'Parameter edited : ["Name" = "'
                    .$dbParameter->getName()
                    .'", "Value" = "'
                    .$dbParameter->getValue()
                    .'", "Default Value" = "'
                    .$defaultValue
                    .'"]'
                );

                return $this->redirectToRoute('admin_parameter', [], Response::HTTP_SEE_OTHER);
            } elseif ($request->request->get('action') == 'delete') {
                //Séléction de l'enregistrement de la table qui à comme valeur "name" la valeur passée dans l'URL
                $deleteQuery = $faoEntityManager->createQueryBuilder();
                $deleteQuery->select('parameter')
                    ->from(ParameterEntity::class, 'parameter')
                    ->where('parameter.name = :name')
                    ->setParameter('name', $name);
                /** @var ParameterEntity $deleteName */
                $deleteName = $deleteQuery->getQuery()->getResult();
                //Supprime le premier enregistrement de l'array
                $faoEntityManager->remove($deleteName[0]);
                $faoEntityManager->flush();
                $parameterClass->clearCache();

                //Ajout d'un message dans le fichier de logs
                $this->logger->info(
                    'Parameter reverted to default value : ["Name" = "'
                    .$dbParameter->getName()
                    .'", "Default Value" = "'
                    .$defaultValue
                    .'"]'
                );

                return $this->redirectToRoute('admin_parameter', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('parameter/edit.html.twig', [
            'parameter' => $parameterClass,
            'form' => $form,
            'currentValueState' => $currentValueState,
            'defaultValue' => $defaultValue,
            'annotation' => $parameterClass->getAnnotations()[$name] ?? '',
        ]);
    }
}
