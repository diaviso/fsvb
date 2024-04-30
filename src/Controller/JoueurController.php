<?php

namespace App\Controller;

use App\Entity\Joueur;
use App\Entity\Trace;
use App\Entity\TraceJoueur;
use App\Form\JoueurType;
use App\Form\TransfertType;
use App\Repository\JoueurRepository;
use App\Repository\TraceRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/joueur')]
class JoueurController extends AbstractController
{
    #[Route('/', name: 'app_joueur_index', methods: ['GET'])]
    public function index(JoueurRepository $joueurRepository): Response
    {
        return $this->render('joueur/index.html.twig', [
            'joueurs' => $joueurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_joueur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $joueur = new Joueur();
        $form = $this->createForm(JoueurType::class, $joueur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($joueur);
            $entityManager->flush();
            return $this->redirectToRoute('app_joueur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('joueur/new.html.twig', [
            'joueur' => $joueur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_joueur_show', methods: ['GET', 'POST'])]
    public function show(Joueur $joueur, Request $request, SessionInterface $session, EntityManagerInterface $entityManager, JoueurRepository $joueurRepository): Response
    {
        // Créer le formulaire
        $form = $this->createForm(TransfertType::class, $joueur);
        $form->handleRequest($request);

        // Capturer le nom du club original avant que le formulaire soit traité
        $clubOriginal = $joueur->getClub()->getNom();  // Assurez-vous que le club est chargé correctement

        if ($form->isSubmitted() && $form->isValid()) {
            $clubOriginal = $form->get('clubOriginal')->getData();  // Récupérer la valeur du champ caché

            $traceJoueur = new TraceJoueur();
            $traceJoueur->setDate(new \DateTime())
                        ->setUtilisateur(null)  // Vous devez définir l'utilisateur ici si possible
                        ->setAction("Transfert")
                        ->setJoueur($joueur)
                        ->setDescription("Transfert du joueur du CLUB " . $clubOriginal . " vers le club " . $joueur->getClub()->getNom());
            $entityManager->persist($traceJoueur);
            $entityManager->flush();

            // Redirection pour éviter la resoumission du formulaire
            return $this->redirectToRoute('app_joueur_show', ['id' => $joueur->getId()], Response::HTTP_SEE_OTHER);
        }
        //dd($joueur);
        return $this->render('joueur/show.html.twig', [
            'form' => $form,
            'joueur' => $joueur,
        ]);
        

    }

    #[Route('/{id}/edit', name: 'app_joueur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Joueur $joueur, EntityManagerInterface $entityManager, SluggerInterface $slugger, JoueurRepository $joueurRepository): Response
    {

               
        $form = $this->createForm(JoueurType::class, $joueur, [
            'clubs' => [$joueur->getClub()]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())  {

            //TRAITEMENT DU SEXE CHANGEANT DANS LE NUMERO DE LICENCE

            //TRAITEMENT DE LA PHOTO DE PROFILE
            $brochureFile = $form->get('brochure')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename).'-'.$joueur->getNumero();
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('photo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $joueur->setPhoto($newFilename);
            }else{
                $oldProfile = $joueurRepository->find($joueur->getId())->getPhoto();
                $joueur->setPhoto($oldProfile);
            }
            /*$trace = new Trace();
            $trace->setAction($request->getClientIp().' => Modification de joueur');
            $trace->setUtilisateur($this->getUser());
            $trace->setDescription('Modification des informations du joueur '. $joueur->__toString());
            
            $entityManager->persist($trace);*/
            $entityManager->persist($joueur);
            $entityManager->flush();

            return $this->redirectToRoute('app_joueur_show', ['id'=>$joueur->getId()], Response::HTTP_SEE_OTHER);
        }
       
        return $this->render('joueur/edit.html.twig', [
            'joueur' => $joueur,
            'form' => $form,
        ]);


        /*$form = $this->createForm(JoueurType::class, $joueur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_joueur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('joueur/edit.html.twig', [
            'joueur' => $joueur,
            'form' => $form,
        ]);*/
    }

    #[Route('/{id}/delete/definitly', name: 'app_joueur_delete', methods: ['POST'])]
    public function delete(Request $request, Joueur $joueur, EntityManagerInterface $entityManager): Response
    {
        //dd('here');
        if ($this->isCsrfTokenValid('delete'.$joueur->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($joueur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_club_show', ['id'=>$joueur->getClub()->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('card/{id}', name: 'app_user_card', methods: ['GET'])]
    public function generateCarde(Joueur $joueur)
    {
        $title = 'carte.pdf';
        $path = str_replace('\\', '/', $this->getParameter('photo_directory'));
        $path .= '/' . $joueur->getPhoto();
        $pathBG = str_replace('\\', '/', $this->getParameter('img_directory'));
        $pathBG .= '/back.jpg';
        $today = new DateTime();

        $template = $this->renderView('joueur/card.html.twig', [
            'title' => $title,
            'profil' => $path,
            'imageBG' => $pathBG,
            'joueur' => $joueur,
            'date' => $today,
        ]);

        $pdf = new Html2Pdf('L', 'A7', 'fr', 'true', 'UTF-8', array(5, 4, 1, 1));
        $pdf->pdf->SetDisplayMode('fullpage');
        $pdf->pdf->setBookmark("FSVB");

        $pdf->writeHTML($template);
        ob_clean();
        $pdf->output($title);
    }

    #[Route('/accepted/joueur/{id}', name: 'app_joueur_accepted', methods: ['GET'])]
    public function accepted(Joueur $joueur, JoueurRepository $joueurRepository, Request $request, TraceRepository $traceRepository, EntityManagerInterface $entityManager): Response
    {
        $joueur->setIsAccepted(! $joueur->isIsAccepted());
        
        /*$trace = new Trace();
        $trace->setAction($request->getClientIp().' => Changement état Joueur');
        $trace->setUtilisateur($this->getUser());
        $trace->setDescription('Modification de l\'état du joueur : '.$joueur->getId(). ' de'. ! $joueur->isIsAccepted() .'en => '. $joueur->isIsAccepted());
        $entityManager->persist($trace);
        $entityManager->persist($joueur);*/

        $entityManager->flush();
        return  $this->redirectToRoute('app_joueur_show', ['id'=> $joueur->getId()], Response::HTTP_SEE_OTHER);
    }

    

}
