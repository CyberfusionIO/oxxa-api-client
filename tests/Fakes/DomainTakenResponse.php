<?php

namespace Cyberfusion\Oxxa\Tests\Fakes;

class DomainTakenResponse
{
    public function body(): string
    {
        return '<?xml version="1.0" encoding="ISO-8859-1" ?>
            <channel>
                <order>
                    <order_id>123456</order_id>
                    <command>domain_check</command>
                    <sld>example</sld>
                    <tld>org</tld>
                    <status_code>XMLOK 10</status_code>
                    <status_description>Domein is bezet</status_description>
                    <price>0</price>
                    <order_complete>TRUE</order_complete>
                    <done>TRUE</done>
                </order>
            </channel>';
    }
}
