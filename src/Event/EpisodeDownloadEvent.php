<?php

namespace App\Event;

use App\Entity\Episode;
use App\Entity\Podcast;
use Symfony\Contracts\EventDispatcher\Event;

class EpisodeDownloadEvent extends Event
{
    public const NAME = 'episode.downloaded';

    /**
     * @var Episode $episode Episode That was Downloaded
     */
    protected $episode;

    /**
     * @var Podcast $podcast Podcast the Episode Belonged To
     */
    protected $podcast;

    /**
     * @var \DateTimeImmutable Time the episode was downloaded
     */
    protected $occouredAt;

    /**
     * Constructor containing the event parameters
     * @var Episode $episode Episode that was downloaded
     * @var Podcast $podcast Podcast the Episode Belonged To
     * @var \DateTimeImmutable $occouredAt Time the Download Occoured
     */
    public function __construct(Episode $episode, Podcast $podcast, \DateTimeImmutable $occouredAt)
    {
        $this->episode = $episode;
        $this->podcast = $podcast;
        $this->occouredAt = $occouredAt;
    }

    public function getEpisode(): Episode
    {
        return $this->episode;
    }

    public function getPodcast(): Podcast
    {
        return $this->podcast;
    }

    public function getOccouredAt(): \DateTimeImmutable
    {
        return $this->occouredAt;
    }
}
