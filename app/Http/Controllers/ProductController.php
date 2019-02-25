<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    // The file that our products go to
    private  $products_file = "products.json";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get data with pagination
        $res = $this->getRangeFromFile(request()->start ?: 0, request()->length ?: 10);
        return array_merge(['draw' => request()->draw], $res);
    }

    /**
     * Get data within given range from the file
     *
     * @param int $start Start of range
     * @param int $end End of range
     *
     * @return array
     */
    private function getRangeFromFile ($start, $end)
    {
        // Open file
        $handle = fopen($this->products_file, "r");
        $result = [];
        $lines = 0;

        if ($handle) {
            // Read file line by line
            while (($buffer = fgets($handle)) !== false) {
                // If current line is in given range
                if ($lines >= $start && $lines <= $end)
                    // Add line to results
                    $result []= json_decode($buffer);
                $lines++;
            }
            fclose($handle);
        }
        return ["data" => $result, "recordsFiltered" => $lines, "recordsTotal" => $lines];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|min:3|max:190',
            'quantity'  => 'required|numeric|min:1|max:1000000000',
            'price'     => 'required|numeric|min:100|max:10000000000'
        ]);

        // Add current date to the data
        $request->merge([
            'created_at' => \Carbon\Carbon::now()->format("Y-m-d H:i:s"),
            'updated_at' => \Carbon\Carbon::now()->format("Y-m-d H:i:s")
        ]);

        $data = json_encode($request->all()) . PHP_EOL;

        // Store data to the file
        file_put_contents($this->products_file, $data, FILE_APPEND);

        return response()->json(['status' => true, 'message' => 'Product stored!'], 200);
    }
}
