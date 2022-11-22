<?php

namespace App;

use Exception;

class ID3TagsReader
{
    const TAG_HEADER_LENGTH = 10;
    const FRAME_HEADER_LENGTH = 10;

    // variables
    var $aTV23 = array( // array of possible sys tags (for last version of ID3)
        'TIT2',
        'TALB',
        'TPE1',
        'TPE2',
        'TRCK',
        'TYER',
        'TLEN',
        'USLT',
        'TPOS',
        'TCON',
        'TENC',
        'TCOP',
        'TPUB',
        'TOPE',
        'WXXX',
        'COMM',
        'TCOM'
    );
    var $aTV23t = array( // array of titles for sys tags
        'Title',
        'Album',
        'Author',
        'AlbumAuthor',
        'Track',
        'Year',
        'Length',
        'Lyric',
        'Desc',
        'Genre',
        'Encoded',
        'Copyright',
        'Publisher',
        'OriginalArtist',
        'URL',
        'Comments',
        'Composer'
    );
    var $aTV22 = array( // array of possible sys tags (for old version of ID3)
        'TT2',
        'TAL',
        'TP1',
        'TRK',
        'TYE',
        'TLE',
        'ULT'
    );
    var $aTV22t = array( // array of titles for sys tags
        'Title',
        'Album',
        'Author',
        'Track',
        'Year',
        'Lenght',
        'Lyric'
    );

    // constructor
    function __construct()
    {
    }

    /**
     * @param resource $fd File descriptor of opened MP3 file
     * @return array
     * @throws Exception
     *
     * Read the MP3 ID3v2 header from an opened file
     */
    function readId3Header($fd): array
    {
        // Read the first part of the ID3v2 tag, which is the 10 byte tag header
        if (false === ($src = fread($fd, self::TAG_HEADER_LENGTH))) {
            throw new Exception(sprintf('Cannot read tag header in MP3 file'));
        }

        $format = 'c3id/cvMaj/cvMin/cflags/Nsize';
        $unpacked = unpack($format, $src);   // Unpack the raw data into a simple, intermediate, temporary associative array

        // Convert the intermediate array into a more useful header structure.
        // In particular, convert the bit mask "flags" into individually-named flags
        $header = [
            'identifier' => chr($unpacked['id1']) . chr($unpacked['id2']) . chr($unpacked['id3']),
            'vMaj' => $unpacked['vMaj'],
            'vMin' => $unpacked['vMin'],
            'fUnsynch' => $unpacked['flags'] & 0x80,
            'fExtHdr' => $unpacked['flags'] & 0x40,
            'fExp' => $unpacked['flags'] & 0x20,
            'fFooter' => $unpacked['flags'] & 0x10,
            'fUndef' => $unpacked['flags'] & 0x0f,
            'size' => $unpacked['size']
        ];

        // The first three bytes of the tag are always "ID3", to indicate that this is an ID3v2 tag
        if ('ID3' !== ($header['identifier'])) {
            throw new Exception(sprintf('File missing ID3v2 file identifier'));
        }

        // directly followed by the two version bytes.
        // The first byte of ID3v2 version is its major version,
        if (3 != $header['vMaj']) {
            throw new Exception(sprintf('Unexpected ID3v2 major version number "%d"', $header['vMaj']));
        }

        // while the second byte is its revision number
        if (0 != $header['vMin']) {
            throw new Exception(sprintf('Unexpected ID3v2 revision number "%d"', $header['vMin']));
        }

        if ($header['fUndef']) {
            throw new Exception(sprintf('Uncleared flags'));
        }

        return $header;
    }


