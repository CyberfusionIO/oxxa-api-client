<?php

namespace Cyberfusion\Oxxa\Endpoints;

use Cyberfusion\Oxxa\Contracts\Endpoint as EndpointContract;
use Cyberfusion\Oxxa\Enum\StatusCode;
use Cyberfusion\Oxxa\Enum\Toggle;
use Cyberfusion\Oxxa\Exceptions\OxxaException;
use Cyberfusion\Oxxa\Models\Identity;
use Cyberfusion\Oxxa\Requests\IdentityListRequest;
use Cyberfusion\Oxxa\Support\OxxaResult;
use DateTime;
use Symfony\Component\DomCrawler\Crawler;

class IdentityEndpoint extends Endpoint implements EndpointContract
{
    /**
     * Returns all the available identities.
     *
     * @throws OxxaException
     */
    public function list(?IdentityListRequest $request = null): OxxaResult
    {
        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'identity_list'],
                $request?->toArray() ?? []
            ));

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);
        if ($statusCode !== StatusCode::STATUS_IDENTITIES_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription,
                status: $statusCode,
            );
        }

        $identities = [];
        $xml
            ->filter('channel > order > details > identity')
            ->each(function (Crawler $identityNode) use (&$identities) {
                $identities[] = new Identity(
                    handle: $identityNode->filter('handle')->text(),
                    alias: $identityNode->filter('alias')->text(),
                    companyName: $identityNode->filter('company_name')->text(),
                    name: $identityNode->filter('name')->text(),
                );
            });

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'identities' => $identities,
            ],
            status: $statusCode,
        );
    }

    /**
     * Retrieve the identity by the handle.
     *
     * @throws OxxaException
     */
    public function get(string $handle): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'identity_get',
                'identity' => $handle,
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);
        if ($statusCode !== StatusCode::STATUS_IDENTITY_RETRIEVED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription,
                status: $statusCode,
            );
        }

        $detailsNode = $xml->filter('channel > order > details');

        $identity = new Identity(
            handle: $handle,
            alias: $detailsNode->filter('alias')->text(),
            company: Toggle::toBoolean($detailsNode->filter('company')->text()),
            companyName: $detailsNode->filter('company_name')->text(),
            companyType: $detailsNode->filter('company_type')->text(),
            jobTitle: $detailsNode->filter('jobtitle')->text(),
            firstName: $detailsNode->filter('firstname')->text(),
            lastName: $detailsNode->filter('lastname')->text(),
            street: $detailsNode->filter('street')->text(),
            number: $detailsNode->filter('number')->text(),
            suffix: $detailsNode->filter('suffix')->text(),
            postalCode: $detailsNode->filter('postalcode')->text(),
            city: $detailsNode->filter('city')->text(),
            state: $detailsNode->filter('state')->text(),
            country: $detailsNode->filter('country')->text(),
            tel: $detailsNode->filter('tel')->text(),
            fax: $detailsNode->filter('fax')->text(),
            email: $detailsNode->filter('email')->text(),
            dateBirth: $detailsNode->filter('datebirth')->text() !== ''
                ? DateTime::createFromFormat('d-m-Y', $detailsNode->filter('datebirth')->text())->setTime(0, 0)
                : null,
            placeBirth: $detailsNode->filter('placebirth')->text(),
            countryBirth: $detailsNode->filter('countrybirth')->text(),
            postalBirth: $detailsNode->filter('postalbirth')->text(),
            idNumber: $detailsNode->filter('idnumber')->text(),
            regNumber: $detailsNode->filter('regnumber')->text(),
            vatNumber: $detailsNode->filter('vatnumber')->text(),
            trademarkNumber: $detailsNode->filter('tmnumber')->text(),
            trademarkCountry: $detailsNode->filter('tmcountry')->text(),
            trademarkName: $detailsNode->filter('tmname')->text(),
            idCardDate: $detailsNode->filter('idcarddate')->text() !== ''
                ? DateTime::createFromFormat('d-m-Y', $detailsNode->filter('idcarddate')->text())->setTime(0, 0)
                : null,
            idCardIssuer: $detailsNode->filter('idcardissuer')->text(),
            xxxMemberId: $detailsNode->filter('xxxmemberid')->text(),
            xxxPassword: $detailsNode->filter('xxxpassword')->text(),
            ensId: $detailsNode->filter('ens_id')->text(),
            ensPassword: $detailsNode->filter('ens_password')->text(),
            profession: $detailsNode->filter('profession')->text(),
            travelUinId: $detailsNode->filter('travel_uin_id')->text(),
        );

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'identity' => $identity,
            ],
            status: $statusCode,
        );
    }

    /**
     * Create the identity.
     *
     * @throws OxxaException
     */
    public function create(Identity $identity): OxxaResult
    {
        $requiredFields = [
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

        if ($identity->missingAny($requiredFields)) {
            return new OxxaResult(
                success: false,
                message: sprintf(
                    'The identity is missing the required fields: `%s`',
                    implode(', ', $identity->missingFields($requiredFields))
                )
            );
        }

        $xml = $this
            ->client
            ->request(array_merge(
                ['command' => 'identity_add'],
                $identity->toArray()
            ));

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);
        if ($statusCode !== StatusCode::STATUS_IDENTITY_ADDED) {
            return new OxxaResult(
                success: false,
                message: $statusDescription,
                status: $statusCode,
            );
        }

        return new OxxaResult(
            success: true,
            message: $statusDescription,
            data: [
                'handle' => $xml->filter('channel > order > details')->text(),
            ],
            status: $statusCode,
        );
    }

    /**
     * Update the identity.
     *
     * @throws OxxaException
     */
    public function update(string $handle, Identity $identity): OxxaResult
    {
        $xml = $this
            ->client
            ->request(array_merge(
                [
                    'command' => 'identity_upd',
                    'identity' => $handle,
                ],
                $identity->toArray()
            ));

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: $statusCode === StatusCode::STATUS_IDENTITY_UPDATED,
            message: $statusDescription,
            status: $statusCode,
        );
    }

    /**
     * Remove the identity.
     *
     * @throws OxxaException
     */
    public function delete(string $handle): OxxaResult
    {
        $xml = $this
            ->client
            ->request([
                'command' => 'identity_del',
                'identity' => $handle,
            ]);

        $statusCode = $this->getStatusCode($xml);
        $statusDescription = $this->getStatusDescription($xml);

        return new OxxaResult(
            success: $statusCode === StatusCode::STATUS_IDENTITY_DELETED,
            message: $statusDescription,
            status: $statusCode,
        );
    }
}
