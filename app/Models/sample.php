<?Php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sample extends Model
{
    use HasFactory;

    protected $table = 'sample'; // Ensure the table name matches your database

    protected $fillable = [
        'portfolio_id',
        'fundname_id',
        'date',
        'price',
        'total'
    ];
}
