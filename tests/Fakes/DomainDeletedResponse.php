<?php

namespace Cyberfusion\Oxxa\Tests\Fakes;

class DomainDeletedResponse
{
    public function body(): string
    {
        return '<?xml version="1.0" encoding="ISO-8859-1" ?>
            <channel>
                <order>
                    <order_id>123456</order_id>
                    <command>domain_del</command>
                    <sld>example</sld>
                    <tld>org</tld>
                    <status_code>XMLPEN 11</status_code>
                    <status_description>De domeinnaam wordt opgeheven</status_description>
                    <price>0</price>
                    <order_complete>FALSE</order_complete>
                    <done>TRUE</done>
                </order> 
            </channel>';
    }
}
