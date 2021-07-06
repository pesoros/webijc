<?php

namespace Modules\Product\Repositories;

use Modules\Product\Entities\UnitType;
use Importer;
use Response;

class UnitTypeRepository implements UnitTypeRepositoryInterface
{
    public function all()
    {
        return UnitType::orderBy("id", "DESC")->get();
    }

    public function serachBased($search_keyword)
    {
        return UnitType::whereLike(['name', 'description'], $search_keyword)->get();
    }

    public function create(array $data)
    {
        $variant = new UnitType();
        $variant->fill($data)->save();
    }

    public function find($id)
    {
        return UnitType::findOrFail($id);
    }

    public function update(array $data, $id)
    {

        $variant = UnitType::findOrFail($id);
        $variant->update($data);
    }

    public function delete($id)
    {
        return UnitType::findOrFail($id)->delete();
    }

    public function csv_upload_unit($data)
    {
        if (!empty($data['file'])) {
            ini_set('max_execution_time', 0);
            $a = $data['file']->getRealPath();
            $column_name = Importer::make('Excel')->load($a)->getCollection()->take(1)->first();
            foreach (Importer::make('Excel')->load($a)->getCollection()->skip(1) as $ke => $row) {
                UnitType::create([
                    $column_name[0] => $row[0],
                    $column_name[1] => $row[1],
                    'status' => '1'
                ]);
            }
        }
    }

    public function csv_download()
    {
        $table = UnitType::all();
        $filename = "units_tbl.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('id', 'name'));

        foreach($table as $row) {
            fputcsv($handle, array($row['id'], $row['name']));
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return Response::download($filename, 'units_tbl.csv', $headers);
    }
}
