<?php

namespace Cyberfusion\Oxxa\Tests\Fakes;

class DomainRegisterResponse
{
    public function body(): string
    {
        return '<?xml version="1.0" encoding="ISO-8859-1" ?>
            <channel>
                <order>
                    <order_id>123456</order_id>
                    <command>register</command>
                    <sld>example</sld>
                    <tld>org</tld>
                    <status_code>XMLOK 1</status_code>
                    <status_description>Het domein is succesvol geregistreerd</status_description>
                    <price>7.20</price>
                    <order_complete>TRUE</order_complete>
                    <done>TRUE</done> 
                </order>
            </channel>';
    }
}
