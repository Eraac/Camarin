<?php

namespace CoreBundle\Service;

class ParseCSV
{
    /**
     * @param string $filename
     * @param string $separator
     * @param bool|true $ignoreFirstLine
     * @return array
     */
    public function parse($filename, $separator = ',', $ignoreFirstLine = true)
    {
        $csv = $this->getFile($filename);
        $i = 0;

        $rows = array();

        while (($data = fgetcsv($csv, null, $separator)) !== false)
        {
            $i++;

            if ($ignoreFirstLine && $i == 1) {
                continue;
            }

            $rows[] = $data;
        }

        fclose($csv);

        return $rows;
    }
    /**
     * @param string $filename
     * @return resource
     */
    private function getFile($filename)
    {
        $csvString = file_get_contents($filename);
        $tmp = tmpfile();

        fwrite($tmp, $csvString);
        fseek($tmp, 0);

        return $tmp;
    }
}
