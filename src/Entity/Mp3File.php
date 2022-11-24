<?php

namespace App\Entity;

use App\Repository\Mp3FileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: Mp3FileRepository::class)]
class Mp3File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $filename;

    #[ORM\Column(length: 1024, nullable: true)]
    private string $ufid_owner;

    #[ORM\Column(length: 1024, nullable: true)]
    private string $ufid_identifier;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $album_name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $author = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $albumAuthor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $track = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $year = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $length = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $lyric = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $genre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $encoded = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $copyright = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $publisher = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $original_artist = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comments = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $composer = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $album_art = null;

    #[ORM\ManyToOne(inversedBy: 'mp3Files')]
    private ?Album $album_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getUfidOwner(): string
    {
        return $this->ufid_owner;
    }

    /**
     * @param string $ufid_owner
     */
    public function setUfidOwner(string $ufid_owner): void
    {
        $this->ufid_owner = $ufid_owner;
    }

    /**
     * @return string
     */
    public function getUfidIdentifier(): string
    {
        return $this->ufid_identifier;
    }

    /**
     * @param string $ufid_identifier
     */
    public function setUfidIdentifier(string $ufid_identifier): void
    {
        $this->ufid_identifier = $ufid_identifier;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAlbumName(): ?string
    {
        return $this->album_name;
    }

    public function setAlbumName(?string $album_name): self
    {
        $this->album_name = $album_name;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAlbumAuthor(): ?string
    {
        return $this->albumAuthor;
    }

    /**
     * @param string|null $albumAuthor
     */
    public function setAlbumAuthor(?string $albumAuthor): void
    {
        $this->albumAuthor = $albumAuthor;
    }

    public function getTrack(): ?string
    {
        return $this->track;
    }

    public function setTrack(?string $track): self
    {
        $this->track = $track;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getLength(): ?string
    {
        return $this->length;
    }

    public function setLength(?string $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getLyric(): ?string
    {
        return $this->lyric;
    }

    public function setLyric(?string $lyric): self
    {
        $this->lyric = $lyric;

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

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getEncoded(): ?string
    {
        return $this->encoded;
    }

    public function setEncoded(?string $encoded): self
    {
        $this->encoded = $encoded;

        return $this;
    }

    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    public function setCopyright(string $copyright): self
    {
        $this->copyright = $copyright;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getOriginalArtist(): ?string
    {
        return $this->original_artist;
    }

    public function setOriginalArtist(?string $original_artist): self
    {
        $this->original_artist = $original_artist;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getComposer(): ?string
    {
        return $this->composer;
    }

    public function setComposer(?string $composer): self
    {
        $this->composer = $composer;

        return $this;
    }

    public function getAlbumArt()
    {
        return $this->album_art;
    }

    public function setAlbumArt($album_art): self
    {
        $this->album_art = $album_art;

        return $this;
    }

    public function getAlbumId(): ?Album
    {
        return $this->album_id;
    }

    public function setAlbumId(?Album $album_id): self
    {
        $this->album_id = $album_id;

        return $this;
    }
}
