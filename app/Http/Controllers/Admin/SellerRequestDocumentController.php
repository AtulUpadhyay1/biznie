<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerOnboardingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves a seller applicant's KYC document to the admin.
 *
 * The show page linked straight at `/storage/...`, which only opens if the
 * public symlink is in place on the host and leaves the document readable by
 * anyone who guesses the path. Streaming it from the disk behind the admin
 * guard works either way, and says so plainly when the file is missing instead
 * of handing the admin a 404 page.
 */
class SellerRequestDocumentController extends Controller
{
    /** Field name -> the column holding its stored path. */
    private const DOCUMENTS = [
        'gst_certificate'          => 'gst_certificate_path',
        'pan_document'             => 'pan_document_path',
        'registration_certificate' => 'registration_certificate_path',
        'address_proof'            => 'address_proof_path',
        'cancelled_cheque'         => 'cancelled_cheque_path',
        'other_documents'          => 'other_documents_path',
        'product_sheet'            => 'product_sheet_path',
    ];

    public function show(Request $request, $id, string $field): Response
    {
        abort_unless(isset(self::DOCUMENTS[$field]), 404);
        abort_unless(auth('admin')->user()?->can('seller-list'), 403);

        $record = SellerOnboardingDetail::findOrFail($id);
        $path = $record->{self::DOCUMENTS[$field]};

        abort_if(blank($path), 404, 'No document was uploaded for this field.');

        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404, 'The uploaded file is no longer on the server.');

        return $disk->response($path, basename($path), [
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }
}
