<?php

namespace App\Controller\Api\v1;

use App\Controller\Api\ApiController;
use App\Entity\Episode;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/episode")
 */
class EpisodeController extends ApiController
{
    /**
     * @Route("/", name="episode_index", methods={"GET"})
     */
    public function index(EpisodeRepository $episodeRepository): Response
    {
        return $this->json([
            'success' => true,
            'result' => $episodeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/create", name="episode_create", methods={"PUT"})
     */
    public function create(Request $request): Response
    {
        $episode = new Episode();
        return $this->processForm($request, $episode);
    }

    /**
     * @Route("/{id}", name="episode_view", methods={"GET"})
     */
    public function view(Episode $episode): Response
    {
        return $this->json([
            'success' => true,
            'result' => $episode,
        ]);
    }

    /**
     * @Route("/{id}/update", name="episode_upda1te", methods={"POST"})
     */
    public function update(Request $request, Episode $episode): Response
    {
        return $this->processForm($request, $episode);
    }

    /**
     * @Route("/{id}", name="episode_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Episode $episode): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($episode);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'result' => null,
        ]);
    }

    public function processForm(Request $request, Episode $episode): Response
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(EpisodeType::class, $episode);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return new JsonResponse([
                'success' => false,
                'errors' => $errors,
            ], 400);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($episode);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'result' => $episode,
        ]);
    }
}
