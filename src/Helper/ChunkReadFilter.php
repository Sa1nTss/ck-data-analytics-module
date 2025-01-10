<?php

namespace App\Helper;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ChunkReadFilter implements IReadFilter
{
    private int $_startRow = 0;
    private int $_endRow = 0;

    public function setRows($startRow, $chunkSize): void
    {
        $this->_startRow = $startRow;
        $this->_endRow = $startRow + $chunkSize;
    }

    public function readCell($columnAddress, $row, $worksheetName = ''): bool
    {
        if ((1 == $row) || ($row >= $this->_startRow && $row < $this->_endRow)) {
            return true;
        }

        return false;
    }
}
