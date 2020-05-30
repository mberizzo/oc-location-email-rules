<?php namespace Mberizzo\LocationEmailRules\Models;

use Illuminate\Support\Facades\DB;
use Model;
use RainLab\Location\Models\Country;

/**
 * Model
 */
class Rule extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public function save(array $data = NULL, $sessionKey = NULL)
    {
        $country = Country::find($data['country_id']);

        if ($country) {
            $country->email_rules = $data['email_rules'] ? json_encode($data['email_rules']) : null;
            return $country->save();
        }

        return false;
    }

}
