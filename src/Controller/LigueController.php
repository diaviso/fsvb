<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Ligue;
use App\Entity\Trace;
use App\Entity\User;
use App\Form\ClubType;
use App\Form\LigueType;
use App\Repository\ClubRepository;
use App\Repository\LigueRepository;
use App\Repository\TraceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/ligue')]
class LigueController extends AbstractController
{
    #[Route('/', name: 'app_ligue_index', methods: ['GET'])]
    public function index(LigueRepository $ligueRepository): Response
    {
        return $this->render('ligue/index.html.twig', [
            'ligues' => $ligueRepository->findAll(),
        ]);
    }

    

    #[Route('/new', name: 'app_ligue_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ligue = new Ligue();
        $form = $this->createForm(LigueType::class, $ligue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ligue);
            $entityManager->flush();

            return $this->redirectToRoute('app_ligue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ligue/new.html.twig', [
            'ligue' => $ligue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ligue_show', methods: ['GET', 'POST'])]
    public function show(Ligue $ligue, Request $request, ClubRepository $clubRepository, TraceRepository $tracesRepository, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $club = new Club();
        $form = $this->createForm(ClubType::class, $club, [
            'username' => $club->getUser() != null ?$club->getUser()->getUserName() : '' ,
        ]);
        $user = new User();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //dd("here i am");
            $brochureFile = $form->get('brochure')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename).'-'.$club->getAbreviation();
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

                $club->setLogo($newFilename);
            }else
                $club->setLogo('default.jpg');
           
            $user->setRoles(['1'=>'ROLE_ADMIN_CLUB']);
            $user->setEmail(strtolower($club->getAbreviation()).'@senegalvolleyball.sn');
            $user->setUserName($form->get('username')->getData());
            $club->setGeneratedPassword($this->randomPassword());
            $user->setPassword($userPasswordHasher->hashPassword($user, $club->getGeneratedPassword()));
            $club->setUser($user);
            $club->setLigue($ligue);
            
            /*
            $traces = new Traces();
            $traces->setAction($request->getClientIp().' => Ajout Club');
            $traces->setUser($this->getUser());
            $traces->setDescription('Enregistrement du club : '.$club->__toString());
            $tracesRepository->save($traces, true);
            */
            //dd($club);
            $entityManager->persist($club);
            $entityManager->flush();
            //$clubRepository->save($club, true);
            return $this->redirectToRoute('app_ligue_show', ['id' => $club->getLigue()->getId()], Response::HTTP_SEE_OTHER);
        }
             
        return $this->render('ligue/show.html.twig', [
            'club'  => $club,
            'ligue' => $ligue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ligue_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ligue $ligue, LigueRepository $ligueRepository, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LigueType::class, $ligue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureFile = $form->get('brochure')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename).'-'.$ligue->getNom();
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
                $ligue->setLogo($newFilename);
            }else{
                $oldProfile = $ligueRepository->find($ligue->getId())->getLogo();
                $ligue->setLogo($oldProfile);
            }
            $traces = new Trace();
            $traces->setAction($request->getClientIp().' => Modification Ligue');
            $traces->setUtilisateur($this->getUser());
            $traces->setDescription('Modification de la ligue : '.$ligue->__toString());
            
            $entityManager->persist($ligue);
            $entityManager->persist($traces);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ligue/edit.html.twig', [
            'ligue' => $ligue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_ligue_delete', methods: ['POST', 'GET'])]
    public function delete(Request $request, Ligue $ligue, EntityManagerInterface $entityManager): Response
    {
        //dd("lad ");
        if ($this->isCsrfTokenValid('delete'.$ligue->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($ligue);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_ligue_index', [], Response::HTTP_SEE_OTHER);
    }
    
    public function randomPassword() {
        $alphabet = 'a-bcd+efghijklmnop#qrst_uvwxyzA*BCDEFGH6IJKLMNOPQ2RST?UVWXYZ12b345!67890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}
