<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportService
{
    /**
     * Export a query to CSV with streaming response.
     *
     * @param Builder $query
     * @param array $columns ['db_field' => 'CSV Header Label']
     * @param string $filename
     * @param callable|null $rowTransformer Optional row transformer
     * @return StreamedResponse
     */
    public function export(Builder $query, array $columns, string $filename, ?callable $rowTransformer = null): StreamedResponse
    {
        return new StreamedResponse(function () use ($query, $columns, $rowTransformer) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Headers
            fputcsv($handle, array_values($columns), ';');

            $fields = array_keys($columns);

            // Stream in chunks to avoid memory issues
            $query->chunk(500, function (Collection $records) use ($handle, $fields, $rowTransformer) {
                foreach ($records as $record) {
                    if ($rowTransformer) {
                        $row = $rowTransformer($record);
                    } else {
                        $row = [];
                        foreach ($fields as $field) {
                            $row[] = data_get($record, $field);
                        }
                    }
                    fputcsv($handle, $row, ';');
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}
