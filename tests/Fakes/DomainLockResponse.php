<?php

namespace Cyberfusion\Oxxa\Tests\Fakes;

class DomainLockResponse
{
    public function body(): string
    {
        return '<?xml version="1.0" encoding="ISO-8859-1" ?>
            <channel>
                <order>
                    <order_id>123456</order_id>
                    <command>lock</command>
                    <sld>example</sld>
                    <tld>org</tld>
                    <status_code>XMLOK 3</status_code>
                    <status_description>Lock voor dit domein is aangepast</status_description>
                    <price>0.00</price>
                    <details>Lock aangepast naar: N</details>
                    <order_complete>TRUE</order_complete>
                    <done>TRUE</done> 
                </order> 
            </channel>';
    }
}
