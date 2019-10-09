<?php

namespace App\EventListener;

use App\Entity\EpisodeDownloaded;
use App\Event\EpisodeDownloadedEvent;
use Doctrine\ORM\EntityManagerInterface;

class EpisodeDownloadedListener
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    public $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(EpisodeDownloadedEvent $event)
    {
        var_dump($event);
        $episode = $event->getEpisode();
        $podcast = $event->getPodcast();
        $occouredAt = $event->getOccouredAt();

        // Save An EpisodeDownloaded Object to the db
        $download = new EpisodeDownload();
        $download->setEpisode($episode);
        $download->setPodcast($podcast);
        $download->setOccouredAt($occouredAt);

        var_dump($download);
        $this->entityManager->persist($download);
        $this->entityManager->flush();
    }
}
