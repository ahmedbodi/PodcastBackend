<?php

namespace App\Controller\Api\v1;

use App\Controller\Api\ApiController;
use App\Entity\Podcast;
use App\Form\PodcastType;
use App\Repository\PodcastRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/podcast")
 */
class PodcastController extends ApiController
{
    /**
     * @Route("/", name="podcast_index", methods={"GET"})
     */
    public function index(PodcastRepository $podcastRepository): Response
    {
        return $this->json([
            'success' => true,
            'result' => $podcastRepository->findAll(),
        ]);
    }

    /**
     * @Route("/create", name="podcast_create", methods={"PUT"})
     */
    public function create(Request $request): Response
    {
        $podcast = new Podcast();
        return $this->processForm($request, $podcast);
    }

    /**
     * @Route("/{id}", name="podcast_view", methods={"GET"})
     */
    public function view(Podcast $podcast): Response
    {
        return $this->json([
            'success' => true,
            'result' => $podcast,
        ]);
    }

    /**
     * @Route("/{id}/update", name="podcast_upda1te", methods={"POST"})
     */
    public function update(Request $request, Podcast $podcast): Response
    {
        return $this->processForm($request, $podcast);
    }

    /**
     * @Route("/{id}", name="podcast_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Podcast $podcast): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($podcast);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'result' => null,
        ]);
    }

    public function processForm(Request $request, Podcast $podcast): Response
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(PodcastType::class, $podcast);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return new JsonResponse([
                'success' => false,
                'errors' => $errors,
            ], 400);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($podcast);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'result' => $podcast,
        ]);
    }
}
