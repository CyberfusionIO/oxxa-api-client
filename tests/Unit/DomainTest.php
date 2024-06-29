<?php

namespace Cyberfusion\Oxxa\Tests\Unit;

use Carbon\Carbon;
use Cyberfusion\Oxxa\Enum\DomainStatus;
use Cyberfusion\Oxxa\Models\Domain;
use Cyberfusion\Oxxa\Oxxa;
use Cyberfusion\Oxxa\Requests\DomainListRequest;
use Cyberfusion\Oxxa\Tests\Fakes\DomainAutoRenewResponse;
use Cyberfusion\Oxxa\Tests\Fakes\DomainDeletedResponse;
use Cyberfusion\Oxxa\Tests\Fakes\DomainEppSentResponse;
use Cyberfusion\Oxxa\Tests\Fakes\DomainInformationResponse;
use Cyberfusion\Oxxa\Tests\Fakes\DomainListResponse;
use Cyberfusion\Oxxa\Tests\Fakes\DomainLockResponse;
use Cyberfusion\Oxxa\Tests\Fakes\DomainNameServerResponse;
use Cyberfusion\Oxxa\Tests\Fakes\DomainRegisterResponse;
use Cyberfusion\Oxxa\Tests\Fakes\DomainTakenResponse;
use Cyberfusion\Oxxa\Tests\Fakes\DomainTransferResponse;
use Cyberfusion\Oxxa\Tests\TestCase;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Arr;

class DomainTest extends TestCase
{
    private Factory $httpClient;

