<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice';
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['date', 'due_date', 'paid_date'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['view_date', 'view_due_date'];
    
    /**
     * Get the Anggota that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }

    /**
     * Get the InvoiceStatus that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoiceStatus()
    {
        return $this->belongsTo(InvoiceStatus::class);
    }

    /**
     * Get the invoiceType that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoiceType()
    {
        return $this->belongsTo(InvoiceType::class);
    }

    public function getViewDateAttribute()
    {
        return $this->date->format('d F Y');
    }

    public function getViewDueDateAttribute()
    {
        return $this->due_date->format('d F Y');
    }
}
