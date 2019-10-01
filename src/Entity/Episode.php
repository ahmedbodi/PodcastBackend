<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="episode")
 * @ORM\Entity(repositoryClass="App\Repository\EpisodeRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Episode
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
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "The title must be at least {{ limit }} characters long",
     *      maxMessage = "The title cannot be longer than {{ limit }} characters"
     * )
     * @Groups({"rest"})
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Groups({"rest"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @Assert\NotBlank
     * @Assert\Positive
     * @Groups({"rest"})
     * @ORM\Column(type="integer")
     */
    private $episodeNumber;

    /**
     * @Assert\NotBlank
     * @Groups({"rest"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Podcast", inversedBy="episodes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $podcast;

    /**
     * @Assert\Url(
     *    message = "The url '{{ value }}' is not a valid url",
     * )
     * @Groups({"rest"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $downloadUrl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEpisodeNumber(): ?int
    {
        return $this->episodeNumber;
    }

    public function setEpisodeNumber(int $episodeNumber): self
    {
        $this->episodeNumber = $episodeNumber;

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

    public function getDownloadUrl(): ?string
    {
        return $this->downloadUrl;
    }

    public function setDownloadUrl(?string $downloadUrl): self
    {
        $this->downloadUrl = $downloadUrl;

        return $this;
    }
}
