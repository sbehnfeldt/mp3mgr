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

    public function importFile(string $filename): array
    {
        $tags = [];
        try {

            try {
                $id3v2tag = $this->getReader()->readId3v2Tag($filename);
            } catch (Exception $e) {
                return [];
            }

            $tags = [
                'Filename' => $filename
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
                $temp = explode(DIRECTORY_SEPARATOR, $filename);
                $artistName = $temp[count($temp) - 3];
            }
            // TODO: See if artist is already in database; insert if not


            if (empty($albumName)) {
                $temp = explode(DIRECTORY_SEPARATOR, $filename);
                $albumName = $temp[count($temp) - 2];
            }
            // TODO: See if album is already in adatabase; insert if not

        } catch (Exception $e) {

        }

        return $tags;
    }
}