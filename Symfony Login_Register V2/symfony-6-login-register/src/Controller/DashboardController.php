<?php
 
namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
 
class DashboardController extends AbstractController
{
    #[Route("/dashboard", name: "dashboard", methods: ['GET'])]
    
    public function index(UserRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $users = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('dashboard/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('dashboard/edition/{id}', 'dashboard.edit', methods: ['GET', 'POST'])]
    public function edit(
        UserRepository $repository,
        int $id,
        Request $request,
        EntityManagerInterface $manager
    ): Response
    {
        $user = $repository->findOneBy(["id" => $id]);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès !'
            );

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('dashboard/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('dashboard/suppression/{id}', 'dashboard.delete', methods: ['POST'])]
    public function delete(UserRepository $repository, EntityManagerInterface $manager, int $id): Response
    {
        $user = $repository->findOneBy(["id" => $id]);

        if(!$user) {
            $this->addFlash(
                'success',
                'L\'utilisateur en questionn\'a pas été trouvé !'
            );

            return $this->redirectToRoute('dashboard');
        }
        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'success',
            'L\'utilisateur a été supprimé avec succès !'
        );

        return $this->redirectToRoute('dashboard');
    }
}