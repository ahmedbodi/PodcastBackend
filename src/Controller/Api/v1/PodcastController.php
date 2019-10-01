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
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/v1/podcast")
 */
class PodcastController extends ApiController
{
    /**
     * @var SerializerInterface $serializer
     */
    private $serializer;

    /**
     * Inject the Serializer so we can use it to get a consistent output
     * @var SerializerInterface $serializer Serializer to convert entities to JSON
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * View an Individual Podcast as JSON
     * @var Podcast $podcast Fetched via ID provided inside the URL
     * @var SerializerInterface $serializer Serializer to convert entity to JSON (can be modified to support XML/CSV etc)
     * @Route("/{id}", name="podcast_view", methods={"GET"})
     */
    public function view(Podcast $podcast, SerializerInterface $serializer): Response
    {
        $result = $this->serialize($podcast);
        return $this->json([
            'success' => true,
            'result' => $result,
        ]);

    }

    /**
     * View an Individual Podcast as JSON
     * @var Podcast $podcast Fetched via ID provided inside the URL
     * @var SerializerInterface $serializer Serializer to convert entity to JSON (can be modified to support XML/CSV etc)
     * @Route("/", name="podcast_index", methods={"GET"})
     */
    public function index(PodcastRepository $podcastRepository, SerializerInterface $serializer): Response
    {
        $podcasts = $podcastRepository->findAll();
        $result = $this->serialize($podcasts);
        return $this->json([
            'success' => true,
            'result' => $result,
        ]);
    }

    /**
     * Create a new Podcast and store it in the database
     * @var Request $request HTTP PUT Request data
     * @var SerializerInterface $serializer Serializer to convert entity to JSON (can be modified to support XML/CSV etc)
     * @Route("/create", name="podcast_create", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer): Response
    {
        $podcast = new Podcast();
        $errors = $this->validateForm($request, $podcast);
        $output = ['success' => !$errors];

        if (!$errors) {
            $podcast = $this->processForm($request, $podcast);
            $output['result'] = $this->serialize($podcast);
        } else {
            $output['errors'] = $errors;
        }

        return $this->json($output);
    }

    /**
     * Update an existing podcast from the database
     * @var Request $request HTTP PUT Request data
     * @var Podcast $podcast Fetched via ID provided inside the URL
     * @var SerializerInterface $serializer Serializer to convert entity to JSON (can be modified to support XML/CSV etc)
     * @Route("/{id}/update", name="podcast_update", methods={"PUT"})
     */
    public function update(Request $request, Podcast $podcast, SerializerInterface $serializer): Response
    {
        $errors = $this->validateForm($request, $podcast);
        $output = ['success' => !$errors];

        if (!$errors) {
            $podcast = $this->processForm($request, $podcast);
            $output['result'] = $this->serialize($podcast);
        } else {
            $output['errors'] = $errors;
        }

        return $this->json($output);
    }

    /**
     * Delete an existing podcast
     * @var Request $request HTTP POST Request data
     * @var Podcast $podcast Fetched via ID provided inside the URL
     * @Route("/{id}/delete", name="podcast_delete", methods={"DELETE"})
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

    /**
     * Should be used by all actions to ensure we get a consistent response
     * @var entities array|Podcast Entities to serialize into an array
     * @return array
     */
    protected function serialize($entities) : array
    {
        $result = $this->serializer->normalize($entities, null, [
            'circular_reference_handler' => function ($obj) {
                return $obj->getId();
            },
            'groups' => [self::REST_SERIALIZER_ENTITY_GROUP, "episodes"] // We want to include it here but not in episode responses
        ]);
        return $result;
    }

    /**
     * Validate a PUT/POST Request Sent by the user
     * @var Request $request HTTP POST Request data
     * @var Podcast $podcast Fetched via ID provided inside the URL
     */
    protected function validateForm(Request $request, Podcast $podcast): array
    {
        if ($request->headers->get('content-type') == 'application/json') {
            $data = json_decode($request->getContent(), true);
        } else {
            $data = $request->request->all();
        }
        $form = $this->createForm(PodcastType::class, $podcast);
        $clearMissing = $request->getMethod() == "POST";
        $form->submit($data, $clearMissing);
        return $this->getErrorsFromForm($form);

    }

    /**
     * Process the POST/PUT Request against the provided podcast and return an updated copy
     * @var Request $request HTTP POST Request data
     * @var Podcast $podcast Fetched via ID provided inside the URL
     */
    protected function processForm(Request $request, Podcast $podcast): Podcast
    {
        if ($request->headers->get('content-type') == 'application/json') {
            $data = json_decode($request->getContent(), true);
        } else {
            $data = $request->request->all();
        }

        if (!$data) {
            // Dont waste a query on an empty update
            return $podcast;
        }

        $form = $this->createForm(PodcastType::class, $podcast);
        $clearMissing = $request->getMethod() == "POST";
        $form->submit($data, $clearMissing);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($podcast);
        $entityManager->flush();
        return $podcast;
    }
}
