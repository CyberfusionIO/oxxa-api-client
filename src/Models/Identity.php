<?php

namespace Cyberfusion\Oxxa\Models;

use Cyberfusion\Oxxa\Contracts\Model;
use Cyberfusion\Oxxa\Support\ArrayHelper;
use Cyberfusion\Oxxa\Traits\HasRequiredAttributes;
use DateTimeInterface;

class Identity implements Model
{
    use HasRequiredAttributes;

    public function __construct(
        public ?string $handle = null,
        public ?string $alias = null,
        public ?bool $company = null,
        public ?string $companyName = null,
        public ?string $companyType = null,
        public ?string $name = null,
        public ?string $jobTitle = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $street = null,
        public ?string $number = null,
        public ?string $suffix = null,
        public ?string $postalCode = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $country = null,
        public ?string $tel = null,
        public ?string $fax = null,
        public ?string $email = null,
        public ?DateTimeInterface $dateBirth = null,
        public ?string $placeBirth = null,
        public ?string $countryBirth = null,
        public ?string $postalBirth = null,
        public ?string $idNumber = null,
        public ?string $regNumber = null,
        public ?string $vatNumber = null,
        public ?string $trademarkNumber = null,
        public ?string $trademarkCountry = null,
        public ?string $trademarkName = null,
        public ?DateTimeInterface $idCardDate = null,
        public ?string $idCardIssuer = null,
        public ?string $xxxMemberId = null,
        public ?string $xxxPassword = null,
        public ?string $ensId = null,
        public ?string $ensPassword = null,
        public ?string $profession = null,
        public ?string $travelUinId = null,
    ) {
    }

    public function missingFields(array $fields = []): array
    {
        if ($this->companyName) {
            // Add the required company field
            $fields[] = 'jobtitle';
        } elseif (in_array('jobtitle', $fields, true)) {
            // Remove the require company fields
            unset($fields['jobtitle']);
        }

        return array_diff($fields, array_keys($this->toArray()));
    }

    public function toArray(): array
    {
        return ArrayHelper::transformToParameters([
            'handle' => $this->handle,
            'alias' => $this->alias,
            'company' => $this->company,
            'company_name' => $this->companyName,
            'company_type' => $this->companyType,
            'name' => $this->name,
            'jobtitle' => $this->jobTitle,
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'street' => $this->street,
            'number' => $this->number,
            'suffix' => $this->suffix,
            'postalcode' => $this->postalCode,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'tel' => $this->tel,
            'fax' => $this->fax,
            'email' => $this->email,
            'datebirth' => $this->dateBirth,
            'placebirth' => $this->placeBirth,
            'countrybirth' => $this->countryBirth,
            'postalbirth' => $this->postalBirth,
            'idnumber' => $this->idNumber,
            'regnumber' => $this->regNumber,
            'vatnumber' => $this->vatNumber,
            'tmnumber' => $this->trademarkNumber,
            'tmcountry' => $this->trademarkCountry,
            'idcarddate' => $this->idCardDate,
            'idcardissuer' => $this->idCardIssuer,
            'xxxmemberid' => $this->xxxMemberId,
            'xxxpassword' => $this->xxxPassword,
            'ens_id' => $this->ensId,
            'ens_password' => $this->ensPassword,
            'profession' => $this->profession,
            'travel_uin_id' => $this->travelUinId,
        ]);
    }
}
