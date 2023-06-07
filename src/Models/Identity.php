<?php

namespace Cyberfusion\Oxxa\Models;

use Cyberfusion\Oxxa\Contracts\Model as ModelContract;

class Identity extends Model implements ModelContract
{
    protected array $available = [
        'handle',
        'alias',
        'company',
        'company_name',
        'company_type',
        'name',
        'jobtitle',
        'firstname',
        'lastname',
        'street',
        'number',
        'suffix',
        'postalcode',
        'city',
        'state',
        'country',
        'tel',
        'fax',
        'email',
        'datebirth',
        'placebirth',
        'countrybirth',
        'postalbirth',
        'idnumber',
        'regnumber',
        'vatnumber',
        'tmnumber',
        'tmcountry',
        'idcarddate',
        'idcardissuer',
        'xxxmemberid',
        'xxxpassword',
        'ens_id',
        'ens_password',
        'profession',
        'travel_uin_id',
    ];

    protected array $required = [
        'firstname',
        'lastname',
        'street',
        'number',
        'postalcode',
        'city',
        'state',
        'country',
        'tel',
        'email',
    ];

    /**
     * Determines if all the required attributes are set.
     */
    public function isUsable(): bool
    {
        if ($this->company_name) {
            // Add the required company field
            $this->required[] = 'jobtitle';
        } elseif (in_array('jobtitle', $this->required, true)) {
            // Remove the require company fields
            $this->required = array_filter(
                $this->required,
                fn (string $field) => $field !== 'jobtitle'
            );
        }

        return count($this->getMissingAttributes()) === 0;
    }
}
