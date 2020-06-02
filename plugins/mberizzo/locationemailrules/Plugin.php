<?php namespace Mberizzo\LocationEmailRules;

use Backend;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Mberizzo\Locationemailrules\Classes\FormRulesHelper;
use RainLab\Location\Controllers\Locations;
use RainLab\Location\Models\Country;
use System\Classes\PluginBase;

/**
 * LocationEmailRules Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * @var array Plugin dependencies
     */
    public $require = ['RainLab.Location'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Location Email Rules',
            'description' => 'Set emails for specific Country and State',
            'author'      => 'Mberizzo',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        Event::listen('backend.list.extendColumns', function($widget) {
            // Only for the Locations controller
            if (!$widget->getController() instanceof Locations) {
                return;
            }

            // Only for the Country model
            if (!$widget->model instanceof Country) {
                return;
            }

            $widget->addColumns([
                '_email_rules' => [
                    'label' => 'Reglas',
                    'type' => 'partial',
                    'clickable' => false,
                    'sortable' => false,
                    'path' => '$/mberizzo/locationemailrules/models/rule/columns/_set_rules_btn.htm'
                ]
            ]);
        });

        Event::listen('backend.form.extendFields', function($widget) {

            // Only for the Locations controller
            if (!$widget->getController() instanceof Locations) {
                return;
            }

            // Only for the Country model
            if (!$widget->model instanceof Country) {
                return;
            }

            $widget->addFields([
                '_email_rules' => [
                    'type' => 'partial',
                    'path' => '$/mberizzo/locationemailrules/models/rule/fields/_set_rules_btn.htm'
                ]
            ]);
        });

        Event::listen('formBuilder.beforeSendMessage', function ($form, $data, $files) {
            $post = post();

            if (empty($post['country_id'])) {
                return false;
            }

            $country = Country::find($post['country_id']);

            if (! $country->email_rules) {
                return false;
            }

            $helper = new FormRulesHelper($form, $country->email_rules, $post);

            $contacts = $helper->getContacts();

            if (! empty($contacts)) {
                Mail::sendTo($contacts, $form->template->code, $data);
                return true;
            }

            return false;
        });
    }
}
