<?php

namespace Cyberfusion\Oxxa\Tests\Fakes;

class DomainAutoRenewResponse
{
    public function body(): string
    {
        return '<?xml version="1.0" encoding="ISO-8859-1" ?>
            <channel>
                <order>
                    <order_id>123456</order_id>
                     <command>autorenew</command>
                     <sld>example</sld>
                     <tld>org</tld>
                     <status_code>XMLOK 2</status_code>
                     <status_description>Autorenew voor dit domein is aangepast</status_description>
                     <price>1.23</price>
                     <details>Autorenew aangepast naar: Y</details>
                     <order_complete>TRUE</order_complete>
                     <done>TRUE</done>
                </order>
            </channel>';
    }
}
