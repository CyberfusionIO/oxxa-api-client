<?php

namespace Cyberfusion\Oxxa\Enum;

class StatusCode
{
    public const STATUS_DOMAIN_REGISTER_REQUESTED = 'XMLOK 1';

    public const STATUS_DOMAIN_AUTORENEW_CHANGED = 'XMLOK 2';

    public const STATUS_DOMAIN_LOCK_CHANGED = 'XMLOK 3';

    public const STATUS_DOMAIN_TOKEN_SENT = 'XMLOK 39';

    public const STATUS_DOMAIN_UPDATED = 'XMLOK 12';

    public const STATUS_NSGROUP_ADDED = 'XMLOK 4';

    public const STATUS_NSGROUP_UPDATED = 'XMLOK 6';

    public const STATUS_NSGROUP_DELETED = 'XMLOK 5';

    public const STATUS_NSGROUP_RETRIEVED = 'XMLOK 25';

    public const STATUS_NSGROUPS_RETRIEVED = 'XMLOK 24';

    public const STATUS_IDENTITY_ADDED = 'XMLOK 7';

    public const STATUS_IDENTITY_UPDATED = 'XMLOK 8';

    public const STATUS_IDENTITY_DELETED = 'XMLOK 9';

    public const STATUS_IDENTITIES_RETRIEVED = 'XMLOK 22';

    public const STATUS_IDENTITY_RETRIEVED = 'XMLOK 23';

    public const STATUS_DOMAIN_TAKEN = 'XMLOK 10';

    public const STATUS_DOMAIN_AVAILABLE = 'XMLOK 11';

    public const STATUS_DOMAIN_RETRIEVED = 'XMLOK 16';

    public const STATUS_DOMAINS_RETRIEVED = 'XMLOK18';

    public const STATUS_DOMAIN_TRANSFER_REQUESTED = 'XMLPEN 3';

    public const STATUS_DOMAIN_TRANSFER_PENDING = 'XMLPEN 4';

    public const STATUS_DOMAIN_DELETED = 'XMLPEN 11';

    public const STATUS_DOMAIN_RESTORED = 'XMLPEN 12';

    public const STATUS_GLUES_UPDATED = 'XMLOK 142';

    public const STATUS_TLDS_RETRIEVED = 'XMLOK 46';

    public const STATUS_DNSSEC_RETRIEVED = 'XMLOK 80';

    public const STATUS_DNSSEC_ADDED = 'XMLOK 82';

    public const STATUS_DNSSEC_DELETED = 'XMLOK 81';
}