    /**
     * @param resource $fd
     * @return ?array
     * @throws Exception
     */
    public function readNextFrame($fd): ?array
    {
        // All ID3v2 frames [consist] of one frame header...
        if (false === ($src = fread($fd, self::FRAME_HEADER_LENGTH))) {
            throw new Exception(sprintf('Cannot read frame header'));
        }
        $unpacked = unpack('c4id/Nsize/nflags', $src);
        $frame = [
            'identifier' => chr($unpacked['id1']) . chr($unpacked['id2']) . chr($unpacked['id3']) . chr($unpacked['id4']),
            'size' => $unpacked['size'],
            'flags' => $unpacked['flags']
        ];

        if (false === ($b = preg_match('/[A-Z0-9]{4}/', $frame['identifier']))) {
            throw new Exception(sprintf('Failure matching frame identifier "%s"', $frame['identifier']));
        }
        if (0 === $b) return null;
        if (0 == $unpacked['size']) return null;


        // ... followed by one or more fields containing the actual information
        if (false === ($data = fread($fd, $unpacked['size']))) {
            throw new Exception(sprintf('Cannot read data encoding'));
        }
        if (('T' == chr($unpacked['id1'])) && ('TXXX' !== $frame['identifier'])) {
            $enc = unpack('Cenc', $data);
            switch ($enc['enc']) {
                case 0:
                    $frame[ 'data' ] = $this->decode0(substr($data, 1));
                    break;

                case 1:
                    $frame[ 'data' ] = $this->decode1(substr($data, 1));
                    break;

                case 2:
                    // UTF-16BE-encoded Unicode without BOM.
                    throw new Exception(sprintf('Unhandled character encoding "%d" (%s)', $enc['enc'], mb_detect_encoding($src)));

                case 3:
                    // UTF-8 encoded Unicode
                    throw new Exception(sprintf('Unhandled character encoding "%d" (%s)', $enc['enc'], mb_detect_encoding($src)));

                default:
                    $data = '';
                    break;
            }

        } elseif ( 'TXXX' === $frame[ 'identifier']) {
            $frame[ 'data' ] = 'TXXX';

        } elseif ( 'COMM' === $frame[ 'identifier']) {
            // 4.10
            $enc = unpack('Cenc', $data);
            $comment = [
                'lang' => substr($data, 1, 3 )
            ];
            switch ($enc['enc']) {
                case 0:
                    $comment[ 'comment' ] = $this->decode0(substr($data, 4));
                    break;

                case 1:
                    $comment[ 'comment' ] = $this->decode1(substr($data, 4));
                    break;

                default:
                    break;
            }
            $frame['data'] = $comment;

        } else {
            $frame[ 'data' ] = '';
        }

        return $frame;
    }


    /**
     * @param string $filepath
     * @return array The ID3v2 information from the specified file
     * @throws Exception
     *
     * Open a file on disk and attempt to read the ID3v2 tag information.
     * The tag is returned as an associative array of header and an array of frames.
     * Each frame contains a 4-character string identifier, associated data, and some meta-data.
     */
    public function readId3v2Tag(string $filepath): array
    {
        $id3v2tag = [];
        if (false === ($fd = fopen($filepath, 'r'))) {
            throw new Exception(sprintf('Cannot open file "%s"', $filepath));
        }

        $id3v2tag['header'] = $this->readId3Header($fd);
        if ($id3v2tag['header']['fExtHdr']) {
            // TODO: Read extended header
            $id3v2tag['extHdr'] = [
                'size' => null,
                'nFlagBytes' => null,
                'extFlags' => null
            ];
        }

        if ($id3v2tag['header']['fFooter']) {
            // TODO: Read footer
            $id3v2tag['footer'] = [
                'identifier' => null,
                'vMaj' => null,
                'vMin' => null,
                'fUnsynch' => null,
                'fExtHdr' => null,
                'fExp' => null,
                'fFooter' => null,
                'fUndef' => null,
                'size' => null
            ];
        }

        $id3v2tag['frames'] = [];
        while ($frame = $this->readNextFrame($fd)) {
            $id3v2tag['frames'][] = $frame;
        }

        return $id3v2tag;
    }

    // ISO-8859-1 [ISO-8859-1]. Terminated with $00.
    private function decode0(string $s) : string {
        if ( !mb_check_encoding($s)) {
            $s = iconv( 'ISO-8859-1', 'utf-8', $s );
        }
        return $s;
    }

    //  UCS-2-encoded unicode w/ byte order mark
    private function decode1(string $s) : string {
        $bom = unpack('C2bom', $s );
        if ((255 != $bom['bom1']) || (254 != $bom['bom2'])) {
            throw new Exception(sprintf('Unexpected BOM: %u, %u', $bom['utf1'], $bom['utf2']));
        }
        return substr($s, 2);
    }
}
