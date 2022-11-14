<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $title = null;

    #[ORM\OneToMany(mappedBy: 'album_id', targetEntity: Mp3File::class)]
    private Collection $mp3Files;

    public function __construct()
    {
        $this->mp3Files = new ArrayCollection();
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

    /**
     * @return Collection<int, Mp3File>
     */
    public function getMp3Files(): Collection
    {
        return $this->mp3Files;
    }

    public function addMp3File(Mp3File $mp3File): self
    {
        if (!$this->mp3Files->contains($mp3File)) {
            $this->mp3Files->add($mp3File);
            $mp3File->setAlbumId($this);
        }

        return $this;
    }

    public function removeMp3File(Mp3File $mp3File): self
    {
        if ($this->mp3Files->removeElement($mp3File)) {
            // set the owning side to null (unless already changed)
            if ($mp3File->getAlbumId() === $this) {
                $mp3File->setAlbumId(null);
            }
        }

        return $this;
    }
}
