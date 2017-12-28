<?php 

namespace Jameron\Import;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Jameron\Import\Repositories\ImportRepository;

class Import
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
    public function __construct(ImportRepository $repo, MessageBag $errors = null, $config = null)
    {
        $this->repo = $repo;

        $this->errors = ! isset($errors) ? new MessageBag() : $errors;

        if (! isset($config)) {
            $config = config('jameron::import');
        }

        static::$config = $config;
    }

    /**
     * Inserts a Role model object
     *
     * @param  string|array $values
     * @return Snap\Taxonomy\Models\Role
     */
    public function import($file)
    {
        return $this->repo->import($file);
    }

}
