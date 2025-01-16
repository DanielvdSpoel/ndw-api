<?php

namespace App\Support;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Saloon\XmlWrangler\XmlReader;
use SimpleXMLElement;
use Str;
use Vyuldashev\XmlToArray\XmlToArray;

trait CollectsXML
{
    /**
     * Fetch the XML content from the given URL
     *
     * @param  string  $url
     * @return array
     */
    private function requestXML($url)
    {
        $response = Http::get($url);
        $file = Str::uuid().'.gz';
        Storage::disk('ndw')->put($file, $response->body());

        // Decompress the .gz file and parse the XML
        $xmlFile = $this->decompressGzFile($file);

        $stream = Storage::disk('ndw')->readStream($xmlFile);

        if (is_resource($stream)) {
            $content = stream_get_contents($stream);
            fclose($stream);
            try {
                return XmlToArray::convert($content);
            } catch (Exception $e) {
                ray($e->getMessage());
                throw new Exception('Failed to parse the XML content');
            }


        } else {
            throw new Exception('Failed to read the XML file');
        }

    }


    /**
     * Decompress the .gz file and return the content.
     *
     * @param  string  $filePath
     * @return string
     */
    private function decompressGzFile(string $filePath): string
    {
        // Get the full path of the .gz file
        $path = Storage::disk('ndw')->path($filePath);
        $bufferSize = 4096; // Read in chunks of 4KB

        // Generate the output XML file name (removing .gz extension)
        $outFileName = str_replace('.gz', '', $filePath);  // Path relative to disk
        $decompressedPath = Storage::disk('ndw')->path($outFileName); // Absolute path

        // Open the .gz file for reading
        $gzFile = gzopen($path, 'rb');
        $outFile = fopen($decompressedPath, 'wb');

        // Decompress the .gz file content and write to the new file
        while (!gzeof($gzFile)) {
            fwrite($outFile, gzread($gzFile, $bufferSize));
        }

        // Close the file handlers
        gzclose($gzFile);
        fclose($outFile);

        Storage::disk('ndw')->delete($filePath);

        // Return the relative path (within the storage disk) of the decompressed file
        return $outFileName;;
    }

}
