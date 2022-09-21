<?php

namespace App\Controller;

use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/apropos', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

    #[Route('/produits', name: 'app_prod')]
    public function products(): Response
    {
        return $this->render('home/products.html.twig');
    }

    #[Route('/services', name: 'app_services')]
    public function services(): Response
    {
        return $this->render('home/services.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine, MailerInterface $mailer): Response
    {
        $create_form = self::create_contact_form($this);

        $create_form->handleRequest($request);
        $message = "";


        if ($create_form->isSubmitted() && $create_form->isValid()) {
            $datas = $create_form->getData();

            //send file mail
            $email = (new Email())
                ->from('contact@agri-ela.com')
                ->to('contact@agri-ela.com')
                ->subject("Message provenant du formulaire de contact")
                ->text('Sending emails is fun again!');

            // $mailer->send($email);


            $message = "Message envoyé avec succès";

            $newcreate_form = self::create_contact_form($this);

            $this->addFlash('success', 1);

            return $this->render('home/contact.html.twig', [
                'controller_name' => 'WelcomeController',
                'create_form' => $newcreate_form->createView(),
                'message' => $message,
                //'message' => ""
            ]);
        }


        return $this->render('home/contact.html.twig', [
            'controller_name' => 'WelcomeController',
            'create_form' => $create_form->createView(),
            'message' => $message,
            //'message' => ""
        ]);
    }

    static function create_contact_form($me)
    {
        return $me->createFormBuilder()
            ->add('names', TypeTextType::class, ['attr' => ['class' => 'form-control', 'placeholder' => 'Entrez votre nom complet'], 'label' => false])
            ->add('email', TypeTextType::class, ['attr' => ['class' => 'form-control', 'placeholder' => 'Entrez votre adresse email'], 'label' => false])
            ->add('message', TextareaType::class, ['attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le message'], 'label' => false])
            ->add('submit', SubmitType::class, ['attr' => ['class' => 'btn btn-primary',], 'label' => 'Envoyer'])
            ->setMethod('POST')
            ->getForm();
    }
}
