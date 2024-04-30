<?php

namespace App\Controller;

use App\Entity\Ligue;
use App\Form\LigueType;
use App\Repository\LigueRepository;
use App\Repository\RenouvellementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomeController extends AbstractController
{
    #[Route('/licences', name: 'app_licences')]
    public function index(RenouvellementRepository $renouvellementRepository): Response
    {
        return $this->render('home/licences.html.twig', [
            'licences' => $renouvellementRepository->findAll(),
        ]);
    }

    #[Route('/home', name: 'app_home', methods: ['GET','POST'])]
    public function index2(LigueRepository $ligueRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {    
        //dd($this->getUser());
        $ligue = new Ligue();
        $form = $this->createForm(LigueType::class, $ligue);
        $form->handleRequest($request);
        $ligues = $ligueRepository->findAll();
        /*foreach ($ligues as $ligue) {
            echo $ligue->getNom(); // Affiche le nom de chaque ligue
        }*/
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
            }else
                $ligue->setLogo('default.jpg');
            /*$traces = new Traces();
            $traces->setAction($request->getClientIp().' => Création Ligue');
            $traces->setUser($this->getUser());
            $traces->setDescription('Enregistrement de la ligue '.$ligue->__toString());
            $tracesRepository->save($traces, true);*/
            $entityManager->persist($ligue);
            $entityManager->flush();
            //$ligueRepository->save($ligue, true);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
       
        return $this->render('home/index.html.twig', [
            'ligues' => $ligues,
            'form' => $form,
            'ligue' => $ligue,
        ]);
    }
}
