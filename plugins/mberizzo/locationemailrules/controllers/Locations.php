<?php namespace Mberizzo\LocationEmailRules\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Backend\Facades\Backend;
use Backend\Models\User;
use Mberizzo\LocationEmailRules\Models\Rule;
use Mberizzo\Locationemailrules\Classes\RuleFieldsBuilder;
use October\Rain\Support\Facades\Flash;
use RainLab\Location\Models\Country;
use RainLab\Location\Models\State;
use Renatio\FormBuilder\Models\Form;

class Locations extends Controller
{
    public $implement = ['Backend\Behaviors\FormController'];

    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
    }

    public function formExtendFields($form)
    {
        $tabs = [];
        $states = State::where('country_id', $form->model->id)->pluck('name', 'id');

        Form::each(function ($formRenatio) use ($form, &$tabs, $states) {
            if ($form->isNested) return;

            $tab = (new RuleFieldsBuilder($formRenatio))->getConfigAsArray(
                $form->model->email_rules,
                $states
            );

            $tabs = array_merge($tabs, $tab);
        });

        $form->addTabFields($tabs);
    }

    public function setRules($countryId)
    {
        $country = Country::find($countryId);

        $this->initForm($country);

        $this->pageTitle = 'Set Email Rules';

        return $this->makePartial('set_rules_form', [
            'country' => $country,
        ]);
    }

    public function onSave()
    {
        $data = array_merge(
            ['email_rules' => array_get(post(), 'Country.email_rules')],
            ['country_id' => $this->params[0]]
        );

        if ((new Rule)->save($data)) {
            Flash::success('Reglas guardadas');
        }

        if (request()->has('close')) {
            return redirect()->to(Backend::url('rainlab/location/locations'));
        }
    }

}
