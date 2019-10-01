<?php

namespace App\Controller\Api\v1;

use App\Controller\Api\ApiController;
use App\Entity\Episode;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/v1/episode")
 */
class EpisodeController extends ApiController
{
    /**
     * @var SerializerInterface $serializer
     */
    private $serializer;

    /**
     * @var PaginatorInterface $paginator
     */
    private $paginator;

    /**
     * Inject the Serializer so we can use it to get a consistent output
     * @var SerializerInterface $serializer Serializer to convert entities to JSON
     * @var PaginatorInterface $paginator Paginator to limit results
     */
    public function __construct(SerializerInterface $serializer, PaginatorInterface $paginator)
    {
        $this->serializer = $serializer;
        $this->paginator = $paginator;
    }

    /**
     * View an Individual Episode as JSON
     * @var Episode $episode Fetched via ID provided inside the URL
     * @var SerializerInterface $serializer Serializer to convert entity to JSON (can be modified to support XML/CSV etc)
     * @Route("/{id}", name="episode_view", methods={"GET"})
     */
    public function view(Episode $episode, SerializerInterface $serializer): Response
    {
        $result = $this->serialize($episode);
        return $this->json([
            'success' => true,
            'result' => $result,
        ]);

    }

    /**
     * View an Individual Episode as JSON
     * @var Episode $episode Fetched via ID provided inside the URL
     * @var SerializerInterface $serializer Serializer to convert entity to JSON (can be modified to support XML/CSV etc)
     * @var Request $request HTTP Request data
     * @Route("/", name="episode_index", methods={"GET"})
     */
    public function index(EpisodeRepository $episodeRepository, SerializerInterface $serializer, Request $request): Response
    {
        $query = $episodeRepository->createQueryBuilder('e');
        $paginatedResults = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        $result = $this->serialize($paginatedResults);
        return $this->json([
            'success' => true,
            'result' => $result,
        ]);
    }

    /**
     * Create a new Episode and store it in the database
     * @var Request $request HTTP PUT Request data
     * @var SerializerInterface $serializer Serializer to convert entity to JSON (can be modified to support XML/CSV etc)
     * @Route("/create", name="episode_create", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer): Response
    {
        $episode = new Episode();
        $errors = $this->validateForm($request, $episode);
        $output = ['success' => !$errors];

        if (!$errors) {
            $episode = $this->processForm($request, $episode);
            $output['result'] = $this->serialize($episode);
        } else {
            $output['errors'] = $errors;
        }

        return $this->json($output);
    }

    /**
     * Update an existing episode from the database
     * @var Request $request HTTP PUT Request data
     * @var Episode $episode Fetched via ID provided inside the URL
     * @var SerializerInterface $serializer Serializer to convert entity to JSON (can be modified to support XML/CSV etc)
     * @Route("/{id}/update", name="episode_update", methods={"PUT"})
     */
    public function update(Request $request, Episode $episode, SerializerInterface $serializer): Response
    {
        $errors = $this->validateForm($request, $episode);
        $output = ['success' => !$errors];

        if (!$errors) {
            $episode = $this->processForm($request, $episode);
            $output['result'] = $this->serialize($episode);
        } else {
            $output['errors'] = $errors;
        }

        return $this->json($output);
    }

    /**
     * Delete an existing episode
     * @var Request $request HTTP POST Request data
     * @var Episode $episode Fetched via ID provided inside the URL
     * @Route("/{id}/delete", name="episode_delete", methods={"DELETE"})
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

    /**
     * Should be used by all actions to ensure we get a consistent response
     * @var entities array|Episode Entities to serialize into an array
     * @return array
     */
    protected function serialize($entities) : array
    {
        $result = $this->serializer->normalize($entities, null, [
            'circular_reference_handler' => function ($obj) {
                return $obj->getId();
            },
            'groups' => [self::REST_SERIALIZER_ENTITY_GROUP]
        ]);
        return $result;
    }

    /**
     * Validate a PUT/POST Request Sent by the user
     * @var Request $request HTTP POST Request data
     * @var Episode $episode Fetched via ID provided inside the URL
     */
    protected function validateForm(Request $request, Episode $episode): array
    {
        if ($request->headers->get('content-type') == 'application/json') {
            $data = json_decode($request->getContent(), true);
        } else {
            $data = $request->request->all();
        }
        $form = $this->createForm(EpisodeType::class, $episode);
        $clearMissing = $request->getMethod() == "POST";
        $form->submit($data, $clearMissing);
        return $this->getErrorsFromForm($form);

    }

    /**
     * Process the POST/PUT Request against the provided episode and return an updated copy
     * @var Request $request HTTP POST Request data
     * @var Episode $episode Fetched via ID provided inside the URL
     */
    protected function processForm(Request $request, Episode $episode): Episode
    {
        if ($request->headers->get('content-type') == 'application/json') {
            $data = json_decode($request->getContent(), true);
        } else {
            $data = $request->request->all();
        }

        if (!$data) {
            // Dont waste a query on an empty update
            return $episode;
        }

        $form = $this->createForm(EpisodeType::class, $episode);
        $clearMissing = $request->getMethod() == "POST";
        $form->submit($data, $clearMissing);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($episode);
        $entityManager->flush();
        return $episode;
    }
}
