<?php

namespace App\Controller;

use App\Entity\Affiliation;
use App\Entity\Club;
use App\Entity\Document;
use App\Form\AffiliationType;
use App\Form\DocumentType;
use App\Repository\AffiliationRepository;
use App\Repository\SaisonRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/affiliation')]
class AffiliationController extends AbstractController
{
    #[Route('/', name: 'app_affiliation_index', methods: ['GET'])]
    public function index(AffiliationRepository $affiliationRepository): Response
    {
        return $this->render('affiliation/index.html.twig', [
            'affiliations' => $affiliationRepository->findAll(),
        ]);
    }

    #[Route('/club/reaffiliation/{id}', name: 'app_club_reaffiliation', methods: ['GET', 'POST'])]
    public function reaffiliation(Affiliation $affiliation, Request $request, EntityManagerInterface $entityManager, SaisonRepository $saisonRepository): Response
    {
        $newAffiliation = new Affiliation();
        $newAffiliation->setAdresseDuClub($affiliation->getAdresseDuClub())
                        //->setClub($affiliation->getClub())
                        ->setCouleurs($affiliation->getCouleurs())
                        ->setDateDeDeclation($affiliation->getDateDeDeclation())
                        ->setDeuxiemeVidePresident($affiliation->getDeuxiemeVidePresident())
                        ->setFax($affiliation->getFax())
                        ->setIsAccepted(false)
                        ->setMailOfficiel($affiliation->getMailOfficiel())
                        ->setPrefecture($affiliation->getPrefecture())
                        ->setPremiervicePresident($affiliation->getPremiervicePresident())
                        ->setPresident($affiliation->getPresident())
                        ->setSecretaireGeneral($affiliation->getSecretaireGeneral())
                        ->setSiegeSocialDuClub($affiliation->getSiegeSocialDuClub())
                        ->setTresorierGeneral($affiliation->getTresorierGeneral())
                        ->setTelephone($affiliation->getTelephone())
                        ->setTerrains($affiliation->getTerrains())
                        ;
      
        
        //dd($newAffiliation);
        $form = $this->createForm(AffiliationType::class, $newAffiliation, ['clubs' => [$affiliation->getClub()]]);
        $form->handleRequest($request);
        $saison = $saisonRepository->find($request->getSession()->get('saison')->getId());
        
        if ($form->isSubmitted() && $form->isValid()) {

            $newAffiliation->setSaison($saison);
            $entityManager->persist($newAffiliation);
            foreach ($affiliation->getDocuments() as $doc) {
                $newDoc = new Document();
                $newDoc->setAffiliation($newAffiliation)
                    ->setNom($doc->getNom())
                    ->setType($doc->getType());
                    $entityManager->persist($newDoc);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_club_show', ['id' => $affiliation->getClub()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('affiliation/new.html.twig', [
            'affiliation' => $affiliation,
            'form' => $form,
        ]);
    }
    #[Route('/new/club/{id}', name: 'app_affiliation_new', methods: ['GET', 'POST'])]
    public function new(Club $club, Request $request, EntityManagerInterface $entityManager, SaisonRepository $saisonRepository): Response
    {
        $affiliation = new Affiliation();
        $form = $this->createForm(AffiliationType::class, $affiliation, ['clubs' => [$club]]);
        $form->handleRequest($request);
        $saison = $saisonRepository->find($request->getSession()->get('saison')->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            $affiliation->setSaison($saison);
            $entityManager->persist($affiliation);
            $entityManager->flush();

            return $this->redirectToRoute('app_club_show', ['id' => $club->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('affiliation/new.html.twig', [
            'affiliation' => $affiliation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_affiliation_show', methods: ['GET', 'POST'])]
    public function show(Affiliation $affiliation, Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $document = new Document();
      
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd("here");
            $brochureFile = $form->get('brochure')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename).'-';
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('documents_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $document->setNom($newFilename);
            }
            $document->setAffiliation($affiliation);
            //$documentRepository->save($document, true);
            $entityManager->persist($document);
            $entityManager->flush();
            return $this->redirectToRoute('app_affiliation_show', ['id'=>$affiliation->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('affiliation/show.html.twig', [
            'affiliation' => $affiliation,
            'form'=>$form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_affiliation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Affiliation $affiliation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AffiliationType::class, $affiliation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_affiliation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('affiliation/edit.html.twig', [
            'affiliation' => $affiliation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_affiliation_delete', methods: ['POST'])]
    public function delete(Request $request, Affiliation $affiliation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$affiliation->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($affiliation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_affiliation_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/accept', name: 'app_affiliation_accept', methods: ['GET'])]
    public function accept(Affiliation $affiliation, AffiliationRepository $affiliationRepository): Response
    {
        $affiliation->setIsAccepted(! $affiliation->isIsAccepted());
        $affiliationRepository->save($affiliation, true);
        return $this->redirectToRoute('app_affiliation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/recu/print/{id}', name: 'app_affiliation_bon_de_caisse', methods: ['GET'])]
    public function recu(Affiliation $affiliation)
    {
     
        $title ='recu.pdf';
        $logo = str_replace('\\', '/', $this->getParameter('img_directory'));
        $logo .= '/logo.jpeg';

        $flag = str_replace('\\', '/', $this->getParameter('img_directory'));
        $flag .= '/sn.png';
       
        $template = $this->renderView('affiliation/recu.html.twig',[
            'title' => $title,
            'affiliation' => $affiliation,
            'logo' => $logo,
            'flag' => $flag,
            'date' => new DateTime(),
        ]);

        $pdf = new Html2Pdf('P', 'A4', 'fr', 'true', 'UTF-8', array(10, 5, 10, 10));
        $pdf->pdf->SetDisplayMode('fullpage');
        $pdf->pdf->setBookmark("FSVB");

        $pdf->writeHTML($template);
        ob_clean();
        $pdf->output($title);
   
    }
}
