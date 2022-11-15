<?php

namespace App\Service;

use App\ID3TagsReader;
use Exception;
use Psr\Log\LoggerInterface;

class Importer
{
    private ID3TagsReader $reader;

    private LoggerInterface $logger;

    private static array $tagNameMap = [
        'TIT2' => 'Title',
        'TALB' => 'Album',
        'TPE1' => 'Author',
        'TPE2' => 'AlbumAuthor',
        'TRCK' => 'Track',
        'TYER' => 'Year',
        'TLEN' => 'Length',
        'USLT' => 'Lyric',
        'TPOS' => 'Desc',
        'TCON' => 'Genre',
        'TENC' => 'Encoded',
        'TCOP' => 'Copyright',
        'TPUB' => 'Publisher',
        'TOPE' => 'OriginalArtist',
        'WXXX' => 'URL',
        'COMM' => 'Comments',
        'TCOM' => 'Composer',

        'APIC' => 'Album Art',
        'PRIV' => 'Private data'
    ];

    /**
     * @param ID3TagsReader $reader
     */
    public function __construct(ID3TagsReader $reader, LoggerInterface $logger)
    {
        $this->reader = $reader;
        $this->logger = $logger;
    }

    /**
     * @return ID3TagsReader
     */
    public function getReader(): ID3TagsReader
    {
        if (!isset($this->reader)) {
            $this->reader = new ID3TagsReader();
        }
        return $this->reader;
    }

    /**
     * @param ID3TagsReader $reader
     */
    public function setReader(ID3TagsReader $reader): void
    {
        $this->reader = $reader;
    }

    /**
     * @param string $dir
     * @return void
     *
     * Recursively import all MP3 files in the specified directory into the database.
     *
     * "Importing" will record the MP3 filename and all ID3v2 tags as a single database record.
     */
    public function import(string $dir)
    {
        $this->logger->info(sprintf('Importing directory "%s"', $dir));
        $this->scanDirectory($dir);
        $this->logger->info(sprintf('Import of directory "%s" complete', $dir));
    }


    private function scanDirectory(string $dir): void
    {
        $entries = scandir($dir);
        $this->logger->notice(sprintf('Scanning directory "%s"', $dir));
        foreach ($entries as $entry) {
            if (in_array($entry, ['.', '..'])) {
                continue;
            }
            $pathname = implode(DIRECTORY_SEPARATOR, [$dir, $entry]);
            if (is_dir($pathname)) {
                $this->scanDirectory($pathname);
            } elseif (is_file($pathname)) {
                try {
                    if (null !== ($tags = $this->scanFile($pathname))) {
                        $this->importTags($tags);
                    }
                } catch (Exception $e) {
                    $this->logger->warning(sprintf('Error scanning file "%s": %s"', $pathname, $e->getMessage()));
                }
            } else {
                $this->logger->warning(sprintf('Directory entry "%s" does not register as either a directory or file', $pathname));
            }
        }
        $this->logger->notice(sprintf('Scanning of directory "%s" complete', $dir));
    }


    public function scanFile(string $pathname): ?array
    {
        if ('mp3' !== strtolower(pathinfo($pathname, PATHINFO_EXTENSION))) {
            return null;
        }
        $id3v2tag = $this->getReader()->readId3v2Tag($pathname);
        $tags = [
            'Filename' => $pathname
        ];
        foreach ($id3v2tag['frames'] as $frame) {
            if (!array_key_exists($frame['identifier'], self::$tagNameMap)) {
                continue;
            }
            $tagName = self::$tagNameMap[$frame['identifier']];
            $tags[$tagName] = $frame['data'];
            if ('TALB' === $frame['identifier']) {
                $albumName = $frame['data'];
            } elseif ('TPE1' === $frame['identifier']) {
                $artistName = $frame['data'];
            }
        }
        if (empty($artistName)) {
            $temp = explode(DIRECTORY_SEPARATOR, $pathname);
            $artistName = $temp[count($temp) - 3];
        }
        // TODO: See if artist is already in database; insert if not


        if (empty($albumName)) {
            $temp = explode(DIRECTORY_SEPARATOR, $pathname);
            $albumName = $temp[count($temp) - 2];
        }
        // TODO: See if album is already in adatabase; insert if not


        return $tags;
    }

    /**
     * @param array $tags
     * @return void
     *
     * TODO: Write the tags to the database.
     */
    private function importTags(array $tags)
    {
        return;
    }
}
