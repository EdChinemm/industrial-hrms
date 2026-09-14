<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeDocumentController extends Controller
{
    /**
     * Upload a document for an employee.
     */
    public function store(
        Request $request,
        Employee $employee
    ): RedirectResponse {
        $validated = $request->validate([
            'document_type' => [
                'required',
                'string',
                'max:100',
            ],

            'document_name' => [
                'required',
                'string',
                'max:255',
            ],

            'document' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'issue_date' => [
                'nullable',
                'date',
            ],

            'expiry_date' => [
                'nullable',
                'date',
                'after_or_equal:issue_date',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $path = $request
            ->file('document')
            ->store(
                'employee-documents/' . $employee->id,
                'public'
            );

        EmployeeDocument::create([
            'employee_id' => $employee->id,

            'document_type' =>
                $validated['document_type'],

            'document_name' =>
                $validated['document_name'],

            'file_path' => $path,

            'issue_date' =>
                $validated['issue_date'] ?? null,

            'expiry_date' =>
                $validated['expiry_date'] ?? null,

            'description' =>
                $validated['description'] ?? null,
        ]);

        return back()->with(
            'success',
            'Employee document uploaded successfully.'
        );
    }

    /**
     * Download an employee document.
     */
    public function download(
        Employee $employee,
        EmployeeDocument $document
    ): StreamedResponse {
        abort_unless(
            $document->employee_id === $employee->id,
            404
        );

        abort_unless(
            Storage::disk('public')
                ->exists($document->file_path),
            404,
            'Document file not found.'
        );

        $extension = pathinfo(
            $document->file_path,
            PATHINFO_EXTENSION
        );

        $downloadName =
            $document->document_name .
            ($extension ? '.' . $extension : '');

        return Storage::disk('public')->download(
            $document->file_path,
            $downloadName
        );
    }

    /**
     * Remove an employee document.
     */
    public function destroy(
        Employee $employee,
        EmployeeDocument $document
    ): RedirectResponse {
        abort_unless(
            $document->employee_id === $employee->id,
            404
        );

        if (
            Storage::disk('public')
                ->exists($document->file_path)
        ) {
            Storage::disk('public')
                ->delete($document->file_path);
        }

        $document->delete();

        return back()->with(
            'success',
            'Employee document removed successfully.'
        );
    }
}
