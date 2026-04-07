<?php

namespace App\Domain\Newsletter\Enums;

enum NewsletterStatus: string
{
    case DRAFT = 'draft';
    case SENDING = 'sending';
    case SENT = 'sent';
    case FAILED = 'failed';
}