    private Oxxa $oxxa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = new Factory();
        $this->oxxa = new Oxxa(
            username: 'USER',
            password: 'PASS',
            client: $this->httpClient
        );
    }

    public function test_it_provides_main_parameters(): void
    {
        $this->httpClient->fake(fn (Request $request) => Factory::response((new DomainTakenResponse())->body()));

        $result = $this
            ->oxxa
            ->domain()
            ->check('example', 'org');

        $this->assertTrue($result->success());
        $this->httpClient->assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Arr::get($request->data(), 'command') === 'domain_check' &&
                Arr::get($request->data(), 'apiuser') === 'USER' &&
                Arr::get($request->data(), 'apipassword') === 'PASS' &&
                Arr::get($request->data(), 'test') === null;
        });
    }

    public function test_it_provides_main_parameters_for_test(): void
    {
        $this->httpClient->fake(fn (Request $request) => Factory::response((new DomainTakenResponse())->body()));

        $result = $this
            ->oxxa
            ->enabledTestMode()
            ->domain()
            ->check('example', 'org');

        $this->assertTrue($result->success());
        $this->httpClient->assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Arr::get($request->data(), 'command') === 'domain_check' &&
                Arr::get($request->data(), 'apiuser') === 'USER' &&
                Arr::get($request->data(), 'apipassword') === 'PASS' &&
                Arr::get($request->data(), 'test') === 'Y';
        });

        $this
            ->oxxa
            ->disableTestMode();
    }

    public function test_it_can_check_if_a_domain_is_available(): void
    {
        $this->httpClient->fake(fn (Request $request) => Factory::response((new DomainTakenResponse())->body()));

        $sld = 'example';
        $tld = 'org';
        $available = $this
            ->oxxa
            ->domain()
            ->check($sld, $tld);

        $this->assertTrue($available->success());
        $this->assertFalse($available->getData('available'));
        $this->httpClient->assertSent(function (Request $request) use ($sld, $tld) {
            return $request->method() === 'GET' &&
                Arr::get($request->data(), 'command') === 'domain_check' &&
                Arr::get($request->data(), 'sld') === $sld &&
                Arr::get($request->data(), 'tld') === $tld &&
                Arr::get($request->data(), 'apiuser') === 'USER' &&
                Arr::get($request->data(), 'apipassword') === 'PASS' &&
                Arr::get($request->data(), 'test') === null;
        });
    }

    public function test_it_can_register_given_domain(): void
    {
        $this->httpClient->fake(fn (Request $request) => Factory::response((new DomainRegisterResponse())->body()));

        $domain = new Domain();
        $domain->sld = 'example';
        $domain->tld = 'org';
        $domain->identityAdmin = 'ABCD123456';
        $domain->identityRegistrant = 'ABCD123456';
        $domain->nameserverGroup = 'ABCD123456';
        $domain->dnsTemplate = 'ABCD123456';
        $domain->lock = true;
        $domain->executionAt = Carbon::createFromDate('2020', '01', '01');

        $registered = $this
            ->oxxa
            ->domain()
            ->register($domain);

        $this->assertTrue($registered->success());
        $this->httpClient->assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Arr::get($request->data(), 'command') === 'register' &&
                Arr::get($request->data(), 'sld') === 'example' &&
                Arr::get($request->data(), 'tld') === 'org' &&
                Arr::get($request->data(), 'apiuser') === 'USER' &&
                Arr::get($request->data(), 'apipassword') === 'PASS' &&
                Arr::get($request->data(), 'autorenew') === 'N' &&
                Arr::get($request->data(), 'period') === 1 &&
                Arr::get($request->data(), 'identity-admin') === 'ABCD123456' &&
                Arr::get($request->data(), 'identity-registrant') === 'ABCD123456' &&
                Arr::get($request->data(), 'nsgroup') === 'ABCD123456' &&
                Arr::get($request->data(), 'dnstemplate') === 'ABCD123456' &&
                Arr::get($request->data(), 'lock') === 'Y' &&
                Arr::get($request->data(), 'execution_at') === '01-01-2020' &&
                Arr::get($request->data(), 'test') === null;
        });
    }

    public function test_it_can_transfer_given_domain(): void
    {
        $this->httpClient->fake(fn (Request $request) => Factory::response((new DomainTransferResponse())->body()));

        $domain = new Domain();
        $domain->sld = 'example';
        $domain->tld = 'org';
        $domain->identityAdmin = 'ABCD123456';
        $domain->identityRegistrant = 'ABCD123456';
        $domain->nameserverGroup = 'ABCD123456';
        $domain->dnsTemplate = 'ABCD123456';
        $domain->lock = true;
        $domain->executionAt = Carbon::createFromDate('2020', '01', '01');

        $transferred = $this
            ->oxxa
            ->domain()
            ->transfer($domain);

        $this->assertTrue($transferred->success());
        $this->httpClient->assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Arr::get($request->data(), 'command') === 'transfer' &&
                Arr::get($request->data(), 'sld') === 'example' &&
                Arr::get($request->data(), 'tld') === 'org' &&
                Arr::get($request->data(), 'apiuser') === 'USER' &&
                Arr::get($request->data(), 'apipassword') === 'PASS' &&
                Arr::get($request->data(), 'autorenew') === 'N' &&
                Arr::get($request->data(), 'period') === 1 &&
                Arr::get($request->data(), 'identity-admin') === 'ABCD123456' &&
                Arr::get($request->data(), 'identity-registrant') === 'ABCD123456' &&
                Arr::get($request->data(), 'nsgroup') === 'ABCD123456' &&
                Arr::get($request->data(), 'dnstemplate') === 'ABCD123456' &&
                Arr::get($request->data(), 'lock') === 'Y' &&
                Arr::get($request->data(), 'execution_at') === '01-01-2020' &&
                Arr::get($request->data(), 'test') === null;
        });
    }

    public function test_it_can_put_domain_in_quarantine(): void
    {
        $this->httpClient->fake(fn (Request $request) => Factory::response((new DomainDeletedResponse())->body()));

        $isQuarantined = $this
            ->oxxa
            ->domain()
            ->toQuarantine('example', 'org');

        $this->assertTrue($isQuarantined->success());
        $this->httpClient->assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Arr::get($request->data(), 'command') === 'domain_del' &&
                Arr::get($request->data(), 'sld') === 'example' &&
                Arr::get($request->data(), 'tld') === 'org' &&
                Arr::get($request->data(), 'apiuser') === 'USER' &&
                Arr::get($request->data(), 'apipassword') === 'PASS' &&
                Arr::get($request->data(), 'test') === null;
        });
    }

    public function test_it_can_send_epp(): void
    {
        $this->httpClient->fake(fn (Request $request) => Factory::response((new DomainEppSentResponse())->body()));

        $tokenSent = $this
            ->oxxa
            ->domain()
            ->sendEpp('example', 'org');

        $this->assertTrue($tokenSent->success());
        $this->httpClient->assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Arr::get($request->data(), 'command') === 'domain_epp' &&
                Arr::get($request->data(), 'sld') === 'example' &&
                Arr::get($request->data(), 'tld') === 'org' &&
                Arr::get($request->data(), 'apiuser') === 'USER' &&
                Arr::get($request->data(), 'apipassword') === 'PASS' &&
                Arr::get($request->data(), 'test') === null;
        });
    }

    public function test_it_can_retrieve_domain_information(): void
    {
        $this->httpClient->fake(fn (Request $request) => Factory::response((new DomainInformationResponse())->body()));

        $domainInf = $this
            ->oxxa
            ->domain()
            ->get('example', 'org');

        $this->assertTrue($domainInf->success());
        $this->assertIsObject($domainInf);
        $this->assertInstanceOf(Domain::class, $domainInf->getData('domain'));
        $this->httpClient->assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Arr::get($request->data(), 'command') === 'domain_inf' &&
                Arr::get($request->data(), 'sld') === 'example' &&
                Arr::get($request->data(), 'tld') === 'org' &&
                Arr::get($request->data(), 'apiuser') === 'USER' &&
                Arr::get($request->data(), 'apipassword') === 'PASS' &&
                Arr::get($request->data(), 'test') === null;
        });
    }

    public function test_it_can_enable_auto_renew_for_domain(): void
    {
        $this->httpClient->fake(fn (Request $request) => Factory::response((new DomainAutoRenewResponse())->body()));

        $isSet = $this
            ->oxxa
            ->domain()
            ->enableAutoRenewal('example', 'org');

        $this->assertTrue($isSet->success());
        $this->httpClient->assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Arr::get($request->data(), 'command') === 'autorenew' &&
                Arr::get($request->data(), 'sld') === 'example' &&
                Arr::get($request->data(), 'tld') === 'org' &&
                Arr::get($request->data(), 'autorenew') === 'Y' &&
                Arr::get($request->data(), 'apiuser') === 'USER' &&
                Arr::get($request->data(), 'apipassword') === 'PASS' &&
                Arr::get($request->data(), 'test') === null;
        });
    }

    public function test_it_can_disable_auto_renew_for_domain(): void
    {
        $this->httpClient->fake(fn (Request $request) => Factory::response((new DomainAutoRenewResponse())->body()));

        $isSet = $this
            ->oxxa
            ->domain()
            ->disableAutoRenewal('example', 'org');

        $this->assertTrue($isSet->success());
        $this->httpClient->assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Arr::get($request->data(), 'command') === 'autorenew' &&
                Arr::get($request->data(), 'sld') === 'example' &&
                Arr::get($request->data(), 'tld') === 'org' &&
                Arr::get($request->data(), 'autorenew') === 'N' &&
                Arr::get($request->data(), 'apiuser') === 'USER' &&
                Arr::get($request->data(), 'apipassword') === 'PASS' &&
                Arr::get($request->data(), 'test') === null;
        });
    }

    public function test_it_can_lock_domain(): void
    {
        $this->httpClient->fake(fn (Request $request) => Factory::response((new DomainLockResponse())->body()));

        $isSet = $this
            ->oxxa
            ->domain()
            ->enableLock('example', 'org');

        $this->assertTrue($isSet->success());
        $this->httpClient->assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Arr::get($request->data(), 'command') === 'lock' &&
                Arr::get($request->data(), 'sld') === 'example' &&
                Arr::get($request->data(), 'tld') === 'org' &&
                Arr::get($request->data(), 'lock') === 'Y' &&
                Arr::get($request->data(), 'apiuser') === 'USER' &&
                Arr::get($request->data(), 'apipassword') === 'PASS' &&
                Arr::get($request->data(), 'test') === null;
        });
    }

    public function test_it_can_unlock_domain(): void
    {
        $this->httpClient->fake(fn (Request $request) => Factory::response((new DomainLockResponse())->body()));

        $isSet = $this
            ->oxxa
            ->domain()
            ->disableLock('example', 'org');

        $this->assertTrue($isSet->success());
        $this->httpClient->assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Arr::get($request->data(), 'command') === 'lock' &&
                Arr::get($request->data(), 'sld') === 'example' &&
                Arr::get($request->data(), 'tld') === 'org' &&
                Arr::get($request->data(), 'lock') === 'N' &&
                Arr::get($request->data(), 'apiuser') === 'USER' &&
                Arr::get($request->data(), 'apipassword') === 'PASS' &&
                Arr::get($request->data(), 'test') === null;
        });
    }

    public function test_it_can_update_domain_name_servers(): void
    {
        $this->httpClient->fake(fn (Request $request) => Factory::response((new DomainNameServerResponse())->body()));

        $domain = new Domain();
        $domain->sld = 'example';
        $domain->tld = 'org';
        $domain->nameserverGroup = 'EFGH123456';

        $updated = $this
            ->oxxa
            ->domain()
            ->updateNameservers($domain);

        $this->assertTrue($updated->success());
        $this->httpClient->assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Arr::get($request->data(), 'command') === 'domain_ns_upd' &&
                Arr::get($request->data(), 'sld') === 'example' &&
                Arr::get($request->data(), 'tld') === 'org' &&
                Arr::get($request->data(), 'nsgroup') === 'EFGH123456' &&
                Arr::get($request->data(), 'apiuser') === 'USER' &&
                Arr::get($request->data(), 'apipassword') === 'PASS' &&
                Arr::get($request->data(), 'test') === null;
        });
    }

    public function test_it_can_retrieve_domain_list(): void
    {
        $this->httpClient->fake(fn (Request $request) => Factory::response((new DomainListResponse())->body()));

        $request = new DomainListRequest();
        $request->tld = 'org';
        $request->sortName = 'sld';
        $request->sortOrder = 'asc';
        $request->start = 0;
        $request->records = 2;
        $request->status = DomainStatus::STATUS_ACTIVE;
        $domains = $this
            ->oxxa
            ->domain()
            ->list($request);

        $this->assertTrue($domains->success());
        $this->assertInstanceOf(Domain::class, $domains->getData('domains')[0]);
        $this->assertInstanceOf(Domain::class, $domains->getData('domains')[0]);
        $this->assertEquals('example1.org', $domains->getData('domains')[0]->domain);
        $this->assertNull($domains->getData('domains')[0]->sld);
        $this->assertNull($domains->getData('domains')[0]->tld);

        $this->httpClient->assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                Arr::get($request->data(), 'command') === 'domain_list' &&
                Arr::get($request->data(), 'tld') === 'org' &&
                Arr::get($request->data(), 'sortname') === 'sld' &&
                Arr::get($request->data(), 'sortorder') === 'asc' &&
                Arr::get($request->data(), 'start') === 0 &&
                Arr::get($request->data(), 'records') === 2 &&
                Arr::get($request->data(), 'status') === 'active' &&
                Arr::get($request->data(), 'apiuser') === 'USER' &&
                Arr::get($request->data(), 'apipassword') === 'PASS' &&
                Arr::get($request->data(), 'test') === null;
        });
    }
}
