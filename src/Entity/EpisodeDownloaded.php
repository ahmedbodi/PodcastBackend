<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EpisodeDownloadedRepository")
 */
class EpisodeDownloaded
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $eventId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Episode", inversedBy="episodeDownloads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $episode;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Podcast", inversedBy="episodesDownloaded")
     * @ORM\JoinColumn(nullable=false)
     */
    private $podcast;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $occouredAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventId(): ?string
    {
        return $this->eventId;
    }

    public function setEventId(string $eventId): self
    {
        $this->eventId = $eventId;

        return $this;
    }

    public function getEpisode(): ?Episode
    {
        return $this->episode;
    }

    public function setEpisode(?Episode $episode): self
    {
        $this->episode = $episode;

        return $this;
    }

    public function getPodcast(): ?Podcast
    {
        return $this->podcast;
    }

    public function setPodcast(?Podcast $podcast): self
    {
        $this->podcast = $podcast;

        return $this;
    }

    public function getOccouredAt(): ?\DateTimeImmutable
    {
        return $this->occouredAt;
    }

    public function setOccouredAt(\DateTimeImmutable $occouredAt): self
    {
        $this->occouredAt = $occouredAt;

        return $this;
    }
}
