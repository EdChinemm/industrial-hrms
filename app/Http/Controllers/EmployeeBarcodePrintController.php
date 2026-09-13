<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorSVG;

class EmployeeBarcodePrintController extends Controller
{
    public function show(
        Employee $employee
    ): View {
        $employee->load([
            'department',
            'position',
            'activeBarcode',
        ]);

        abort_if(
            !$employee->activeBarcode,
            404,
            'This employee does not have an active barcode.'
        );

        $generator =
            new BarcodeGeneratorSVG();

        $barcodeSvg = $generator->getBarcode(
            $employee->activeBarcode->barcode,
            BarcodeGeneratorSVG::TYPE_CODE_128,
            2,
            70
        );

        return view(
            'employees.barcode-print',
            compact(
                'employee',
                'barcodeSvg'
            )
        );
    }
}
