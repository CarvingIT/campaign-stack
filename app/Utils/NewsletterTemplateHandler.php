<?php

namespace App\Utils;

use App\Models\Contact;

class NewsletterTemplateHandler
{
    public function process(string $template, Contact $contact): string
    {
        return preg_replace_callback(
            '/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\|\s*([^}]+?)\s*\}\}/',
            function ($matches) use ($contact) {
                $field = $matches[1];
                $fallback = trim($matches[2]);

                $value = $contact->{$field} ?? null;

                return ($value !== null && $value !== '')
                    ? $value
                    : $fallback;
            },
            $template
        );
    }
}