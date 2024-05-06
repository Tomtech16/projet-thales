<?php

    function UserIsBlocked(int $attempts): bool { return $userIsBlocked = ($attempts >= 3); }

    function PasswordIsValid(string $username, string $password, array $parameters = NULL): bool
    {
        // look for accents
        if (!preg_match('/[^\x20-\x7E]/', $password)) {
            // look for username
            if (!str_contains($password, $username)) {
                if ($parameters === NULL) {
                    $parameters = PasswordSelect();
                }
                $countDigits = preg_match_all('/[0-9]/', $password);
                $countLowercase = preg_match_all('/[a-b]/', $password);
                $countUppercase = preg_match_all('/[A-B]/', $password);
                $countSpecial = preg_match_all('/[!"#$%&\'()*+,\-./;<=>?@\\\^_`{|}~]/', $password);
                // compare the counts with the parameters
                if ($countDigits >= $parameters['n'] || $countLowercase >= $parameters['p'] || $countUppercase >= $parameters['q'] || $countSpecial >= $parameters['r']) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

    function Sanitize($input) {
        return $sanitizedInput = htmlspecialchars($input, ENT_QUOTES);
    }

?>