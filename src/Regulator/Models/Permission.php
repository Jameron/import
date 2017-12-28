<?php namespace Jameron\Regulator\Models;

use Illuminate\Database\Eloquent\Model;
use Jameron\Regulator\Models\Role;
use Jameron\Model\Traits\PublishedTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    protected $table = 'regulator_permissions';

    /**
     * Values that can be automatically filled.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'level',
    ];

    protected $table_prefix;

    public function __construct($arguments = [])
    {
        parent::__construct($arguments);
        $this->table_prefix = config('regulator.db_table_prefix');
        if (!empty($this->table_prefix)) {
            $this->table_prefix = $this->table_prefix . (substr($this->table_prefix, -1)=='_') ? '' : '_';
            $this->table = $this->table_prefix . $this->table;
        }
    }

    /**
     * Permissions can belong to many different roles
     *
     * @return Jameron\Regulator\Models\Role
     */
    public function roles()
    {
        return $this->belongsToMany('Jameron\Regulator\Models\Role', $this->table_prefix . 'regulator_permission_role');
    }
}
