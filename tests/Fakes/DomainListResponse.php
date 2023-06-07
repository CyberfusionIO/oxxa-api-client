<?php

namespace Cyberfusion\Oxxa\Tests\Fakes;

class DomainListResponse
{
    public function body(): string
    {
        return '<?xml version="1.0" encoding="ISO-8859-1" ?>
            <channel>
                <order>
                    <order_id>123456</order_id>
                    <command>domain_list</command>
                    <tld>org</tld>
                    <status_code>XMLOK18</status_code>
                    <status_description>In DETAILS vind u de uitgebreide informatie</status_description>
                    <price>0.00</price>
                        <details>
                            <domains_total>100</domains_total>
                            <domains_found>25</domains_found>
                            <domain>
                                <domainname>example1.org</domainname>
                                <nsgroup>EFGH123456</nsgroup>
                                <identity-registrant>ABCD123456</identity-registrant>
                                <identity-admin>ABCD 123456</identity-admin>
                                <identity-tech>ABCD 123456</identity-tech>
                                <identity-billing>ABCD 123456</identity-billing>
                                <expire_date>2009-10-06</expire_date>
                                <autorenew>Y</autorenew> 
                                <lock>Y</lock>
                            </domain>
                            <domain>
                                <domainname>example2.org</domainname>
                                <nsgroup>EFGH123456</nsgroup>
                                <identity-registrant>ABCD123456</identity-registrant>
                                <identity-admin>ABCD123456</identity-admin>
                                <identity-tech>ABCD123456</identity-tech>
                                <identity-billing>ABCD123456</identity-billing>
                                <expire_date>2008-10-09</expire_date>
                                <autorenew>Y</autorenew>
                            <lock>Y</lock>
                        </domain>
                    </details>
                    <order_complete>TRUE</order_complete>
                    <done>TRUE</done> 
                </order> 
            </channel>';
    }
}
