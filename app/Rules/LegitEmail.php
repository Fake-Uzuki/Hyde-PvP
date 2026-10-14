<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LegitEmail implements ValidationRule
{
    /**
     * Common disposable / throwaway / temporary email domains.
     */
    protected static array $disposableDomains = [
        'mailinator.com',
        'tempmail.com',
        'temp-mail.org',
        '10minutemail.com',
        '10minutemail.net',
        'guerrillamail.com',
        'guerrillamail.net',
        'guerrillamail.biz',
        'guerrillamail.org',
        'sharklasers.com',
        'grr.la',
        'throwawaymail.com',
        'yopmail.com',
        'yopmail.fr',
        'yopmail.net',
        'trashmail.com',
        'trashmail.net',
        'trashmail.me',
        'dispostable.com',
        'getairmail.com',
        'burnermail.io',
        'fakeinbox.com',
        'mytemp.email',
        'crazymailing.com',
        'mohmal.com',
        'generator.email',
        'emailondeck.com',
        'tempail.com',
        'inboxkitten.com',
        'minuteinbox.com',
        'tempr.email',
        'discard.email',
        'spamgourmet.com',
        'mailcatch.com',
        'dropmail.me',
        'harakirimail.com',
        'zillamail.com',
        'throwaway.email',
        'fakemailgenerator.com',
        'getnada.com',
        'nada.ltd',
        'tmpmail.net',
        'tmpmail.org',
        'maildrop.cc',
    ];

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('Please enter a valid email address.');
            return;
        }

        $parts = explode('@', $value);
        if (count($parts) !== 2) {
            $fail('The email format is invalid.');
            return;
        }

        $domain = strtolower(trim($parts[1]));

        // 1. Check disposable domain blacklist
        if (in_array($domain, static::$disposableDomains, true)) {
            $fail('Disposable, temporary, or throwaway email addresses are not permitted on HYDE PVP. Please use a permanent email provider (Gmail, Outlook, Yahoo, etc.).');
            return;
        }

        // 2. Validate DNS MX / A records if host lookup is supported
        if (function_exists('checkdnsrr')) {
            $hasMx = @checkdnsrr($domain, 'MX');
            $hasA = $hasMx ? true : @checkdnsrr($domain, 'A');

            if (! $hasMx && ! $hasA) {
                $fail("The email domain '{$domain}' does not appear to have active mail servers. Please enter a valid, deliverable email address.");
            }
        }
    }
}
