<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientTransferAttachment extends Model
{
    protected $table = 'patient_transfer_attachments';

    protected $fillable = [
        'patient_transfer_id',
        'file_name',
        'file_path',
        'mime_type',
        'size_bytes',
        'file_ext',
    ];

    public function transfer()
    {
        return $this->belongsTo(PatientTransfer::class, 'patient_transfer_id');
    }
}
