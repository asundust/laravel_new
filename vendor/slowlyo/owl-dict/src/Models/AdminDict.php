<?php

namespace Slowlyo\OwlDict\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Slowlyo\OwlAdmin\Models\BaseModel as Model;

class AdminDict extends Model
{
    use SoftDeletes;

    protected $table = 'admin_dict';

    public function children()
    {
        return $this->hasMany(AdminDict::class, 'parent_id')->orderByDesc('sort');
    }

    public function dict_type()
    {
        return $this->belongsTo(AdminDict::class, 'parent_id');
    }
}
