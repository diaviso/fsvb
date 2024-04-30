<?php

namespace App\Controller;

use App\Entity\Trace;
use App\Form\TraceType;
use App\Repository\TraceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/trace')]
class TraceController extends AbstractController
{
    #[Route('/', name: 'app_trace_index', methods: ['GET'])]
    public function index(TraceRepository $traceRepository): Response
    {
        return $this->render('trace/index.html.twig', [
            'traces' => $traceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_trace_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $trace = new Trace();
        $form = $this->createForm(TraceType::class, $trace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($trace);
            $entityManager->flush();

            return $this->redirectToRoute('app_trace_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trace/new.html.twig', [
            'trace' => $trace,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trace_show', methods: ['GET'])]
    public function show(Trace $trace): Response
    {
        return $this->render('trace/show.html.twig', [
            'trace' => $trace,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trace_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trace $trace, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TraceType::class, $trace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_trace_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trace/edit.html.twig', [
            'trace' => $trace,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trace_delete', methods: ['POST'])]
    public function delete(Request $request, Trace $trace, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trace->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($trace);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_trace_index', [], Response::HTTP_SEE_OTHER);
    }
}
