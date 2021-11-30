<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use App\Entity\Client;
use App\Entity\Restaurateur;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function clientRegister(Request $request, UserPasswordEncoderInterface $passEncoder)
    {
    
        $entityManager = $this->getDoctrine()->getManager();
        
        $userForm = $this->createFormBuilder()
            ->add('username', TextType::class, [
                'label' => 'Pseudo',
                'attr' => [
                    'class' => 'w3-input w3-border w3-light-grey w3-margin-top w3-margin-bottom',
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'class' => 'w3-input w3-border w3-light-grey w3-margin-top w3-margin-bottom',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmation de mot de passe',
                    'attr' => [
                        'class' => 'w3-input w3-border w3-light-grey w3-margin-top w3-margin-bottom',
                    ],
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'w3-input w3-border w3-light-grey w3-margin-top w3-margin-bottom',
                ],
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'class' => 'w3-input w3-border w3-light-grey w3-margin-top w3-margin-bottom',
                ],
            ])
            ->add('usertype', ChoiceType::class, [
                'label' => 'Quel type d\'utilisateur êtes-vous?',
                'choices' => [
                    'client' => 'client',
                    'restaurateur' => 'restaurateur',
                    'admin' => 'admin',
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'class' => 'w3-input w3-border w3-light-grey w3-margin-top w3-margin-bottom',
                ],
            ])
            ->add('register', SubmitType::class, [
                'label' => 'Créer son compte',
                'attr' => [
                    'class' => 'w3-button w3-green w3-margin-bottom',
                    'style' => 'margin-top:5px;'
                ],
            ])
            ->getForm();

        //Nous traions les données reçues au sein de notre formulaire
        $userForm->handleRequest($request);
        if ($request->isMethod('post') && $userForm->isValid()) {
            
            $data = $userForm->getData();

            if ($data['usertype'] == 'client') {
                $user = new Client;
                $user->setNom($data['nom']);
                $user->setAdresse($data['adresse']);
                $user->setRoles(['ROLE_USER', 'ROLE_CLIENT']);
            } elseif ($data['usertype'] == 'restaurateur') {
                $user = new Restaurateur;
                $user->setNom($data['nom']);
                $user->setTelephone('non renseigné');
                $user->setRoles(['ROLE_USER', 'ROLE_RESTAURATEUR']);
            } elseif ($data['usertype'] == 'admin'){
                $user = new Admin;
                $user->setMatricule('non renseigné');
                $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
            }
            $user->setUsername($data['username']);
            $user->setPassword($passEncoder->encodePassword($user, $data['password']));
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('app_login'));
        }
        
        //ATTENTION: ICI C'EST POUR LA DEMONSTRATION... 
        //POUR L'ADMINISTRATEUR, IL FAUDRA FAIRE UN FORMULAIRE A PART DE SORTE QUE SEUL UN ADMIN PEUT ENREGISTRER UN NOUVEL ADMIN !!!!!!

        return $this->render('index/dataform.html.twig', [
            'formName' => 'Inscription Utilisateur',
            'dataForm' => $userForm->createView(),
        ]);
    }
    
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
