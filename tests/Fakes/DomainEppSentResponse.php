<?php

namespace Cyberfusion\Oxxa\Tests\Fakes;

class DomainEppSentResponse
{
    public function body(): string
    {
        return '<?xml version="1.0" encoding="ISO-8859-1" ?>
            <channel>
                <order>
                    <order_id>123456</order_id>
                    <command>domain_epp</command>
                    <sld>example</sld>
                    <tld>org</tld>
                    <status_code>XMLOK 39</status_code>
                    <status_description>De EPP code is verstuurd naar de registrant</status_description>
                    <price />
                    <details>EPPCODE</details>
                    <order_complete>TRUE</order_complete>
                    <done>TRUE</done>
                </order>
            </channel>';
    }
}
