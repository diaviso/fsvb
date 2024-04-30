<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Joueur;
use App\Entity\Renouvellement;
use App\Entity\Trace;
use App\Form\ClubType;
use App\Form\JoueurType;
use App\Repository\ClubRepository;
use App\Repository\JoueurRepository;
use App\Repository\SaisonRepository;
use App\Repository\TraceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/club')]
class ClubController extends AbstractController
{
    #[Route('/', name: 'app_club_index', methods: ['GET'])]
    public function index(ClubRepository $clubRepository): Response
    {
        return $this->render('club/index.html.twig', [
            'clubs' => $clubRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_club_show', methods: ['GET','POST'])]
    public function show(SaisonRepository $saisonRepository, Club $club, Request $request, JoueurRepository $joueurRepository, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $joueur = new Joueur();
        $form = $this->createForm(JoueurType::class, $joueur,[
            'clubs' => [$club],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd('aksina fi');
            $joueur->setIsAccepted(false);
            $joueur->setClub($club);
            $joueur->setNumero($this->gererateNumero($joueurRepository, $joueur));
              
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
            }else
                $joueur->setPhoto('default.png');

            //création de la demande de d'affiliation du joueur
            $renouvellement = new Renouvellement();
            $renouvellement->setJoueur($joueur);
            $id = $request->getSession()->get("saison")->getId();
            $saison = $saisonRepository->find($id);
            $renouvellement->setSaison($saison);
            $renouvellement->setValide(false);
            $entityManager->persist($renouvellement);

            $entityManager->persist( $joueur );
            $entityManager->flush();
            return $this->redirectToRoute('app_club_show', ['id'=>$joueur->getClub()->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('club/show.html.twig', [
            'club' => $club,
            'form' => $form->createView(),
        ]);
   
    }

    #[Route('/{id}/edit', name: 'app_club_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Club $club, EntityManagerInterface $entityManager, TraceRepository $tracesRepository, SluggerInterface $slugger, ClubRepository $clubRepository): Response
    {
       
        $form = $this->createForm(ClubType::class, $club, [
            'username' => $club->getUser()->getUserName() ?$club->getUser()->getUserName() : '' ,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureFile = $form->get('brochure')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename).'-'.$club->getNom();
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('logos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $club->setLogo($newFilename);
            }else{
                $oldProfile = $clubRepository->find($club->getId())->getLogo();
                $club->setLogo($oldProfile);
            }
            $club->getUser()->setUserName($form->get('username')->getData());

            $traces = new Trace();
            $traces->setAction($request->getClientIp().' => Modification club');
            $traces->setUtilisateur($this->getUser());
            $traces->setDescription('Modification des information du club '. $club->__toString());
            //$tracesRepository->save($traces, true);
            $entityManager->persist($traces);
            $entityManager->persist($club);
            $entityManager->flush();
           // $clubRepository->save($club, true);

            return $this->redirectToRoute('app_club_show', ['id'=>$club->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('club/edit.html.twig', [
            'club' => $club,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_club_delete', methods: ['POST'])]
    public function delete(Request $request, Club $club, EntityManagerInterface $entityManager): Response
    {
        //dd('here');
        if ($this->isCsrfTokenValid('delete'.$club->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($club);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_club_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/ask/renew/{id}', name: 'app_ask_renew', methods:['GET'])]
    public  function askRenew(Joueur $joueur, Request $request, SaisonRepository $saisonRepository, EntityManagerInterface $entityManager): Response{

        $renouvellement = new Renouvellement();
        $renouvellement->setJoueur($joueur);
        $saison = $saisonRepository->find($request->getSession()->get('saison')->getId());
        $renouvellement->setSaison($saison);
        $renouvellement->setValide(false);
        $entityManager->persist($renouvellement);
        $entityManager->flush();

        return $this->redirectToRoute('app_club_show', ['id'=>$joueur->getClub()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/validate/renew/{id}', name: 'app_validate_renew', methods:['GET'])]
    public  function validate(Renouvellement $renouvellement, Request $request, SaisonRepository $saisonRepository, EntityManagerInterface $entityManager): Response{

        //dd($renouvellement);
        $renouvellement->setValide(true);
        $entityManager->persist($renouvellement);
        $entityManager->flush();

        return $this->redirectToRoute('app_club_show', ['id'=>$renouvellement->getJoueur()->getClub()->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/invalidate/renew/{id}', name: 'app_invalidate_renew', methods:['GET'])]
    public  function invalidate(Renouvellement $renouvellement, Request $request, SaisonRepository $saisonRepository, EntityManagerInterface $entityManager): Response{

        //dd($renouvellement);
        $renouvellement->setValide(false);
        $entityManager->persist($renouvellement);
        $entityManager->flush();

        return $this->redirectToRoute('app_club_show', ['id'=>$renouvellement->getJoueur()->getClub()->getId()], Response::HTTP_SEE_OTHER);
    }
    public function gererateNumero(JoueurRepository $joueurRepository, Joueur $joueur): string
    {
        $numero = "LIC-";
        $joueurs = $joueurRepository->findAll();
        if ($joueurs != null) {
            $el = $joueurs[count($joueurs) - 1];
            $last = substr($el->getNumero(), 4,4);
            $last++;
            if ($last < 10)
                $numero .= '000' . $last;
            elseif ($last < 100)
                $numero .= '00' . $last;
            elseif ($last < 1000)
                $numero .= '0' . $last;
            else
                $numero .= '' . $last;
        } else
            $numero .= '0001';

        $numero .= substr($joueur->getSexe(), 0, 1);
        $y = explode('/', $joueur->getDateDeNaissance()->format('d/m/y'));
       
        $numero .= $y[2];
        return $numero;
    }
    
}
