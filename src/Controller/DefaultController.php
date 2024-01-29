<?php

namespace App\Controller;

use App\Parameter;
use App\Security\Action;
use App\Security\Role;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends BaseController
{
    #[Route('/', name: 'homepage')]
    public function index(Parameter $parameter)
    {
        $this->denyAccessUnlessGranted(Action::HOME);
        $roles = Role::getRoles();
        foreach ($roles as $key => $role) {
            if (!$this->isGranted($role)) {
                unset($roles[$key]);
            }
        }

        return $this->render(
            'homepage.html.twig',
            ['rolesHeritage' => $roles, 'ginaAttribut' => $parameter->adminHomepageDisplayGinaAttribut]
        );
    }

    #[Route('/todo', name: 'todo')]
    public function todo()
    {
        $this->denyAccessUnlessGranted(Action::BACKEND);

        return $this->render('todo.html.twig', []);
    }

    #[Route('/ckeditor', name: 'ckeditor')]
    public function ckeditor()
    {
        $this->denyAccessUnlessGranted(Role::ALL);

        return $this->render('ckeditor.html.twig', []);
    }

    #[Route('/500', name: '500')]
    public function e500()
    {
        throw new \Exception('error');

        return $this->render('ckeditor.html.twig', []);
    }
}
