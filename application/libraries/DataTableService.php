<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class DataTableService
{
    protected $CI;
    protected $db;
    protected $query;
    protected $searchableFields = [];
    protected $rawSearchMap = [];

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->db = $this->CI->db;
    }

    public function initialize($query, $searchableFields = [])
    {
        $this->query = $query;
        $this->searchableFields = [];
        $this->rawSearchMap = [];

        foreach ($searchableFields as $key => $value) {
            if (is_string($key)) {
                $this->searchableFields[] = $value;
                $this->rawSearchMap[$key] = $value;
            } else {
                $this->searchableFields[] = $value;
            }
        }
    }

    public function getResponse($export = false, $exportType = 'csv')
    {
        $request = $_GET;

        $start = isset($request['first']) ? (int)$request['first'] : 0;
        $length = isset($request['rows']) ? (int)$request['rows'] : 10;
        $sortMeta = isset($request['multiSortMeta']) ? json_decode($request['multiSortMeta'], true) : null;
        $sortField = isset($request['sortField']) ? $request['sortField'] : null;
        $sortOrder = isset($request['sortOrder']) ? $request['sortOrder'] : null;
        $filters = isset($request['filters']) ? json_decode($request['filters'], true) : [];
        $globalFilter = isset($filters['global']['value']) ? $filters['global']['value'] : null;

        $builder = clone $this->query;

        if ($globalFilter && !empty($this->searchableFields)) {
            $builder->group_start();
            foreach ($this->searchableFields as $field) {
                $builder->or_like($field, $globalFilter);
            }
            $builder->group_end();
        }

        foreach ($filters as $field => $filter) {
            if ($field === 'global') continue;

            $value = isset($filter['value']) ? $filter['value'] : null;
            $matchMode = isset($filter['matchMode']) ? $filter['matchMode'] : 'contains';

            if ($value !== null && $value !== '') {
                $sqlField = isset($this->rawSearchMap[$field]) ? $this->rawSearchMap[$field] : $field;
                $this->applyFilter($builder, $sqlField, $value, $matchMode);
            }
        }

        $filteredBuilder = clone $builder;
        $filtered = $this->db->query("SELECT COUNT(*) AS numrows FROM (" . $filteredBuilder->get_compiled_select() . ") AS dt")->row()->numrows;

        if ($sortMeta && is_array($sortMeta)) {
            foreach ($sortMeta as $meta) {
                $field = $meta['field'];
                $order = $meta['order'] == 1 ? 'ASC' : 'DESC';
                $sqlField = isset($this->rawSearchMap[$field]) ? $this->rawSearchMap[$field] : $field;
                $builder->order_by($sqlField, $order);
            }
        } elseif ($sortField && $sortOrder !== null) {
            $direction = $sortOrder == 1 ? 'ASC' : 'DESC';
            $sqlField = isset($this->rawSearchMap[$sortField]) ? $this->rawSearchMap[$sortField] : $sortField;
            $builder->order_by($sqlField, $direction);
        }

        if (!$export) {
            $builder->limit($length, $start);
        }

        $results = $builder->get()->result();

        if ($export) {
            switch (strtolower($exportType)) {
                case 'excel':
                    $this->exportExcel($results);
                    break;
                case 'pdf':
                    // $this->exportPDF($results);
                    return $results;
                    break;
                case 'csv':
                default:
                    $this->exportCSV($results);
                    break;
            }
            exit;
        }

        return [
            'data' => $results,
            'totalRecords' => (int)$filtered
        ];
    }

    protected function applyFilter(&$builder, $field, $value, $matchMode)
    {
        switch ($matchMode) {
            case 'startsWith':
                $builder->like($field, $value, 'after');
                break;
            case 'endsWith':
                $builder->like($field, $value, 'before');
                break;
            case 'equals':
                $builder->where($field, $value);
                break;
            case 'notEquals':
                $builder->where("{$field} !=", $value);
                break;
            case 'in':
                $builder->where_in($field, is_array($value) ? $value : explode(',', $value));
                break;
            case 'lt':
                $builder->where("{$field} <", $value);
                break;
            case 'lte':
                $builder->where("{$field} <=", $value);
                break;
            case 'gt':
                $builder->where("{$field} >", $value);
                break;
            case 'gte':
                $builder->where("{$field} >=", $value);
                break;
            case 'dateIs':
                $builder->where("DATE({$field}) =", date('Y-m-d', strtotime($value)));
                break;
            case 'dateBefore':
                $builder->where("{$field} <", date('Y-m-d', strtotime($value)));
                break;
            case 'dateAfter':
                $builder->where("{$field} >", date('Y-m-d', strtotime($value)));
                break;
            case 'contains':
            default:
                $builder->like($field, $value);
                break;
        }
    }

    protected function exportCSV($data)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="export.csv"');

        $output = fopen('php://output', 'w');
        if (!empty($data)) {
            fputcsv($output, array_keys((array)$data[0]));
            foreach ($data as $row) {
                fputcsv($output, (array)$row);
            }
        }
        fclose($output);
    }

    protected function exportExcel($data)
    {
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if (!empty($data)) {
            $sheet->fromArray(array_keys((array)$data[0]), null, 'A1');
            $rowIndex = 2;
            foreach ($data as $row) {
                $sheet->fromArray(array_values((array)$row), null, 'A' . $rowIndex);
                $rowIndex++;
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="export.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    protected function exportPDF($data)
    {
        $html = '<table border="1" cellpadding="4"><thead><tr>';
        if (!empty($data)) {
            foreach (array_keys((array)$data[0]) as $heading) {
                $html .= '<th>' . htmlspecialchars($heading) . '</th>';
            }
            $html .= '</tr></thead><tbody>';

            foreach ($data as $row) {
                $html .= '<tr>';
                foreach ((array)$row as $cell) {
                    $html .= '<td>' . htmlspecialchars($cell) . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
        }

        $this->CI->pdf->loadHtml($html);
        $this->CI->pdf->render();
        $this->CI->pdf->stream("export.pdf", array("Attachment" => true));
    }
}
