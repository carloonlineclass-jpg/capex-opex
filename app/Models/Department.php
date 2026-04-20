<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'code', 'capex_limit', 'opex_limit'];

    public function users() { return $this->hasMany(User::class); }
    public function allocations() { return $this->hasMany(Allocation::class); }
    public function requisitions() { return $this->hasMany(Requisition::class); }
}
