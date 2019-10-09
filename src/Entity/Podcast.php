<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="podcast")
 * @ORM\Entity(repositoryClass="App\Repository\PodcastRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Podcast
{
    /*
     * Adds createdAt and updatedAt fields/methods to this class.
     * Updates triggered by Doctrine Lifecycle Callbacks
     */
    use Timestampable;

    /**
     * @Groups({"rest"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"rest"})
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "The name must be at least {{ limit }} characters long",
     *      maxMessage = "The name cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Groups({"episodes"})
     * @ORM\OneToMany(targetEntity="App\Entity\Episode", mappedBy="podcast", orphanRemoval=true)
     */
    private $episodes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EpisodeDownloaded", mappedBy="podcast", orphanRemoval=true)
     */
    private $episodesDownloaded;

    public function __construct()
    {
        $this->episodes = new ArrayCollection();
        $this->episodesDownloaded = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Episode[]
     */
    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(Episode $episode): self
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes[] = $episode;
            $episode->setPodcast($this);
        }

        return $this;
    }

    public function removeEpisode(Episode $episode): self
    {
        if ($this->episodes->contains($episode)) {
            $this->episodes->removeElement($episode);
            // set the owning side to null (unless already changed)
            if ($episode->getPodcast() === $this) {
                $episode->setPodcast(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EpisodeDownloaded[]
     */
    public function getEpisodesDownloaded(): Collection
    {
        return $this->episodesDownloaded;
    }

    public function addEpisodeDownloaded(EpisodeDownloaded $episodesDownloaded): self
    {
        if (!$this->episodesDownloaded->contains($episodesDownloaded)) {
            $this->episodesDownloaded[] = $episodesDownloaded;
            $episodesDownloaded->setPodcast($this);
        }

        return $this;
    }

    public function removeEpisodeDownloaded(EpisodeDownloaded $episodesDownloaded): self
    {
        if ($this->episodesDownloaded->contains($episodesDownloaded)) {
            $this->episodesDownloaded->removeElement($episodesDownloaded);
            // set the owning side to null (unless already changed)
            if ($episodesDownloaded->getPodcast() === $this) {
                $episodesDownloaded->setPodcast(null);
            }
        }

        return $this;
    }
}
