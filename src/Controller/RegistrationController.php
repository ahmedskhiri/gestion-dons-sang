<?php

namespace App\Controller;

use App\Entity\Donateur;
use App\Form\RegistrationFormType;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface; // Important pour la connexion auto
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Security\AppAuthenticator;
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher,
    Security $security,
        EntityManagerInterface $entityManager
    ): Response
    {
        $user = new Donateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // 1. Hachage du mot de passe
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // 2. Sauvegarde dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // 3. Connexion automatique : utilise le service Security pour connecter
            // l'utilisateur sans avoir besoin d'un authenticator injectable ici.
            // When multiple authenticators are configured, pass the authenticator
            // service name (class) so Security::login() can resolve the correct
            // authenticator for the firewall.
            $security->login($user, AppAuthenticator::class);

            return $this->redirectToRoute('app_Acceuil');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}