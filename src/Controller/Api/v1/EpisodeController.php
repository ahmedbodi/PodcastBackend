<?php

namespace App\Controller\Api\v1;

use App\Controller\Api\ApiController;
use App\Entity\Episode;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
use Knp\Component\Pager\PaginatorInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use wapmorgan\MediaFile\MediaFile;

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

    /*
     * @var FilesystemInterface $defaultStorage Flysystem Storage Service
     */
    private $defaultStorage;

    /**
     * Inject the Serializer so we can use it to get a consistent output
     * @var SerializerInterface $serializer Serializer to convert entities to JSON
     * @var PaginatorInterface $paginator Paginator to limit results
     * @var FilesystemInterface $defaultStorage Flysystem Storage Service
     */
    public function __construct(SerializerInterface $serializer, PaginatorInterface $paginator, FilesystemInterface $defaultStorage)
    {
        $this->serializer = $serializer;
        $this->paginator = $paginator;
        $this->filesystem = $defaultStorage;
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
     * Update an existing episode from the database with the file passed in the request
     * @var Request $request HTTP PUT Request data
     * @var Episode $episode Fetched via ID provided inside the URL
     * @Route("/{id}/upload", name="episode_upload", methods={"POST"})
     */
    public function uploadFile(Request $request, Episode $episode): Response
    {
        $file = $request->files->get('file');

        if (!$file) {
            return $this->json([
                'success' => false,
                'errors' => ['Invalid File'],
            ]);
        }

        if ($this->filesystem->has($episode->getFilename())) {
            // Move it to the backups just incase.
            $newName = str_replace("episodes/", "backups/", $episode->getFilename());
            $this->filesystem->rename($episode->getFilename(), $newName);
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        // Upload to S3
        $stream = fopen($file->getRealPath(), 'r+');
        $filename = "episodes/" . $newFilename;
        $success = $this->filesystem->writeStream($filename, $stream);
        fclose($stream);

        // Get the File Path
        $url = $this->filesystem->getAdapter()->getClient()->getObjectUrl(
            $this->filesystem->getAdapter()->getBucket(),
            $filename
        );

        try {
            $media = MediaFile::open($file->getRealPath());
            $audioAdapter = $media->getAudio();

            // Get Metadata
            $episode->setTrackLength($audioAdapter->getLength());
            $episode->setBitRate($audioAdapter->getBitRate());
            $episode->setSampleRate($audioAdapter->getSampleRate());
            $episode->setChannels($audioAdapter->getChannels());
            $episode->setIsVariableBitRate($audioAdapter->isVariableBitRate());
            $episode->setIsLossless($audioAdapter->isLossless());
        } catch (\Exception $exc) {
            // We could raise an error here. but the library we're using just might not support the audio type.
            // So we'll avoid saving any metadata for now
        }

        // Update the Episode
        $episode->setFilename($filename);
        $episode->setDownloadUrl($url);
        $episode->setMimeType($this->filesystem->getMimetype($filename));
        $episode->setFileSize($this->filesystem->getSize($filename));

        // Save the Entity
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($episode);
        $entityManager->flush();

        return $this->json([
            'success' => $success,
            'result' => $this->serialize($episode),
        ]);
    }

    /**
     * Delete an existing episode
     * @var Request $request HTTP POST Request data
     * @var Episode $episode Fetched via ID provided inside the URL
     * @Route("/{id}/delete", name="episode_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Episode $episode): Response
    {
        if ($this->filesystem->has($episode->getFilename())) {
            // Move it to the backups just incase.
            $newName = str_replace("episodes/", "backups/", $episode->getFilename());
            $this->filesystem->rename($episode->getFilename(), $newName);
        }

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
