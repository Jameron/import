<?php 

namespace Jameron\Regulator;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Jameron\Regulator\Repositories\RegulatorRepository;

class Regulator
{

    /**
     * Errors captured during validation or saving.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * The config/vendor/jameron/regulator.php configuration values.
     *
     * @var array
     */
    protected static $config;

    /**
     * Constructor for Taxonomy.
     *
     * @param  Snap\Taxonomy\Repositories\TaxonomyRepository  $repo
     * @param  \Illuminate\Support\MessageBag  $errors
     * @param  array  $config
     * @return void
     */
    public function __construct(RegulatorRepository $repo, MessageBag $errors = null, $config = null)
    {
        $this->repo = $repo;

        $this->errors = ! isset($errors) ? new MessageBag() : $errors;

        // how does the config namespace get registered?
        if (! isset($config)) {
            $config = config('jameron::regulator');
        }

        static::$config = $config;
    }

    /**
     * Inserts a Role model object
     *
     * @param  string|array $values
     * @return Snap\Taxonomy\Models\Role
     */
    public function createRole($values)
    {
        return $this->repo->createRole($values);
    }

    /**
     * Returns a Role model object
     *
     * @param  integer $id
     * @return Snap\Regulator\Models\Role
     */
    public function role($id)
    {
        return $this->repo->role($id);
    }

    /**
     * Returns a collection of Role model objects
     *
     * @return \Illuminate\Support\Collection
     */
    public function roles()
    {
        return $this->repo->roles();
    }

    /**
     * Inserts a Role model object
     *
     * @param  string|array $values
     * @return Snap\Taxonomy\Models\Permission
     */
    public function createPermission($values)
    {
        return $this->repo->createPermission($values);
    }

    /**
     * Returns a Permission model object
     *
     * @param  integer $id
     * @return Snap\Regulator\Models\Permission
     */
    public function permission($id)
    {
        return $this->repo->permission($id);
    }

    /**
     * Returns a collection of Permission model objects
     *
     * @param Snap\Taxonomy\Models\Role
     * @return \Illuminate\Support\Collection
     */
    public function permissions($role=null)
    {
        return $this->repo->permissions();
    }
}
