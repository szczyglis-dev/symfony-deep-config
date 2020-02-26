<?php

namespace App\Controller;

use App\Entity\DeepConfig;
use App\Form\DeepConfigType;
use App\Repository\DeepConfigRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/deep/config")
 */
class DeepConfigController extends AbstractController
{
    /**
     * @Route("/", name="deep_config_index", methods={"GET"})
     */
    public function index(DeepConfigRepository $deepConfigRepository): Response
    {
        return $this->render('deep_config/index.html.twig', [
            'deep_configs' => $deepConfigRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="deep_config_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $deepConfig = new DeepConfig();
        $form = $this->createForm(DeepConfigType::class, $deepConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($deepConfig);
            $entityManager->flush();

            return $this->redirectToRoute('deep_config_index');
        }

        return $this->render('deep_config/new.html.twig', [
            'deep_config' => $deepConfig,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="deep_config_show", methods={"GET"})
     */
    public function show(DeepConfig $deepConfig): Response
    {
        return $this->render('deep_config/show.html.twig', [
            'deep_config' => $deepConfig,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="deep_config_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DeepConfig $deepConfig): Response
    {
        $form = $this->createForm(DeepConfigType::class, $deepConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('deep_config_index');
        }

        return $this->render('deep_config/edit.html.twig', [
            'deep_config' => $deepConfig,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="deep_config_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DeepConfig $deepConfig): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deepConfig->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($deepConfig);
            $entityManager->flush();
        }

        return $this->redirectToRoute('deep_config_index');
    }
}
