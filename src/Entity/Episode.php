<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @Groups({"rest"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $trackLength;

    /**
     * @Groups({"rest"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bitRate;

    /**
     * @Groups({"rest"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sampleRate;

    /**
     * @Groups({"rest"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $channels;

    /**
     * @Groups({"rest"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isVariableBitRate;

    /**
     * @Groups({"rest"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isLossless;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @Groups({"rest"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mimeType;

    /**
     * @Groups({"rest"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fileSize;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EpisodeDownloaded", mappedBy="episode", orphanRemoval=true)
     */
    private $episodeDownloads;

    public function __construct()
    {
        $this->episodeDownloads = new ArrayCollection();
    }

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

    public function getTrackLength(): ?int
    {
        return $this->trackLength;
    }

    public function setTrackLength(?int $trackLength): self
    {
        $this->trackLength = $trackLength;

        return $this;
    }

    public function getBitRate(): ?int
    {
        return $this->bitRate;
    }

    public function setBitRate(?int $bitRate): self
    {
        $this->bitRate = $bitRate;

        return $this;
    }

    public function getSampleRate(): ?int
    {
        return $this->sampleRate;
    }

    public function setSampleRate(?int $sampleRate): self
    {
        $this->sampleRate = $sampleRate;

        return $this;
    }

    public function getChannels(): ?int
    {
        return $this->channels;
    }

    public function setChannels(?int $channels): self
    {
        $this->channels = $channels;

        return $this;
    }

    public function getIsVariableBitRate(): ?bool
    {
        return $this->isVariableBitRate;
    }

    public function setIsVariableBitRate(?bool $isVariableBitRate): self
    {
        $this->isVariableBitRate = $isVariableBitRate;

        return $this;
    }

    public function getIsLossless(): ?bool
    {
        return $this->isLossless;
    }

    public function setIsLossless(?bool $isLossless): self
    {
        $this->isLossless = $isLossless;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): self
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * @return Collection|EpisodeDownloaded[]
     */
    public function getEpisodeDownloads(): Collection
    {
        return $this->episodeDownloads;
    }

    public function addEpisodeDownload(EpisodeDownloaded $episodeDownload): self
    {
        if (!$this->episodeDownloads->contains($episodeDownload)) {
            $this->episodeDownloads[] = $episodeDownload;
            $episodeDownload->setEpisode($this);
        }

        return $this;
    }

    public function removeEpisodeDownload(EpisodeDownloaded $episodeDownload): self
    {
        if ($this->episodeDownloads->contains($episodeDownload)) {
            $this->episodeDownloads->removeElement($episodeDownload);
            // set the owning side to null (unless already changed)
            if ($episodeDownload->getEpisode() === $this) {
                $episodeDownload->setEpisode(null);
            }
        }

        return $this;
    }
}
