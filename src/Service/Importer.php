<?php

namespace App\Service;

use App\ID3TagsReader;
use Exception;

class Importer
{
    private ID3TagsReader $reader;

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

    public function import(string $pathname)
    {
        $this->scanDirectory($pathname);
    }


    private function scanDirectory(string $dir)
    {
        $entries = scandir($dir);
        foreach ($entries as $entry) {
            if (in_array($entry, ['.', '..'])) {
                continue;
            }
            $pathname = implode(DIRECTORY_SEPARATOR, [$dir, $entry]);
            if (is_dir($pathname)) {
                $this->scanDirectory($pathname);
            } elseif (is_file($pathname)) {
                $tags = $this->scanFile($pathname);
                $this->importTags($tags);
            } else {
//                throw new \Exception( 'What?!');
                continue;
            }
        }
    }


    public function scanFile(string $pathname): array
    {
        $tags = [];
        try {

            try {
                $id3v2tag = $this->getReader()->readId3v2Tag($pathname);
            } catch (Exception $e) {
                return [];
            }

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

        } catch (Exception $e) {

        }

        return $tags;
    }

    private function importTags(array $tags)
    {
        return;
    }
}
