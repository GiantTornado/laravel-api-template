<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Exports\Book\BooksExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportController extends Controller {
    public function export(Request $request) {
        return Excel::download(new BooksExport($request->all()), 'books.xlsx');
    }
}
