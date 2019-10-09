<?php

namespace App\Tests\Event;

use App\Event\EpisodeDownloadEvent;
use App\EventListener\EpisodeDownloadedListener;
use App\Entity\Episode;
use App\Entity\EpisodeDownloaded;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EpisodeDownloadedEventTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private $dispatcher;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->dispatcher = $kernel->getContainer()
            ->get('debug.event_dispatcher');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }


    public function testHasListener()
    {
        $episode = $this->entityManager
            ->getRepository(Episode::class)
            ->findBy([], [], 1)[0];

        // Create the Event
        $time = new \DateTimeImmutable();
        $event = new EpisodeDownloadEvent($episode, $episode->getPodcast(), $time);

        // Dispatch it
        $this->dispatcher->dispatch($event, EpisodeDownloadEvent::NAME);

        // Find the EpisodeDownloaded Entity in the db
        $episodeDownload = $this->entityManager
            ->getRepository(EpisodeDownloaded::class)
            ->findOneBy([
                'episode' => $episode,
                'podcast' => $episode->getPodcast(),
                'occouredAt' => $time
            ]);

        $this->assertNotNull($episodeDownload);
    }
}
