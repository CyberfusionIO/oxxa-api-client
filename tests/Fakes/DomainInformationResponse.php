<?php

namespace Cyberfusion\Oxxa\Tests\Fakes;

class DomainInformationResponse
{
    public function body(): string
    {
        return '<?xml version="1.0" encoding="ISO-8859-1" ?>
            <channel>
                <order>
                <order_id />
                <command>domain_inf</command>
                <sld>example</sld>
                <tld>org</tld>
                <status_code>XMLOK 16</status_code>
                <status_description>In DETAILS vind u de uitgebreide informatie</status_description>
                <price>0.00</price>
                <details>
                    <identity-registrant>ABCD147258</identity-registrant>
                    <identity-admin> ABCD147258</identity-admin>
                    <identity-billing> ABCD147258</identity-billing>
                    <identity-tech> ABCD147258</identity-tech>
                    <nsgroup>EFGH1234</nsgroup>
                    <expire_date>04-10-2009</expire_date>
                    <autorenew>Y</autorenew>
                    <lock>N</lock>
                    <dnssec>N</dnssec>
                </details>
                <order_complete>TRUE</order_complete>
                <done>TRUE</done> 
                </order> 
            </channel>';
    }
}
