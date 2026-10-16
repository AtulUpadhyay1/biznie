<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * B30: a credit request says how much and for how long, and the secured /
 * unsecured credit types ask for the two documents credit is actually
 * underwritten on — 12 months of GSTR-3B and of bank statements.
 */
return new class extends Migration
{
    private const EXTRA_FIELDS = [
        ['label' => 'GSTR-3B returns (last 12 months)', 'required' => true, 'type' => 'file', 'description' => 'All monthly GSTR-3B filings for the last 12 months, as PDF'],
        ['label' => 'Bank statement (last 12 months)', 'required' => true, 'type' => 'file', 'description' => 'Main current-account statement for the last 12 months, as PDF'],
    ];

    public function up(): void
    {
        Schema::table('credit_wallet_requests', function (Blueprint $table) {
            $table->decimal('amount', 14, 2)->nullable()->after('credit_wallet_document_type_id');
            $table->unsignedSmallInteger('tenure_days')->nullable()->after('amount');
        });

        $types = DB::table('credit_wallet_document_types')
            ->whereNull('deleted_at')
            ->where(fn ($q) => $q->where('name', 'like', '%Secured%')->orWhere('name', 'like', '%Unsecured%'))
            ->get(['id', 'forms']);

        foreach ($types as $type) {
            $forms = json_decode((string) $type->forms, true) ?: [];
            $labels = array_map(fn ($f) => strtolower(trim($f['label'] ?? '')), $forms);
            foreach (self::EXTRA_FIELDS as $field) {
                if (! in_array(strtolower($field['label']), $labels, true)) {
                    $forms[] = $field;
                }
            }
            DB::table('credit_wallet_document_types')->where('id', $type->id)->update(['forms' => json_encode($forms)]);
        }
    }

    public function down(): void
    {
        Schema::table('credit_wallet_requests', function (Blueprint $table) {
            $table->dropColumn(['amount', 'tenure_days']);
        });
    }
};
