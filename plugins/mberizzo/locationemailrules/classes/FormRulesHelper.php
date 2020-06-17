<?php namespace Mberizzo\Locationemailrules\Classes;

class FormRulesHelper
{

    protected $form;
    protected $rules;
    protected $post;

    public function __construct($form, $rules, $post)
    {
        $this->form = $form;
        $this->rules = json_decode($rules, true);
        $this->post = $post;
    }

    /**
     * Get list of email for that especific coutry
     * Also check if has exception in that state.
     *
     * @return array. List of emails.
     */
    public function getContacts()
    {
        $sendTo = [];
        $formRules = $this->rules[$this->form->id] ?? false;

        if ($formRules) {
            $sendTo = $formRules['emails'] ?? [];

            // Verify if has exceptions for this specific state
            if (!empty($formRules['exceptions']) && ($exceptionEmails = $this->getExceptionEmails($formRules))) {
                $sendTo = $exceptionEmails;
            }
        }

        return $sendTo;
    }

    /**
     * Get rules for this specific Form
     *
     * @return [type] [description]
     */
    private function getFormRules()
    {
        if (array_key_exists($this->form->id, $this->rules)) { // Rules keys are form ids
            return $this->rules[$this->form->id];
        }

        return false;
    }

    /**
     * Verify if that country contains any excepcion
     * for that specific state.
     *
     * @param  array $formRules. An array of state and emails
     * @return array List of emails
     */
    private function getExceptionEmails($formRules)
    {
        $stateId = $this->post['state_id'];

        $found = array_first($formRules['exceptions'], function ($value, $key) use ($stateId) {
            return $value['state'] === $stateId;
        });

        if ($found && $found['emails']) { // $found = ['state': "128", 'emails' =>  ["mail@mail.com", "mail2@mail2.com"] ]
            return $found['emails'];
        }

        return [];
    }
}
