<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAcquisition extends Model
{
    use HasFactory;
    protected $table = 'acquisitions';
    protected $fillable = ['supplier_id', 'item_id', 'quantity', 'unit_cost', 'acquisition_date', 'remarks'];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function item() { return $this->belongsTo(Item::class); }
}
